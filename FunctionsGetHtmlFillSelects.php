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

function GetHtmlFillDiasSemana($idDiaSeleccionado){
    $html='<option value="0"></option>';
    $dias=CovGetDiasSemana();
    foreach($dias as $dia){ 
        $selected=$idDiaSeleccionado==$dia["id"] ? "selected" : "";
        $html = $html . '<option value="' . $dia["id"] . '" ' . $selected . '>' . $dia["dias"] . '</option>';
    }
    return $html;
}

function GetHtmlFillSelectCursos($cursoPreSelect, $idPromocion){
    $html='<option value="0"></option>';
    $cursos=GetCursosFromMatriculas($idPromocion, 0,0,"",2); // el 2 es para que ordene por curso
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

function GetHtmlFillSelectProfesores($idProfesorPreSelect){
    $cadenaHtml='<option value="0"></option>';
    $profesores=GetProfesores();
    while($row=$profesores->fetch(PDO::FETCH_ASSOC)){ 
        $selected = ($idProfesorPreSelect==$row["id"]) ? "selected" : "";
        $cadenaHtml = $cadenaHtml . '<option value="' . $row["id"] . '" ' . $selected . '>' . $row["nombre"] . '</option>';
    }
    return $cadenaHtml;
}


function GetHtmlFillSelectFechas($idPromocion, $diasArray){
    $html='<option value="0"></option>';
    $fechas=GetFechasCalendario($idPromocion, $diasArray);
    foreach($fechas as $fecha){ 
        $html = $html . '<option value="' . $fecha["fecha"] . '">' . $fecha["diaSemanaYFecha"] . '</option>';
    }
    return $html;
}

function GetHtmlFillSelectHorarios($horarioPreSelect, $idPromocion){
    $html='<option value="0"></option>';
    $horarios=GetAllHorarios($idPromocion);
    foreach($horarios as $horario){ 
        $selected=$horarioPreSelect==$horario["horario"] ? "selected" : "";
        $html = $html . '<option value="' . $horario["horario"] . '" ' . $selected . '>' . $horario["horario"] . '</option>';
    }
    return $html;
}

// Debe quedar seleccionada la promoción actual según la fecha del sistema
function GetHtmlFillSelectPromociones(){

    // obtiene la fecha del sistema para que quede selecccionada la promocion actual
    $hoy = strtotime(date("Y-m-d", time()));

    $html='<option value="0"></option>';
    $promociones=GetPromociones();
    foreach($promociones as $promocion){ 

        // Establece la cadena -selected- para la promocion seleccionada si la fecha actual está entre la fecha desde y hasta de la promocion.
        $fDesde=strtotime($promocion["fechaDesde"]); // obtiene la fecha DESDE de la promocion
        $fHasta=strtotime($promocion["fechaHasta"]); // obtiene la fecha HASTA de la promocion
        $selected= ( $hoy >= $fDesde && $hoy <= $fHasta ) ? "selected" : "";
        
        $html = $html . '<option value="' . $promocion["Id"] . '"' . $selected . '>' . $promocion["Promocion"] . '</option>';

    }
    return $html;

}





?>