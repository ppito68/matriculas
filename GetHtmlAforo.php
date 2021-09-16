<?php
    
    require_once("Db.php");

    $centro=$_POST["idCentro"];
    $fecha=$_POST['fecha'];
    $idAula=$_POST['idAula'];
    $curso=$_POST['curso'];
    $horario=$_POST['horario'];
    $idPromocion = $_POST["idPromocion"];

    $cadenaHtml='';

    $grupo=AforoGrupo($fecha, $centro, $idAula, $curso, $horario, $idPromocion);

    // DEPURACION echo '- fecha:' .  $fecha . ' - centro:' . $centro . ' - aula:' . $idAula . ' - curso:' . $curso . ' - horario:' . $horario;

    // DEPURACION echo var_dump($grupo);

    if(!is_null($grupo)){

        if($gr=$grupo->fetch(PDO::FETCH_ASSOC)){

            $cadenaHtml = 'Aforo Aula:  <span class="badge badge-primary badge-pill">' . $gr['aforoCovid'] . '</span>
                            Alumnos:  <span class="badge badge-primary badge-pill">' . $gr['matriculados'] . '</span>';
        }
    }
    
    echo $cadenaHtml;



?>