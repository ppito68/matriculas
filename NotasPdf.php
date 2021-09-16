<?php

require_once('Utiles.php');

$idMatricula = $_GET['idMatricula'];

if ($_SERVER['REQUEST_METHOD'] == 'GET'){

    GenerarNotasPdf($idMatricula, true);
    header("HTTP/1.1 200 OK");

}



?>









