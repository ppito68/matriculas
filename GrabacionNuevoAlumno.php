<?php

$idPromocion = $_POST["idPromocion"];
$idAlumno = $_POST["idAlumno"];
$numero = $_POST["numero"];
$nombre = $_POST["nombre"];
$apellidos = $_POST["apellidos"];
$codigoPostal = $_POST["codPostal"];
$domicilio = $_POST["domicilio"];
$email = $_POST["email"];
$email2 = $_POST["email2"];
$fechaNacimiento = $_POST["fechaNacimiento"];
$municipio = $_POST["municipio"];
$nif = $_POST["nif"];
$observaciones = $_POST["observaciones"];

$idCentro = $_POST["centro"];
$idAula = $_POST["aula"];
$idProfesor = $_POST["profesor"];

// $curso = $_POST["curso"];
$idCurso = $_POST["curso"];

//$horario = $_POST["horario"];
$idHorario = $_POST["horario"];


$dias = $_POST["dias"];
$url = $_POST["url"];
$seEnviaCorreo = $_POST["seEnviaCorreo"];

$seEnviaCorreo = $seEnviaCorreo ? 1 : 0;

require_once("Db.php");

$res = GetAlumno($numero);

// *****  GRABACION DE DATOS DEL ALUMNO 

// si el alumno ya existía, lo modifica
if($alum=$res->fetch(PDO::FETCH_ASSOC)){

    try {
        $r = ModificacionAlumno($idAlumno, $nombre, $apellidos, $codigoPostal, $domicilio, $email, $email2, $fechaNacimiento, $municipio, $nif, $observaciones);
        echo "El alumno se ha modificado correctamente";
    } catch (\Throwable $th) {
        echo 'ha ocurrido un error al modificar el alumno.';
    }

}else{ // si NO existía el alumno, lo graba nuevo

    try {
        $r = GrabacionAlumno($numero, $nombre, $apellidos, $codigoPostal, $domicilio, $email, $email2, $fechaNacimiento, $municipio, $nif, $observaciones);

        // lee el alumno recien grabado para obtener la id y pasarsela a la grabacion de la matricula
        $resGetAl=GetAlumno($numero); 
        if($al=$resGetAl->fetch(PDO::FETCH_ASSOC)){
            $idAlumno = $al["id"];
            echo "El alumno se ha grabado correctamente";
        }
    
    } catch (\Throwable $th) {
        echo 'ha ocurrido un error al crear el alumno: Error -> ' . $th->getMessage();
    }

};


$resMat=GetMatricula($idAlumno, $idPromocion, true);

// *****  GRABACION DE DATOS DE LA MATRICULA 

// si la matricula ya existía, la modifica
if($mat=$resMat->fetch(PDO::FETCH_ASSOC)){

    try {
        $r = ModificacionMatricula($idAlumno, $idPromocion, $seEnviaCorreo, $idCurso, $dias, $email, $email2, $idHorario, $idAula, $idCentro, $idProfesor, $url);
        echo "    La matrícula se ha modificado correctamente";
    } catch (\Throwable $th) {
        echo 'ha ocurrido un error al modificar el alumno.' .  $th->getMessage();;
    }

}else{ // si NO existía el alumno, lo graba nuevo

    try {
        $r = GrabacionMatricula($idAlumno, $idPromocion, $seEnviaCorreo, $idCurso, $dias, $email, $email2, $idHorario, $idAula, $idCentro, $idProfesor, $url);
        echo "La matrícula se ha grabado correctamente";
    } catch (\Throwable $th) {
        echo 'ha ocurrido un error al crear el alumno: Error -> ' . $th->getMessage();
    }

};



?>

<a href="index.php">VOLVER AL PANEL</a>