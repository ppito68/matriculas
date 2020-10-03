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

function GetHtmlFillSelectCursos($cursoPreSelect){
    $html='<option value="0"></option>';
    $cursos=CovGetCursos(0,0,"",2); // el 2 es para que ordene por curso
    foreach($cursos as $curso){ 
        $selected=$cursoPreSelect==$curso["curso"] ? "selected" : "";
        $html = $html . '<option value="' . $curso["curso"] . '" ' . $selected . '>' . $curso["curso"] . '</option>';
    }
    return $html;
}

function GetHtmlFillSelectAulas($idAulaPreSelect){
    $html='<option value="0"></option>';
    $aulas=GetAulas(); // sin parametros no filtra el centro
    foreach($aulas as $aula){ 
        $selected=$idAulaPreSelect==$aula["Id"] ? "selected" : "";
        $html = $html . '<option value="' . $aula["Id"] . '" ' . $selected . '>' . $aula["Aula"] . '</option>';
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

function GetHtmlFillSelectHorarios($horarioPreSelect){
    $html='<option value="0"></option>';
    $horarios=CovGetAllHorarios();
    foreach($horarios as $horario){ 
        $selected=$horarioPreSelect==$horario["horario"] ? "selected" : "";
        $html = $html . '<option value="' . $horario["horario"] . '" ' . $selected . '>' . $horario["horario"] . '</option>';
    }
    return $html;
}

// $fechaComunicacionEmail: Es la fecha en la que se conunicó por email al alumno el modo de asistencia a la clase, Puede ser NULL porque
//  no se haya notificado aun.
function GetUrlIconosAsistencia($modoAsistencia, $fechaComunicacionEmail, $fechaRecibidoEmail){
    $url="";
    if($modoAsistencia=="a"){

        // Si no se ha enviado email al alumno, coloca el icono de ASINTENCIAL sin el sobre de enviado
        if(is_null($fechaComunicacionEmail)){
            $url="./img/asist.png";
        
        }else{ // Si se ha enviadoi email al alumno

            // Si no ha marcado el email comoleido, colocal el icono de asintencial pero con el sobre ROJO
            if(is_null($fechaRecibidoEmail)){
                $url="./img/asist_EnviadoSinLeer.png";
            
            }else{// Si el alumno ha leido el email, coloca el icono de asistencial con el sobre VERDE
                $url="./img/asist_EnviadoLeido.png";
            }
        }
    } else if ($modoAsistencia=="o"){
        
        // Si no se ha enviado email al alumno, coloca el icono de REMOTO sin el sobre de enviado
        if(is_null($fechaComunicacionEmail)){
            $url="./img/remote.png";
        
        }else{ // Si se ha enviadoi email al alumno

              // Si el alumno no ha marcado el email como leido, colocal el icono de REMOTO pero con el sobre ROJO
              if(is_null($fechaRecibidoEmail)){
                $url="./img/Remote_EnviadoSinLeer.png";
            
            }else{// Si el alumno ha leido el email, coloca el icono de REMOTO con el sobre VERDE
                $url="./img/Remote_EnviadoLeido.png";
            }
        }

    } 

    return $url;
}

?>