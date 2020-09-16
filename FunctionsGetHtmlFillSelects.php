<?php

require_once("Db.php");

//Params: idCentroSeleccionado.- Es el elemento que debe quedar seleccionado en la lista de seleccion
function GetHtmlFillSelectCentros($idCentroSeleccionado){
    $html='<option value="0"></option>';
    $centros=GetCentros();
    foreach($centros as $centro){ 
        $selected=$idCentroSeleccionado==$centro["id"] ? "selected" : "";
        $html = $html . '<option value="' . $centro["id"] . '" ' . $selected . '>' . $centro["centro"] . '</option>';
    }
    return $html;
}

function GetHtmlFillSelectFechas($idPromocion){
    $html='<option value="0"></option>';
    $fechas=GetFechasCalendario($idPromocion);
    foreach($fechas as $fecha){ 
        $html = $html . '<option value="' . $fecha["fecha"] . '">' . $fecha["diaSemanaYFecha"] . '</option>';
    }
    return $html;
}

// $fechaComunicacionEmail: Es la fecha en la que se conunicó por email al alumno el modo de asistencia a la clase, Puede ser NULL porque
//  no se haya notificado aun.
function GetUrlIconosAsistencia($modoAsistencia, $fechaComunicacionEmail){
    $url="";
    if($modoAsistencia=="a"){
        if(is_null($fechaComunicacionEmail)){
            $url="./img/asist.png";
        }else{
            $url="./img/asist_enviado.png";
        }
    } else if ($modoAsistencia=="o"){
        if(is_null($fechaComunicacionEmail)){
            $url="./img/remote.png";
        }else{
            $url="./img/remote_enviado.png";
        }

    } 

    return $url;
}

?>