<?php

require_once("Db.php");

$fecha = $_POST["fecha"];
$centro = $_POST["idCentro"];
$idAula = $_POST["idAula"];
$curso = $_POST["curso"];
$horario = $_POST["horario"];

$grupos = CovGetGruposAInformar($fecha, $centro, $idAula, $curso, $horario);

while($grupo=$grupos->fetch(PDO::FETCH_ASSOC)){ // (1)

    $centroGrupo = $grupo['centro'];
    $idAulaGrupo = $grupo['idAula'];
    $cursoGrupo = $grupo['curso'];
    $horarioGrupo = $grupo['horario'];
    $diasSemana = $grupo['dias'];
    
    // obtener los alumnos del grupo en curso por orden de asistencia OnlIne
    $alumnos = CovGetAsistenciasGrupo($fecha, $centroGrupo, $idAulaGrupo, $cursoGrupo, $horarioGrupo);
    $alumnosArray = $alumnos->fetchAll();

    CovEliminarAsistencias($alumnosArray, $fecha);
        
}

$grupos->closeCursor();


?>




