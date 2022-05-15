
<?php

require_once("Db.php");
require_once("funcionesVarias.php");

$idCentro=$_POST["idCentro"];
$fecha = $_POST["fecha"];
$idAula=$_POST["idAula"];
$idCurso = $_POST["curso"];
$idHorario = $_POST["horario"];
$idPromocion = $_POST["idPromocion"];

// ****************************** C A B E C E R A *********************************

$html = "<script>
            function ocultar(ap, ar){
                document.getElementById(ap).style.display = 'none';
                document.getElementById(ar).style.display = 'block';
            }

            function mostrar(ap, ar){
                document.getElementById(ap).style.display = 'block';            
                document.getElementById(ar).style.display = 'none';
            }
        </script>";

//Obtiene el cuadrante si la fecha tiene un valor, si no es asi, no calcula nada.
if($fecha){        

    $html .=                                //'<div class="container-fluid">
                '<table class="table table-striped table-sm">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col-auto">nºAl.</th>
                            <th scope="col-2">Name</th>';
                            // <th scope="col-1">day/week</th>
                            // <th scope="col-1">Curse</th>
                            // <th scope="col-1">Hour</th>';


    // obtiene los dias del calendario del mes de la fecha hasta la fecha recibida inclusive.
    $diasRecSet = GetDiasCalendario($idPromocion, $fecha);

    // añade la fila cabecera que muestra los dias en formato dd/mm de asistencia
    $diasArray = $diasRecSet->fetchAll();
    foreach($diasArray as  $diaCal){
        $diaFormat = date("d/m", strtotime($diaCal['fecha']));
        
        // $html = $html . '<div class="col-auto bg-light lead text-uppercase" id="d' . $diaFormat . '" style="font-size: small">' . $diaFormat . '</div>';
        $html = $html . '<th align="center" scope="col-auto">' . $diaFormat . '</th>';
    }
    $diasRecSet->closeCursor();

    // cierra el script de la cabecera de la lista de alumnos con las asistencias
    // $html = $html . '</div></div>'; 
    $html = $html . '</tr>
            </thead>';

    // Obtiene los alumnos con el cuadrante de asistencia
    $alumnos=GetCuadranteMatriculas($idCentro, $idAula, $fecha, $idCurso, $idHorario, $idPromocion);

    $colAsist = $alumnos->columnCount() - 8;

    $html = $html .        
        '<tbody>
            <tr>';


    // bucle ppal de recorrido de alumnos            
    while($alum=$alumnos->fetch(PDO::FETCH_ASSOC)){

        // le coloca el icono de "no enviar email" si el alumno no se le envía su forma de asistir a clase.
        $imgNoEmail = (!$alum["comunicarAsistencia"])
                        ? '<img class="ml-1" src="./img/noemail.png" width="20px" height="20px">'
                        : '';


        $iconoNotas = '';                    
        // Si las notas ya han sido enviada presenta el icono en verde
        if($alum["notasEnviadas"] == 1){
            $iconoNotas = '../assitancecontrol/img/iconoNotasEnviadas.svg';
        
        }elseif($alum["notasEnviadas"] == 0){
            $iconoNotas = '../assitancecontrol/img/iconoNotas.svg';

        }else{
            $iconoNotas = '../assitancecontrol/img/iconoSinNotas.svg';

        };
        
        
        // $iconoNotas = ( $alum["notasEnviadas"] == 1 ) ? '../assitancecontrol/img/iconoNotas.svg' : '../assitancecontrol/img/iconoSinNotas.svg'  ;

        $html = $html .        
            '<th scope="row">' . $alum["numero"] . '</th>
            <td>
                <img src="./img/eliminarAlumno.svg" width="20px" height="20px" 
                    onclick="EliminarMatricula(' . $alum["idMatricula"] . ')" 
                    style="cursor: pointer;"> 
                <img src="./img/edit.svg" width="20px" height="20px" 
                    onclick="MttoAlumno(' . $alum["numero"] . ')" 
                    style="cursor: pointer;"> 
                <img src="' . $iconoNotas . '" width="20px" height="20px" 
                    onclick="MttoNotasAlumno(' . $alum["idMatricula"] .  ')" 
                    style="cursor: pointer;"> 
                <span>' .
                    $alum["nombre"] . 
                '</span>
                <span class="small"> - ' .
                    $alum["curso"] . '-' . $alum["Aula"] . '-' . $alum["dias"] . '-' . $alum["horario"] . '-' . $alum["profesor"] . 
                    ' - (' . $alum["totalRemotos"] . ' OL - ' . $alum["totalPresenciales"] . ' Pr - ' . $alum["totalAusencias"] . ' Aus) 
                </span>' . $imgNoEmail . 
            '</td>';
            // <td>' . $alum["dias"] . '</td>
            // <td>' . $alum["curso"] . '</td>
            // <td>' . $alum["horario"] . '</td>';

        // añade las columnas de los dias de asistencia del alumno
        foreach($diasArray as $diaCal){
            
            $f=$diaCal['fecha'];

            // si tiene establecida la asistencia real, añade el html de la imagen para el popUp que muetra el icono de la aistencia
            //      prevista.
            $imgPopUp = ($alum['real'.$f])  ? '<img id="ipop' . $alum["idMatricula"] . '-' . $f . '" 
                                                    style="display: none"
                                                    src="' . GetUrlIconosAsistencia($alum[$f], 
                                                                                    $alum['real'.$f], 
                                                                                    $alum['com'.$f], 
                                                                                    $alum['rec'.$f], 
                                                                                    $alum['man'.$f],
                                                                                    true) . '"
                                                />'
                                            : '';

            $html = $html . 
                '<td onclick="ConmutaAsistencia(\'' . $diaCal['fecha'] . '\', ' . $alum["idMatricula"] . ', \'i' . $alum["idMatricula"] . '-' . $f . '\')"
                    onmouseover="mostrar(\'ipop' . $alum["idMatricula"] . '-' . $f . '\', \'i' . $alum["idMatricula"] . '-' . $f . '\')" 
                    onmouseout="ocultar(\'ipop' . $alum["idMatricula"] . '-' . $f . '\', \'i' . $alum["idMatricula"] . '-' . $f . '\')" 
                    style="cursor: pointer; ' . 
                        SetColorBackGround( $alum[$f], $alum['real'.$f] ) . '"  
                    align="center" valign="middle">
                    <img id="i' . $alum["idMatricula"] . '-' . $f . '" 
                        src="' . GetUrlIconosAsistencia($alum[$f], 
                                                        $alum['real'.$f], 
                                                        $alum['com'.$f], 
                                                        $alum['rec'.$f], 
                                                        $alum['man'.$f]) . '" 
                    />' . 
                    $imgPopUp . '
                </td>'; 

        }
        
        $html = $html . 
            '</tr>';
                
    }

    $html .= '</tbody></table>'; //</div>';

    $alumnos->closeCursor();

}

echo $html;



?>
