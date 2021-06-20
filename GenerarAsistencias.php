<?php

require_once("Db.php");

$fecha = $_POST["fecha"];
$centro = $_POST["idCentro"];
$idAula = $_POST["idAula"];
$curso = $_POST["curso"];
$horario = $_POST["horario"];

// Esta variable se creo para que recibiera la hora de toque de queda en época de covid. Si un grupo tenia un horario que abarcara la hora de esta variable, el objetivo era
//  asignarle a todos los alumnos de ese grupo la asistencia en modo ONLINE. No se llegó a usar, por lo que le asigno a esta varible un null para que no proceda en el proceso 
//  de asignacion de asistencia.
$horaToqueQueda = NULL; //$_POST["horaToqueQueda"];

// funcion para ordenar array de alumnos por los puntos asignados en el sistema de asignacion de asist a alumnos con iguales
//  tipos de asistencias.
function cmp($a, $b){
    // if($a['puntos'] == $b['puntos']){
    //     return 0;
    // }
    // return ($a['puntos'] < $b['puntos']) ? -1 : 1;

    return $a['puntos'] - $b['puntos'];
}

// Devuelve true ó false si el Grupo tiene un horario donde termina despues de la hora recibida como parametro
function GrupoEnHorarioComprometido($grupo, $hora){
    list($horaBegin, $horaEnd) = explode("-", $grupo['horario']);
    return ((float) $horaEnd) > ((float) $hora); // devuelve true si el horario de final de clase es mayor a la hora recibida
}

// obtiene los dias comprometidos del calendario escolar desde el inicio de curso hasta el dia de la fecha para usarlo en (*1) para
// conseguir el orden correcto en el que deben estar los alumnos para asignarle sus asistencias correspondientes
$diasRecSet=CovGetDiasCalendario($fecha, false, false); // el primer false es para que no filtre el mes de la fecha. El segundo false es para que no incluya el parámetro $fecha en la consulta
$diasArray = $diasRecSet->fetchAll(); // se pasa a un array porque hay que recorrelo varias veces


