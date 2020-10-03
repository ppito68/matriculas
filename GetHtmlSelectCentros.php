<?php

$idCentroPreSelect = $_POST['idCentroPreSelect'];

require_once("Db.php");

$html='<option value="0"></option>';
$centros=GetCentros();
foreach($centros as $centro){ 
    $selected = ( $idCentroPreSelect == $centro["id"] ) ? "selected" : "";
    $html = $html . '<option value="' . $centro["id"] . '" ' . $selected . '>' . $centro["centro"] . '</option>';
}
echo $html;

?>