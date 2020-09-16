<?php

require_once("Db.php");

$fecha = $_POST["fecha"];
$centro = $_POST["idCentro"];
$idAula = $_POST["idAula"];
$curso = $_POST["curso"];
$horario = $_POST["horario"];


// obtener los grupos con el aforo civd y la cantidad de alumnos que deben asistir online por cada grupo
// ATENCION: Es posible que al llamar a la funcion no se reciba la totalidad de los parametros porque no se haya filtrado por ningún 
//  campo, pero para calcular los que deben asistir a clase y los que deben hace la clase online en cada grupo, estos parametros 
//  son necesarios para dicho claculo, pero esos parametros no son los recibidos en esta funcion sino que los debe recoger del 
//  grupo que se está iterando en (1)
$grupos = CovGetGruposAInformar($fecha, $centro, $idAula, $curso, $horario);

$pp = "";

while($grupo=$grupos->fetch(PDO::FETCH_ASSOC)){ // (1)

    // (1) aqui es donde se recogen los parametros para calcular las asistencias
    $centroGrupo = $grupo['centro'];
    $idAulaGrupo = $grupo['idAula'];
    $cursoGrupo = $grupo['curso'];
    $horarioGrupo = $grupo['horario'];
    $diasSemana = $grupo['dias'];
    
echo "*******Grupo a generar-> Centro:" . $centroGrupo . ", Aula:" . $idAulaGrupo . ", Curso:" . $cursoGrupo . ", Horario:" . $horarioGrupo . "\n";
    // Obtener el total de las asistencias ya fijadas en la fecha del proceso y restar esos modos de asistencia a los aforos del grupo
    $PrefijadosAsistidos = CovGetTotalAsistidos($fecha, $centroGrupo, $idAulaGrupo, $cursoGrupo, $horarioGrupo);
    $prefijadosOnLine = CovGetTotalOnLine($fecha, $centroGrupo, $idAulaGrupo, $cursoGrupo, $horarioGrupo);
echo "prefijados asistidos:" . $PrefijadosAsistidos . ", prefijados on line:" . $prefijadosOnLine . "\n";

    // Calcula el total de las asistencias ON LINE  que se deben asignar RESTANDO las ya asignadas al total aforo_covid del grupo
    $onLine = $grupo['online'] - $prefijadosOnLine;
    $Asisten = $grupo['asisten'] - $PrefijadosAsistidos;
echo "Aforo online:" . $grupo['online'] . ", a generar online:" . $onLine . ". a generar asistentes:" . $Asisten . "\n";


    // obtener los alumnos del grupo en curso por orden de asistencia OnlIne
    $alumnos = CovGetAsistenciasGrupo($fecha, $centroGrupo, $idAulaGrupo, $cursoGrupo, $horarioGrupo);
    $alumnosArray = $alumnos->fetchAll();
    
    //while($alumno=$alumnosArray->fetch(PDO::FETCH_ASSOC)){
    foreach($alumnosArray as &$alumno){

        if($alumno["asignado"]==""){

            if($onLine>0){
                $alumno["asignado"]='o';
                $onLine--;

            }else{
                $alumno["asignado"]='a';

            }

        }elseif($alumno["asignado"]=="o"){
            $alumno["asignado"]='po';
        
        }else{
            $alumno["asignado"]='pa';
        }
echo "nºalum:" . $alumno[numero] . ", Asignado:" . $alumno["asignado"] . "\n";

    }

    unset($alumno);

    CovGrabaAsistencias($alumnosArray, $fecha);
        
}


?>




