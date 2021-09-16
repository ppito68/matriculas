<?php

require_once("Db.php");

$idMatricula = $_POST["idMatricula"];

try {
    EliminarMatricula($idMatricula);
} catch (\Throwable $th) {
    echo "La matrícula no se ha eliminado";
}
        


?>




