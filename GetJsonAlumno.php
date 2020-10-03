<?php
    
    require_once("Db.php");

    $numero=$_GET["numero"];

    $result = CovGetAlumno($numero);

    $alumno = $result->fetch(PDO::FETCH_ASSOC);
    $json = json_encode($alumno);
    echo $json;


?>