<?php

require_once("Db.php");

$idMatricula = $_POST["idMatricula"];

try {

    $hoy = date('Y-m-d');

    BajaMatricula( $idMatricula, $hoy );

} catch (\Throwable $th) {

    echo "La matrícula no se ha eliminado";
}
        


?>