// obtener los grupos con el aforo civd y la cantidad de alumnos que deben asistir online por cada grupo
// ATENCION: Es posible que al llamar a la funcion no se reciba la totalidad de los parametros porque no se haya filtrado por ningún 
//  campo, pero para calcular los que deben asistir a clase y los que deben hace la clase online en cada grupo, estos parametros 
//  son necesarios para dicho claculo, pero esos parametros no son los recibidos en esta funcion sino que los debe recoger del 
//  grupo que se está iterando en (*3)
$grupos = CovGetGruposAInformar($fecha, $centro, $idAula, $curso, $horario);
while($grupo=$grupos->fetch(PDO::FETCH_ASSOC)){ 

    // (*3) aqui es donde se recogen los parametros para calcular las asistencias
    $centroGrupo = $grupo['centro'];
    $idAulaGrupo = $grupo['idAula'];
    $cursoGrupo = $grupo['curso'];
    $horarioGrupo = $grupo['horario'];
    $diasSemana = $grupo['dias'];
    
    // Obtener el total de las asistencias ya prefijadas en la fecha del proceso y restar esos modos de asist a los 
    //  aforos del grupo
    $PrefijadosAsistidos = CovGetTotalAsistidos($fecha, $centroGrupo, $idAulaGrupo, $cursoGrupo, $horarioGrupo);
    $prefijadosOnLine = CovGetTotalOnLine($fecha, $centroGrupo, $idAulaGrupo, $cursoGrupo, $horarioGrupo);
    $prefijadosAusentes = CovGetTotalAusencias($fecha, $centroGrupo, $idAulaGrupo, $cursoGrupo, $horarioGrupo);

    // Calcula el total de las asistencias ON LINE  que se deben asignar RESTANDO las ya preasignadas al total aforo_covid 
    //  del grupo. Resta tambien los ausentes pues dejan plaza libre tambien para presenciales.
    $onLine = $grupo['online'] - $prefijadosOnLine - $prefijadosAusentes;

    // esto no se usa, pero lo dejo para posibles calculos en el futuro
    $asisten = $grupo['asisten'] - $PrefijadosAsistidos; 

    // obtener los alumnos del grupo en curso
    $alumnosArray = array(); // en este aray se va introduciendo los datos del alumno que luwego se usa para grabar las asistencias
    $alumnos = CovGetAsistenciasGrupo($fecha, $centroGrupo, $idAulaGrupo, $cursoGrupo, $horarioGrupo);
    while($alumno=$alumnos->fetch(PDO::FETCH_ASSOC)){ 


        /* Cálculo del orden de asignacion del modo de asist ONLINE: 

         1º El primer criterio asigna ONLINE preferentemente a los alumnos cuya ultima clase tenia asignado el modo de 
                asist PRESENCIAL, NO acudieron a clase y NO avisaron con anterioridad para poder llamar a otro alumno que 
                tenia asist ONLINE y aprovechara la clase presencial. A estos alumnos se les asigna ONLINE preferentemente 
                en su proxima clase.

         2º El segundo orden asigna ONLINE preferentemente a los alumno que en su ultima clase tenia asignada la asist de 
                forma ONLINE y acudió a la clase para ser presencial       

         3º El tercer criterio asigna OL preferentemenete a los alumnos que MENOS DIAS OL TIENEN DE FORMA CONTINUADA en 
                los ultimos dias. Orden Ascendente.

         4º Asigna OL preferentemente a los alumnos que MAS dias presenciales continuados ha tenido en los ultimos dias. 
                Orden Descendente

         5º El tercer orden que se establece es la cantidad de asist ONLINE que ya haya realizado cada alumno.
              Este orden es Ascendente, es decir, los alumnos que MENOS asistencias ONLINE hayan hecho serán los primeros en 
              asignarle la asist ONLINE. Si en este orden existen empates, se establece el siguiente orden.

         6º Este orden se establece si aun aplicando los ordenes anteriores, continua habiendo empates. Por desempatar de alguna
                manera, este orden establece el numero de alumno, y será Descendente para dar una falsa prioridad de antigüedad.  
        */  

        

        // Inicializa la variable que guarda para cada alumno los puntos por día asistido OL de forma continuada en los dias inmediatamente anteriores al día de la asignación                
        $puntosOnlineContinuos = 0;

        // Inicializa la variable que guarda para cada alumno los puntos por día asistido PRESENCIAL de forma continuada en los dias inmediatamente anteriores al día de la asignación         
        $puntosPresencialesContinuos = 0;

        // recorre todas las fecha lectivas anteriores a la fecha de asignacion para obtener puntos por asistencias continuadas OL y Presenc.
        foreach($diasArray as $dia){ 

            $asist = CovGetAsistencia( $dia['fecha'], $alumno['numero'] ); // obtiene la asistencia del alumno y día de la fecha en proceso
            $puntosContinuidad = puntosPorContinuidad($asist); // Obtencion array con puntos para Criterios 3 y 4

            $puntosOnlineContinuos = ($puntosContinuidad["online"] > 0) ? ($puntosOnlineContinuos + $puntosContinuidad["online"]) : 0; 
            $puntosPresencialesContinuos = ( $puntosContinuidad["presenciales"] > 0 ) ? ( $puntosPresencialesContinuos + $puntosContinuidad["presenciales"] ) : 0; 

        }

        // 1º criterio
        $seAusentoDiaAnteriorSinAvisar = SeAusentoDiaAnteriorSinAvisar($alumno['numero'], $fecha);

        // 2º criterio
        $vinoAClaseCuandoEraOnline = VinoAClaseCuandoEraOnline($alumno['numero'], $fecha);

        // Añade el alumno al array 
        $alumnosArray[] = array (   "numero"=>$alumno['numero'], 
                                    "asignado"=>$alumno['asignado'], 
                                    "remoto"=>intval($alumno['remoto']), 
                                    "seAusentoDiaAnteriorSinAvisar" => $seAusentoDiaAnteriorSinAvisar, 
                                    "vinoAClaseCuandoEraOnline" => $vinoAClaseCuandoEraOnline,
                                    "onLineContinuos" => $puntosOnlineContinuos,
                                    "presencialesContinuos" => $puntosPresencialesContinuos
                                );
    }

    $alumnos->closeCursor;

    // ordena el array por Ausencias sin avisar como primer orden descendente, cantidad de asistencias online como 
    //  segundo orden ascendente, y por puntos descendente por el tercer orden
    $colAusentadoDiaAnteriorSinAvisar = array_column($alumnosArray, 'seAusentoDiaAnteriorSinAvisar');
    $colVinoAClaseCuandoEraOnline = array_column($alumnosArray, 'vinoAClaseCuandoEraOnline');
    $colOnLineContinuos = array_column($alumnosArray, 'onLineContinuos');
    $colPresencialesContinuados = array_column($alumnosArray, 'presencialesContinuos');
    $colRemotos = array_column($alumnosArray, 'remoto');
    $colNumeros = array_column($alumnosArray, 'numero');
    array_multisort($colAusentadoDiaAnteriorSinAvisar, SORT_DESC,
                    $colVinoAClaseCuandoEraOnline, SORT_DESC,
                    $colOnLineContinuos, SORT_ASC,
                    $colPresencialesContinuados, SORT_DESC,
                    $colRemotos, SORT_ASC, 
                    $colNumeros, SORT_DESC, $alumnosArray);

    // Bucle que asigna la asist correspondiente a cada alumno
    foreach($alumnosArray as &$alumno){ // se pone & para poder modificar el elemento $alumno

        // Si hay horario de toque de queda le asigna ONLINE a todos los alumnos cuyas clases se impartan dentro del horario de toque de queda.
        // La variable "$horaToqueQueda" está declarada al principio de este modulo con valor NULL, ya que no se llegó a utlizar esta opción, pero se deja por si en un futuro...
        if(!is_null($horaToqueQueda)){

            // si el grupo tiene un horario el cual abarca la hora de estado de queda, le asigna a todo el grupo la asistencia online
            if(GrupoEnHorarioComprometido($grupo, $horaToqueQueda)){

                $alumno["asignado"]='o';

            }

        }else{

            // Si no tiene PREAsignado la asist, le asigna "o" ó "a" según sea el modo de asist
            if($alumno["asignado"]==""){

                // La variable $onLine guarda las veces online que quedan por asignar; cuando es 0 ya ha asignado todos los online,
                //  los demas le asigna PRESENCIAL
                if($onLine>0){
                    $alumno["asignado"]='o';
                    $onLine--; // le quita 1 a las veces que faltan por asignar online

                }else{
                    $alumno["asignado"]='a';

                }
            
            // Si ya tenia PREAsignada la asitencia, le cambia el valor a "PRE" por ponerle un valor distinto a "a" y "o"
            //   ya que el proceso de grabacion de la asist, solo graba las asist "a" ú "o" en la llamada a la funcion
            //  CovGrabaAsistencias(...) que se hace en (*2)
            }else{
                $alumno["asignado"]='PRE';

            }
               
        }

         // LINEA DE DEPURACION ->        
                // echo 'nº' . $alumno['numero'] . 
                //     ' - Faltó y era Presenc.:' . $alumno['seAusentoDiaAnteriorSinAvisar'] . 
                //     ' - Acudió y era OL?:' . $alumno['vinoAClaseCuandoEraOnline'] . 
                //     ' - OL cont.:' . $alumno["onLineContinuos"] . 
                //     ' - Presenc cont:' . $alumno["presencialesContinuos"] . 
                //     ' - veces OL:' . $alumno['remoto'] . "\n";

    }
    
    CovGrabaAsistencias($alumnosArray, $fecha); // (*2)
        
}

