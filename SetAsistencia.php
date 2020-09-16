<?php

$fecha = $_POST['fecha'];
$numeroAlumno = $_POST['numeroAlumno'];
// $modoAsistencia = $_POST['modoAsistencia'];

require_once("Db.php");

$html = "";

$asistenciaAnterior = CovGetAsistencia($fecha, $numeroAlumno);

// si existe una asistencia ya grabada de este alumno en esta fecha
if($asistenciaAnterior){

    if($asistenciaAnterior['modoAsistencia']=='a'){

        // Si existia un estado del alumno como asistencial ("a") lo cambia a OnLine ("o")
        $r = CovUpdateAsistencia($fecha, $numeroAlumno, "o");
        if(is_null($asistenciaAnterior["fechaHoraComunicacion"])){
            $html = '<img id="i' . $numeroAlumno . '-' . $fecha . '" src="./img/remote.png" class="img-responsive"/>';
        }else{
            $html = '<img id="i' . $numeroAlumno . '-' . $fecha . '" src="./img/remote_enviado.png" class="img-responsive"/>';
        }

    }else{

        // si pasa por aqui es que estaba como OnLine, por lo que el siguiente estado es que el 
        //  registro no debe de existir.
        $r = CovDeleteAsistencia($fecha, $numeroAlumno);
        
        // $html = '<img id="i' . $numeroAlumno . '-' . $fecha . '" src="./img/blanco.png" class="img-responsive"/>';
        $html = '<img id="i' . $numeroAlumno . '-' . $fecha . '" src="" class="img-responsive"/>';
        
    }

    
}else{

    // Si pasa por aqui es que no existía ningun estado, por lo que graba el estado Asistencial ("a")
    $r = CovPutAsistencia($fecha, $numeroAlumno, "a");
    if(is_null($asistenciaAnterior["fechaHoraComunicacion"])){
        $html = '<img id="i' . $numeroAlumno . '-' . $fecha . '" src="./img/asist.png" class="img-responsive"/>';
    }else{
        $html = '<img id="i' . $numeroAlumno . '-' . $fecha . '" src="./img/asist_enviado.png" class="img-responsive"/>';
    }

}

echo $html;



?>