<?php
    
    require_once("Db.php");

    $numeroAlumno = $_GET["numeroAlumno"];

    $result = GetAlumno($numeroAlumno);

    $alumno = $result->fetch(PDO::FETCH_ASSOC);

    $json = json_encode($alumno);

    echo $json;


?>