<?php

require_once("Db.php");

$fecha = $_POST["fecha"];
$centro = $_POST["idCentro"];
$idAula = $_POST["idAula"];
$curso = $_POST["curso"];
$horario = $_POST["horario"];

// funcion para ordenar array de alumnos por los puntos asignados en el sistema de asignacion de asistencia a alumnos con iguales
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
    
    // Obtener el total de las asistencias ya prefijadas en la fecha del proceso y restar esos modos de asistencia a los 
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

        /* Cálculo del orden de asignacion del modo de asistencia ONLINE: 

         1º El primer criterio asigna ONLINE preferentemente a los alumnos cuya ultima clase tenia asignado el modo de 
                asistencia PRESENCIAL, NO acudieron a clase y NO avisaron con anterioridad para poder llamar a otro alumno que 
                tenia asistencia ONLINE y aprovechara la clase presencial. A estos alumnos se les asigna ONLINE preferentemente 
                en su proxima clase.

         2º El segundo orden asigna ONLINE preferentemente a los alumno que en su ultima clase tenia asignada la asistencia de 
                forma ONLINE y acudió a la clase para ser presencial       

         3º El tercer criterio es ordenar a los alumnos segun el modo de SU ULTIMA CLASE, asignando preferentemente ONLINE a los 
                alumnos cuyo ultimo modo haya sido presencial.

         4º El tercer orden que se establece es la cantidad de asistencia ONLINE que ya haya realizado cada alumno.
              Este orden es Ascendente, es decir, los alumnos que MENOS asistencias ONLINE hayan hecho serán los primeros en 
              asignarle la asistencia ONLINE. Si en este orden existen empates, se establece el siguiente orden.

         5º El cuarto orden tiene en cuenta los dias transcurridos desde sus ultimas veces ONLINE, asignando a cada alumno, de 
                mayor a menor,  una puntuación dependiendo de la cantidad de dichos días transcurridos. Cuantos más días 
                transcurridos, mas puntos le asigna al alumno, por lo que este orden es Descendente.

         6º Este orden se establece si aun aplicando los ordenes anteriores, continua habiendo empates. Por desempatar de alguna
                manera, este orden establece el numero de alumno, y será Descendente para dar una falsa prioridad de antigüedad.  */  
        $puntos = 0;
        
        // variable que guarda los puntos iniciales a asignar y que va disminuyendo en 1 por cada iteracion de asignacion de puntos
        $puntosDelDia = count($diasArray); 
        
        // En el último ciclo del bucle siguiente donde se asigna los puntos, quedara en esta variable el modo de asistencia del 
        //  ultimo día para comprobar si fue ONLINE y poder ordenar por el Xº criterio de orden.
        $ultimoDiaFueOnLine = ''; 

        // bucle de asignacion de puntos para el 2º criterio de orden
        foreach($diasArray as $dia){ // recorre todas las fecha lectivas anteriores a la fecha solicitada para ver las clases online hechas
            $ultimoDiaFueOnLine = covEsOnline($alumno['numero'], $dia['fecha']);
            $puntos += ($ultimoDiaFueOnLine) ? $puntosDelDia : 0; // verifica que el dia ha sido online o no
            $puntosDelDia--; // resta uno para que que el dia siguiente sume menos puntos 
        }

        // 1º criterio
        $seAusentoDiaAnteriorSinAvisar = SeAusentoDiaAnteriorSinAvisar($alumno['numero'], $fecha);

        // 2º criterio
        $vinoAClaseCuandoEraOnline = VinoAClaseCuandoEraOnline($alumno['numero'], $fecha);

        $alumnosArray[] = array (   "numero"=>$alumno['numero'], 
                                    "asignado"=>$alumno['asignado'], 
                                    "remoto"=>intval($alumno['remoto']), 
                                    "puntos"=>$puntos,
                                    "seAusentoDiaAnteriorSinAvisar" => $seAusentoDiaAnteriorSinAvisar, 
                                    "vinoAClaseCuandoEraOnline" => $vinoAClaseCuandoEraOnline,
                                    "ultimoDiaFueOnLine" => $ultimoDiaFueOnLine
                                );
    }

    $alumnos->closeCursor;

    // ordena el array por Ausencias sin avisar como primer orden descendente, cantidad de asistencias online como 
    //  segundo orden ascendente, y por puntos descendente por el tercer orden
    $colAusentadoDiaAnteriorSinAvisar = array_column($alumnosArray, 'seAusentoDiaAnteriorSinAvisar');
    $colVinoAClaseCuandoEraOnline = array_column($alumnosArray, 'vinoAClaseCuandoEraOnline');
    $colModoAsistenciaUltimaClase = array_column($alumnosArray, 'ultimoDiaFueOnLine');
    $colRemotos = array_column($alumnosArray, 'remoto');
    $colPuntos = array_column($alumnosArray, 'puntos');
    $colNumeros = array_column($alumnosArray, 'numero');
    array_multisort($colAusentadoDiaAnteriorSinAvisar, SORT_DESC,
                    $colVinoAClaseCuandoEraOnline, SORT_DESC,
                    $colModoAsistenciaUltimaClase, SORT_ASC,
                    $colRemotos, SORT_ASC, 
                    $colPuntos, SORT_DESC, 
                    $colNumeros, SORT_DESC, $alumnosArray);

    // Bucle que asigna la asistencia correspondiente a cada alumno
    foreach($alumnosArray as &$alumno){ // se pone & para poder modificar el elemento $alumno

        // Si no tiene PREAsignado la asistencia, le asigna "o" ó "a" según sea el modo de asistencia
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
        //   ya que el proceso de grabacion de la asistencia, solo graba las asistencia "a" ú "o" en la llamada a la funcion
        //  CovGrabaAsistencias(...) que se hace en (*2)
        }else{
            $alumno["asignado"]='PRE';

        }
// LINEA DE DEPURACION ->        
// echo 'nº' . $alumno['numero'] . 
//     ' - Se ausento la ultima clase?:' . $alumno['seAusentoDiaAnteriorSinAvisar'] . 
//     ' - Vino a clase cuando era OL?:' . $alumno['vinoAClaseCuandoEraOnline'] . 
//     ' - veces Online:' . $alumno['remoto'] . 
//     ' - ultima clase ONLINE??:' . $alumno["ultimoDiaFueOnLine"] . 
//     ' - puntos:' . $alumno['puntos'] . "\n";

    }
    
    CovGrabaAsistencias($alumnosArray, $fecha); // (*2)
        
}

$grupos->closeCursor();


?>




