<?php

// // Continuación de sesión
// session_start();
// if (!isset($_SESSION["user"])) {
//     header("location:LoginStaff.php"); //  echo "<script>location.href='FrmLogin.php';</script>";
//     exit;
// }

require_once("Db.php");

$idCentro= $_GET["idCentro"];
$idCurso=$_GET["idCurso"];
$idDia=$_GET["idDia"];
$idHorario=$_GET["idHorario"];
$idPromocion=$_GET["idPromocion"];
$idAula=$_GET["idAula"];
// $login = $_GET["login"];
// $pass = $_GET["pass"];


$html='<div class="row align-items-center" ">
            <div class="col-1 lead text-center border rounded bg-light" style="font-size:small">NºAlumno</div>
            <div class="col-2 lead text-center border rounded bg-light" style="font-size:small">Alumno</div>
            <div class="col-1 lead text-center border rounded bg-light" style="font-size:small">Curso</div>
            <div class="col-2 lead text-center border rounded bg-light" style="font-size:small">Dias</div>
            <div class="col-1 lead text-center border rounded bg-light" style="font-size:small">Horario</div>
            <div class="col-5 lead text-center border rounded bg-light" style="font-size:small">Observaciones Tutor</div>
        </div> ';

$matriculas=GetMatriculas($idCentro, $idCurso, $idDia, $idHorario, $idPromocion, $idAula);

while($mat=$matriculas->fetch(PDO::FETCH_ASSOC)){

    // $checkVisto=$mat["visto"] ? 'checked' : '';

    $html = $html . '<div class="row border align-items-center">
                        <div class="col-1 lead text-right" id="numeroAlumno" style="font-size: small">' .  $mat["NumeroAlumno"] . '</div>
                        
                        <div class="col-2 lead text-uppercase" id="nombre" style="font-size: small">
                            <input type="checkbox" name="visto"' . $checkVisto . ' onchange="RegistraVisto(' . $mat["idSolicitud"] . ')">
                            <a href="FrmRegistroMatricula.php?idMatricula=' . $mat["idMatricula"] . '">' . $mat["nombreAlumno"] . '</a> 
                        </div>

                        <div class="col-1 lead text-uppercase" id="curso" style="font-size: small">' . $mat["descripcionCurso"] . '</div>
                        <div class="col-2 lead text-uppercase" id="dias" style="font-size: small">' . $mat["dias"] . '</div>
                        <div class="col-1 lead text-uppercase" id="horario" style="font-size: small">' . $mat["horario"] . '</div>
                        <div class="col-5 lead" id="observaciones" style="font-size: small">' . $mat["ObservacionesTutor"] . '</div>
                    </div> ';
}
echo $html;

?>

<!-- <div class="col-1 lead" id="mat">
                            <input type="checkbox" name="matriculado" disabled >
                        </div> -->