<?php

$numero = $_POST["numero"];
$nombre = $_POST["nombre"];
$apellidos = $_POST["apellidos"];
$centro = $_POST["centro"];
$idAula = $_POST["aula"];
$curso = $_POST["curso"];
$horario = $_POST["horario"];
$dias = $_POST["dias"];
$email = $_POST["email"];
$email2 = $_POST["email2"];
$url = $_POST["url"];

require_once("Db.php");

$res=CovGetAlumno($numero);

// si el alumno ya existía, lo modifica
if($alum=$res->fetch(PDO::FETCH_ASSOC)){

    try {
        $r = CovModificacionAlumno($numero, $nombre, $apellidos, $centro, $idAula, $curso, $horario, $dias, $email, $email2, $url);
        echo "El alumno se ha modificado correctamente";
    } catch (\Throwable $th) {
        echo 'ha ocurrido un error al modificar el alumno.';
    }



}else{ // si NO existía el alumno, lo graba nuevo

    try {
        $r = CovGrabacionAlumno($numero, $nombre, $apellidos, $centro, $idAula, $curso, $horario, $dias, $email, $email2, $url);
        echo "El alumno se ha grabado correctamente";
    } catch (\Throwable $th) {
        echo 'ha ocurrido un error al crear el alumno';
    }


};




?>

<a href="index.php">VOLVER AL PANEL</a>