<?php

    require_once("Db.php");
    
    $centro=$_GET["centro"];
    $idPromocion=$_GET["idPromocion"];

    $tot=GetTotalAlumnos($centro, $idPromocion); 
    echo $tot["total"] . " alumnos";

?>