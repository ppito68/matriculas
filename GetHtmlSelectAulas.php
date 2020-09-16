<?php
    
    require_once("Db.php");

    $idCentro=$_POST["idCentro"];

    $cadenaHtml='<option value="0"></option>';

    // Si no viene el idCentro, no se cargan las aulas
    if($idCentro!=0){
        $aulas=GetAulas($idCentro);
    }

    while($row=$aulas->fetch(PDO::FETCH_ASSOC)){ 
        $cadenaHtml = $cadenaHtml . '<option value="' . $row["Id"] . '">' . $row["Aula"] . '</option>';
    }

    echo $cadenaHtml;

?>