$grupos->closeCursor();




// Devuelve array con dos variables numericas, una para el valor de OnLine y otra para el valor de asistencia presencial.
// - La variable para Online devuelve 1 si la asistencia REAL fue Online (o si la asistencia real no está establecida pero estaba prevista Online). Devuelve 0,5 si se ausentó pero la 
//      asistencia prevista era Online ó estaba previsto que faltara
// - La variable para Presencial devuelve 1 si la asistencia Real fue Presencial ó si la asistencia real no está establecida pero estaba previsto presencial
function puntosPorContinuidad($asistencia){

    $puntosOnline = 0;
    $puntosPresenc = 0;

      //Si NO esta establecida la asistecia REAL, obtiene la Prevista 
    if(!$asistencia['modoAsistenciaReal']){
        if($asistencia['modoAsistencia'] === 'o'){
            $puntosOnline = 1;
        }elseif($asistencia['modoAsistencia'] === 'a'){
            $puntosPresenc = 1;
        }
    // Si esta establecida la asist REAL                    
    }else{
        if($asistencia['modoAsistenciaReal'] === 'o'){
            $puntosOnline = 1;
        }elseif($asistencia['modoAsistenciaReal'] === 'n' && 
                        ( $asistencia['modoAsistencia'] === 'o' || $asistencia['modoAsistencia'] === 'n' )
                ){
            $puntosOnline = 0.5;
        }elseif($asistencia['modoAsistenciaReal'] === 'a'){
            $puntosPresenc = 1;
        }
    }

    return [ "online" => $puntosOnline, "presenciales" => $puntosPresenc];
}


?>