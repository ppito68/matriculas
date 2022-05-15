<?php
    
    // Devuelve una cadena html con la lista de opciones de una lista de seleccion (combo) de cursos

    require_once("Db.php");

    // $idCentro=$_POST["idCentro"];
    // $idAula=$_POST["idAula"];
    $fecha = $_POST["fecha"];
    $idCursoPreSelect = $_POST['cursoPreSelect'];
    // $idPromocion = $_POST['idPromocion'];

    $nDiaSemana = isset($fecha) ? date("N", strtotime($fecha)) : 0; // obtiene el dia de la semana en cifra
    $sDiasSemana = "";

    if($nDiaSemana == 1 || $nDiaSemana == 3){ // Lunes y Miercoles
        $sDiasSemana = "M-W";
    }elseif($nDiaSemana == 2 || $nDiaSemana == 4){ // Martes y Jueves
        $sDiasSemana = "T-TH";
    }

    $cadenaHtml='<option value="0"></option>';
    
    //$cursos=GetCursosFromMatriculas($idPromocion, $idCentro, $idAula, $sDiasSemana );
    $cursos=GetCursos();

    while($row=$cursos->fetch(PDO::FETCH_ASSOC)){ 
        
        $selected = ($idCursoPreSelect==$row["idCurso"]) ? "selected" : "";
        
        //$cadenaHtml = $cadenaHtml . '<option value="' . $row["curso"] . '" ' . $selected . '>' . $row["curso"] . '</option>';
        $cadenaHtml = $cadenaHtml . '<option value="' . $row["idCurso"] . '" ' . $selected . '>' . $row["Descripcion"] . '</option>';

    }

    echo $cadenaHtml;

?>