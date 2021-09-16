<?php

require_once("Db.php");

$fecha = $_POST["fecha"];
$centro = $_POST["idCentro"];
$idAula = $_POST["idAula"];
$curso = $_POST["curso"];
$horario = $_POST["horario"];
$idPromocion = $_POST["idPromocion"];

$grupos = GetGruposAInformar($fecha, $centro, $idAula, $curso, $horario, $idPromocion);

while($grupo=$grupos->fetch(PDO::FETCH_ASSOC)){ // (1)

    $centroGrupo = $grupo['centro'];
    $idAulaGrupo = $grupo['idAula'];
    $cursoGrupo = $grupo['curso'];
    $horarioGrupo = $grupo['horario'];
    $diasSemana = $grupo['dias'];
    
    // obtener los alumnos del grupo en curso por orden de asistencia OnlIne
    $alumnos = GetAsistenciasGrupo($fecha, $centroGrupo, $idAulaGrupo, $cursoGrupo, $horarioGrupo, $idPromocion);
    $alumnosArray = $alumnos->fetchAll();

    EliminarAsistencias($alumnosArray, $fecha);
        
}

$grupos->closeCursor();


?>




