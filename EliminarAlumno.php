<?php

require_once("Db.php");

$numero = $_POST["numero"];

try {
    CovEliminarAlumno($numero);
} catch (\Throwable $th) {
    echo "El alumno no se ha eliminado";
}
        


?>




