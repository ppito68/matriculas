<?php
    
    require_once("Db.php");

    $idDias=$_POST["idDias"];
    $idPromocion = $_POST["idPromocion"];

    $diasArray = null;
    
    switch ($idDias) {
        case 1:
            $diasArray = array(1, 3);
            break;

        case 2:
            $diasArray = array(2, 4);
            break;
        
        case 3:
            $diasArray = array(5);
            break;
    }

    $cadenaHtml='<option value="0"></option>';
    $dias= GetFechasCalendario($idPromocion, $diasArray);

    while($row=$dias->fetch(PDO::FETCH_ASSOC)){ 
        $cadenaHtml = $cadenaHtml . '<option value="' . $row["fecha"] . '" ' . $selected . '>' . $row["diaSemanaYFecha"] . '</option>';
    }

    echo $cadenaHtml;

?>