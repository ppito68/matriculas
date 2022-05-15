<?php
    
    // Devuelve una cadena con la lista de opciones de una lista de seleccion (combo) de horarios

    require_once("Db.php");

    // $fecha = $_POST["fecha"];
    // $idCentro=$_POST["idCentro"];
    // $idAula=$_POST["idAula"];
    // $curso = $_POST["curso"];
    $idHorarioPreSelect = $_POST['horarioPreSelect'];
    // $idPromocion = $_POST["idPromocion"];

    $cadenaHtml='<option value="0"></option>';
    
    // $horarios=CovGetHorarios($fecha, $idCentro, $idAula, $curso, $idPromocion);
    $horarios=GetAllHorarios();

    while($row=$horarios->fetch(PDO::FETCH_ASSOC)){ 
        
        // $selected = ($horarioPreSelect==$row["horario"]) ? "selected" : "";
        $selected = ($idHorarioPreSelect==$row["id"]) ? "selected" : "";
        
        $cadenaHtml = $cadenaHtml . '<option value="' . $row["id"] . '" ' . $selected . '>' . $row["horario"] . '</option>';
    }

    echo $cadenaHtml;

?>