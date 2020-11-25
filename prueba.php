<?php


include ("Db.php");

// function CovGetAsistencia($fecha, $numeroAlumno){
//                 $con=PdoOpenCon();
//                 $sql="SELECT * FROM stControlAsistencia c 
//                         WHERE c.fecha = :fecha AND c.numeroAlumno = :numeroAlumno ";
//                 $recSet=$con->prepare($sql);
//                 $recSet->execute(array(":fecha"=>$fecha, ":numeroAlumno"=>$numeroAlumno));
//                 return $recSet->fetch(PDO::FETCH_ASSOC);
//             }

$f = "2020-11-17";

$r = CovGetAsistencia($f, 273);

echo var_dump($r);


?>