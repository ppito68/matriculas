<?php
    
    require_once("Db.php");

    $idCentro=$_POST["idCentro"];
    $idAulaPreSelect=$_POST['idAulaPreSelect'];

    $cadenaHtml='<option value="0"></option>';

    // Si no viene el idCentro, no se cargan las aulas
    // if($idCentro!=0){
        $aulas=GetAulas($idCentro);
    // }

    while($row=$aulas->fetch(PDO::FETCH_ASSOC)){ 
        $selected = ($idAulaPreSelect==$row["Id"]) ? "selected" : "";
        $cadenaHtml = $cadenaHtml . '<option value="' . $row["Id"] . '" ' . $selected . '>' . $row["Aula"] . '</option>';
    }

    echo $cadenaHtml;

?>