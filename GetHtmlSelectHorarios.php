<?php
    
    // Devuelve una cadena con la lista de opciones de una lista de seleccion (combo) de cursos

    require_once("Db.php");

    $fecha = $_POST["fecha"];
    $idCentro=$_POST["idCentro"];
    $idAula=$_POST["idAula"];
    $curso = $_POST["curso"];

    $cadenaHtml='<option value="0"></option>';
    $cursos=CovGetHorarios($fecha, $idCentro, $idAula, $curso);

    while($row=$cursos->fetch(PDO::FETCH_ASSOC)){ 
        $cadenaHtml = $cadenaHtml . '<option value="' . $row["horario"] . '">' . $row["horario"] . '</option>';
    }

    echo $cadenaHtml;

?>