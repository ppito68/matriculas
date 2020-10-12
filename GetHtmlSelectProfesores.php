<?php
    
    require_once("Db.php");

    $idProfesorPreSelect=$_POST['idProfesorPreSelect'];

    $cadenaHtml='<option value="0"></option>';

    $profesores=GetProfesores();

    while($row=$profesores->fetch(PDO::FETCH_ASSOC)){ 
        $selected = ($idProfesorPreSelect==$row["id"]) ? "selected" : "";
        $cadenaHtml = $cadenaHtml . '<option value="' . $row["id"] . '" ' . $selected . '>' . $row["nombre"] . '</option>';
    }

    echo $cadenaHtml;

?>