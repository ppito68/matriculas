<script>
    function ConmutaAsistencia(fecha, numeroAlumno, id) {

        const param = {
            fecha: fecha,
            numeroAlumno: numeroAlumno,
        };

        $.ajax({
            type: "post",
            url: "SetAsistencia.php",
            data: param,
            success: function(r) {
                $('#'+id).replaceWith(r);
            },

            error: function (error){
              console.log(error);
            }
        })
    }
</script>

<?php

require_once("Db.php");
require_once("FunctionsGetHtmlFillSelects.php");

$idCentro=$_POST["idCentro"];
$fecha = $_POST["fecha"];
$idAula=$_POST["idAula"];
$curso = $_POST["curso"];
$horario = $_POST["horario"];

// ****************************** C A B E C E R A *********************************

$html = //'<div class="container-fluid">
         '   <table class="table table-striped table-sm">
                <thead class="thead-light">
                    <tr>
                        <th scope="col-auto">nºAl.</th>
                        <th scope="col-2">Name</th>
                        <th scope="col-1">day/week</th>
                        <th scope="col-1">Curse</th>
                        <th scope="col-1">Hour</th>';


// obtiene los dias del calendario del mes de la fecha hasta la fecha recibida inclusive.
$diasRecSet = CovGetDiasCalendario($fecha);

// añade la fila de las columnas de los dias de asistencia
$diasArray = $diasRecSet->fetchAll();
foreach($diasArray as  $diaCal){
    $diaFormat = date("d/m", strtotime($diaCal['fecha']));
    
    // $html = $html . '<div class="col-auto bg-light lead text-uppercase" id="d' . $diaFormat . '" style="font-size: small">' . $diaFormat . '</div>';
    $html = $html . '<th scope="col-auto">' . $diaFormat . '</th>';
}
$diasRecSet->closeCursor();

// cierra el script de la cabecera de la lista de alumnos con las asistencias
// $html = $html . '</div></div>'; 
$html = $html . '</tr>
        </thead>';

// Obtiene los alumnos con el cuiadrante de asistencia
$alumnos=CovGetCuadranteAsistenciasPorAlumnos($idCentro, $idAula, $fecha, $curso, $horario);

$colAsist = $alumnos->columnCount() - 8;

$html = $html .        
    '<tbody>
        <tr>';

while($alum=$alumnos->fetch(PDO::FETCH_ASSOC)){

    $html = $html .        
        '<th scope="row">' . $alum["numero"] . '</th>
        <td>' . $alum["nombre"] . '</td>
        <td>' . $alum["dias"] . '</td>
        <td>' . $alum["curso"] . '</td>
        <td>' . $alum["horario"] . '</td>';

    // añade las columnas de los dias de asistencia del alumno
    foreach($diasArray as $diaCal){
        
        
        $f=$diaCal['fecha'];

        $html = $html . 
            '<td onclick="ConmutaAsistencia(\'' . $diaCal['fecha'] . '\', ' . $alum["numero"] . ', \'i' . $alum["numero"] . '-' . $f . '\')">
                <img id="i' . $alum["numero"] . '-' . $f . '" src="' . GetUrlIconosAsistencia($alum[$f], $alum['com'.$f] ) . '" class="img-responsive"/>
            </td>';

    }
    
    $html = $html . 
           '</tr>';
            
}

echo $html . '</tbody></table>'; //</div>';;

$alumnos->closeCursor();


?>
