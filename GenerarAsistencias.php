<?php

require_once("Db.php");

$fecha = $_POST["fecha"];
$centro = $_POST["idCentro"];
$idAula = $_POST["idAula"];
$curso = $_POST["curso"];
$horario = $_POST["horario"];

// funcion para ordenar array de alumnos por los puntos asignados en el sistema de asignacion de asist a alumnos con iguales
//  tipos de asistencias.
function cmp($a, $b){
    // if($a['puntos'] == $b['puntos']){
    //     return 0;
    // }
    // return ($a['puntos'] < $b['puntos']) ? -1 : 1;

    return $a['puntos'] - $b['puntos'];
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

         6º El cuarto orden tiene en cuenta los dias transcurridos desde todas las veces ONLINE que ha hecho el alumno, asignando
                a cada alumno una puntuación dependiendo de la cantidad de dichos días transcurridos. La suma de esos días son asignados 
                al alumno, por lo que este orden es Descendente, asignando preferentemente OL a los alumnos que mas días transcurridos
                tiene acumulado. 

         7º Este orden se establece si aun aplicando los ordenes anteriores, continua habiendo empates. Por desempatar de alguna
                manera, este orden establece el numero de alumno, y será Descendente para dar una falsa prioridad de antigüedad.  */  

        // Inicializa la variable que guarda para cada alumno los puntos por día asistido OL de forma continuada en los dias 
        //  inmediatamente anteriores al día de la asignación                
        $puntosOnlineContinuos = 0;

        //
        $puntosPresencialesContinuos = 0;

        // variable que acumula para un alumno la cantidad  de dias transcurridos desde todos los dias que ha asistido ONLINE 
        //   hasta el dia de la asignacion.
        $puntos = 0; 
        
        // variable que guarda los puntos iniciales a asignar y que va disminuyendo en 1 por cada iteracion de asignacion de puntos
        $puntosDelDia = count($diasArray); 
        

        // bucle que recoge informacion para los criterios 3º y 5º de orden.
        // recorre todas las fecha lectivas anteriores a la fecha de asignacion para acumular tipos de asistencias
        foreach($diasArray as $dia){ 
            
            // Variable para control criterio 3
            // Suma a esta variable 1 punto si el $dia hizo OL (o tenia previsto OL y no tenia la asist confirmada aun)
            //  Suma a esta variable 0.5 punto si la asist PREVISTA era OL pero NO asistió a la cita
            //  Para las demás premisas, no suma nada.
            $puntosOnline = 0; // = getPuntosPorOnLine($alumno['numero'], $dia['fecha']);

            // Variable para control criterio 4
            // Suma a esta variable 1 punto si el $dia hizo PRESENCIAL (o tenia previsto PRESENCIAL y no tenia la asist confirmada aun)
            //  Para las demás premisas, no suma nada.
            $puntosPresenciales = 0;

            $asist = CovGetAsistencia( $dia['fecha'], $alumno['numero'] );

            // Criterios 3 y 4
            if($asist){
                //Si NO esta establecida la asistecia REAL, obtiene la Prevista 
                if(!$asist['modoAsistenciaReal']){
                    if($asist['modoAsistencia'] === 'o'){
                        $puntosOnline = 1;
                    }elseif($asist['modoAsistencia'] === 'a'){
                        $puntosPresenciales = 1;
                    }
                // Si esta establecida la asist REAL                    
                }else{
                    if($asist['modoAsistenciaReal'] === 'o'){
                        $puntosOnline = 1;
                    }elseif($asist['modoAsistenciaReal'] === 'n' && 
                                    ( $asist['modoAsistencia'] === 'o' || $asist['modoAsistencia'] === 'n' )
                            ){
                        $puntosOnline = 0.5;
                    }elseif($asist['modoAsistenciaReal'] === 'a'){
                        $puntosPresenciales = 1;
                    }
                }
            }

            // añade 2, 1 o 0 puntos a la variable que acumula los puntos por OL continuados
            $puntosOnlineContinuos = ($puntosOnline > 0) ? ($puntosOnlineContinuos + $puntosOnline) : 0; 

            //
            $puntosPresencialesContinuos = ( $puntosPresenciales > 0 ) ? ( $puntosPresencialesContinuos + $puntosPresenciales ) : 0; 


            // Suma los puntos para el criterio 5º si el dia hizo OL
            // Con saber que los puntos obtenidos por OL continuados en la variable "$puntosOnLine" sea > 1, ya se sabe que ha 
            //  sido OL para obtener estos puntos, asi evito preguntar por los modos de asistencias que son varios.
            $puntos += ( $puntosOnline > 1 ) ? $puntosDelDia : 0; 
            $puntosDelDia--; // resta uno a los puntos a asignar para el siguiente 
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
                                    "presencialesContinuos" => $puntosPresencialesContinuos,
                                    "puntos"=>$puntos
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
    $colPuntos = array_column($alumnosArray, 'puntos');
    $colNumeros = array_column($alumnosArray, 'numero');
    array_multisort($colAusentadoDiaAnteriorSinAvisar, SORT_DESC,
                    $colVinoAClaseCuandoEraOnline, SORT_DESC,
                    $colOnLineContinuos, SORT_ASC,
                    $colPresencialesContinuados, SORT_DESC,
                    $colRemotos, SORT_ASC, 
                    $colPuntos, SORT_DESC, 
                    $colNumeros, SORT_DESC, $alumnosArray);

    // Bucle que asigna la asist correspondiente a cada alumno
    foreach($alumnosArray as &$alumno){ // se pone & para poder modificar el elemento $alumno

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
            // LINEA DE DEPURACION ->        
            echo 'nº' . $alumno['numero'] . 
                ' - Faltó y era Presenc.:' . $alumno['seAusentoDiaAnteriorSinAvisar'] . 
                ' - Acudió y era OL?:' . $alumno['vinoAClaseCuandoEraOnline'] . 
                ' - OL continuados:' . $alumno["onLineContinuos"] . 
                ' - Presenc cont:' . $alumno["presencialesContinuos"] . 
                ' - veces OL:' . $alumno['remoto'] . 
                ' - Puntos dias transc.desde OL:' . $alumno['puntos'] . "\n";

    }
    
    CovGrabaAsistencias($alumnosArray, $fecha); // (*2)
        
}

$grupos->closeCursor();


?>