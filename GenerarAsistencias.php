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
$diasRecSet=CovGetDiasCalendario($fecha, false, false); // el primer false es para que no filtre el mes de la fecha. El segundo false es para que no incluya el $fecha en la consulta
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
    
    // Obtener el total de las asistencias ya fijadas en la fecha del proceso y restar esos modos de asistencia a los aforos del grupo
    $PrefijadosAsistidos = CovGetTotalAsistidos($fecha, $centroGrupo, $idAulaGrupo, $cursoGrupo, $horarioGrupo);
    $prefijadosOnLine = CovGetTotalOnLine($fecha, $centroGrupo, $idAulaGrupo, $cursoGrupo, $horarioGrupo);
    
    // Calcula el total de las asistencias ON LINE  que se deben asignar RESTANDO las ya asignadas al total aforo_covid del grupo
    $onLine = $grupo['online'] - $prefijadosOnLine;
    $Asisten = $grupo['asisten'] - $PrefijadosAsistidos;

    // obtener los alumnos del grupo en curso
    $alumnosArray = array(); // en este aray se va metidneo los datos del alumno que luwego se usa para grabar las asistencias
    $alumnos = CovGetAsistenciasGrupo($fecha, $centroGrupo, $idAulaGrupo, $cursoGrupo, $horarioGrupo);
    while($alumno=$alumnos->fetch(PDO::FETCH_ASSOC)){ 

        // calculo de puntos segun sus asistencias online en las fechas anteriores a la fecha solicitada
        $puntos = 0;
        $puntosDelDia = count($diasArray);
        foreach($diasArray as $dia){ // recorre todas las fecha lectivas anteriores a la fecha solicitada para ver las clases online hechas
            $puntos += covEsOnline($alumno['numero'], $dia['fecha']) ? $puntosDelDia : 0; // verifica que el dia ha sido online o no
            $puntosDelDia--; // resta uno para que que el dia siguiente sume menos puntos 
        }

        $alumnosArray[] = array ( "numero"=>$alumno['numero'], 
                                "asignado"=>$alumno['asignado'], 
                                "remoto"=>intval($alumno['remoto']), 
                                "puntos"=>$puntos );
    }

    $alumnos->closeCursor;

    // ordena el array por cantidad de asistencias online como primer orden ascendente, y por puntos descendente por el segundo orden
    $colPuntos = array_column($alumnosArray, 'puntos');
    $colRemotos = array_column($alumnosArray, 'remoto');
    $colNumeros = array_column($alumnosArray, 'numero');
    array_multisort($colRemotos, SORT_ASC, $colPuntos, SORT_DESC, $colNumeros, SORT_DESC, $alumnosArray);

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

    }
    
    CovGrabaAsistencias($alumnosArray, $fecha); // (*2)
        
}

$grupos->closeCursor();


?>




