<?php

    require_once("Db.php");
    $centro=$_GET["centro"];
    $tot=CovGetTotalAlumnos($centro); 
    echo $tot["total"] . " alumnos";

?>