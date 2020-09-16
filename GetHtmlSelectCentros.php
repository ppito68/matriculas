<?php

require_once("Db.php");


$html='<option value="0"></option>';
$centros=GetCentros();
foreach($centros as $centro){ 
    $html = $html . '<option value="' . $centro["id"] . '">' . $centro["centro"] . '</option>';
}
echo $html;

?>