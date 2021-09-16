<?php

$fecha = $_POST['fecha'];
$idMatricula = $_POST['idMatricula'];
// $modoAsistencia = $_POST['modoAsistencia'];

require_once("Db.php");

$html = "";

$asistencia = GetAsistencia($fecha, $idMatricula);

// Si NO se ha establecido la asistencia real por el profe, entonces se puede establecer la asistencia prevista
if(!$asistencia['modoAsistenciaReal']){

    // si existe una asistencia ya grabada de este alumno en esta fecha
    if($asistencia){

        // Había grabada una asitencia en modo PRESENCIAL
        if($asistencia['modoAsistencia']=='a'){

            // Si existia un estado del alumno como asistencial ("a") lo cambia a OnLine ("o")
            $r = UpdateAsistencia($fecha, $idMatricula, "o", 1);

            // Si existe la fecha y hora de comunicacion al alumno, pone el icono con sobre VERDE, si no, sobre AMARILLO
            // if(is_null($asistencia["fechaHoraComunicacion"])){
                $html = '<img id="i' . $idMatricula . '-' . $fecha . '" src="./img/preRemote.png" class="img-responsive"/>';
            // }else{
                // $html = '<img id="i' . $idMatricula . '-' . $fecha . '" src="./img/preRemote_enviado.png" class="img-responsive"/>';
            // }

        // Habia grabada una asistencia en modo ONLINE    
        }elseif($asistencia['modoAsistencia']=='o'){

            // si pasa por aqui es que estaba como OnLine, por lo que el siguiente estado es AUSENTE
            $r = UpdateAsistencia($fecha, $idMatricula, "n", 1);
            $html = '<img id="i' . $idMatricula . '-' . $fecha . '" src="./img/NoAsiste.png" class="img-responsive"/>';

        // Había grabada una AUSENCIA
        }else{

            // si pasa por aqui es que estaba grabada como AUSENCIA, por lo que el siguiente estado, es que el registro no debe de existir,
            //   excepto si existe la asistencia REAL marcada por el teacher, en ese caso el siguiente estado es PRESENCIAL

            $r = DeleteAsistencia($fecha, $idMatricula);
            $html = '<img id="i' . $idMatricula . '-' . $fecha . '" src="" class="img-responsive"/>';

        }

        
    }else{

        // Si pasa por aqui es que no existía ningun estado, por lo que graba el estado Asistencial ("a")
        $r = PutAsistencia($fecha, $idMatricula, "a", 1); // 1=true para asignacionManual
        // if(is_null($asistencia["fechaHoraComunicacion"])){
            $html = '<img id="i' . $idMatricula . '-' . $fecha . '" src="./img/preAsist.png" class="img-responsive"/>';
        // }else{
        //     $html = '<img id="i' . $idMatricula . '-' . $fecha . '" src="./img/preAsist_enviado.png" class="img-responsive"/>';
        // }

    }

}


echo $html;



?>