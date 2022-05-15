<?php

require_once('Db.php');


if ($_SERVER['REQUEST_METHOD'] == 'POST'){

    $idMatricula =  $_POST["idMatricula"];
    $speaking1 = is_null($_POST["speaking1"]) ? 0 : $_POST["speaking1"];
    $speaking2 = is_null($_POST["speaking2"]) ? 0 : $_POST["speaking2"];
    $listening1 = is_null($_POST["listening1"]) ? 0 : $_POST["listening1"];
    $listening2 = is_null($_POST["listening2"]) ? 0 : $_POST["listening2"];
    $writing1 = is_null($_POST["writing1"]) ? 0 : $_POST["writing1"];
    $writing2 = is_null($_POST["writing2"]) ? 0 : $_POST["writing2"];
    $reading1 = is_null($_POST["reading1"]) ? 0 : $_POST["reading1"];
    $reading2 = is_null($_POST["reading2"]) ? 0 : $_POST["reading2"];
    $exEscrito1 = is_null($_POST["exEscrito1"]) ? 0 : $_POST["exEscrito1"];
    $exEscrito2 = is_null($_POST["exEscrito2"]) ? 0 : $_POST["exEscrito2"];
    $participacion1 = is_null($_POST["participacion1"]) ? 0 : $_POST["participacion1"];
    $participacion2 = is_null($_POST["participacion2"]) ? 0 : $_POST["participacion2"];
    $comportamiento1 = is_null($_POST["comportamiento1"]) ? 0 : $_POST["comportamiento1"];;
    $comportamiento2 = is_null($_POST["comportamiento2"]) ? 0 : $_POST["comportamiento2"];
    $examenOral2 = is_null($_POST["examenOral"]) ? 0 : $_POST["examenOral"];


    // $examDone1 = is_null($_POST["examDone1"]) ? 0 : $_POST["examDone1"];
    // $examDone2 = is_null($_POST["examDone2"]) ? 0 : $_POST["examDone2"];
    // $examDone1 = ($examDone1) ? 1 : 0 ;
    // $examDone2 = ($examDone2) ? 1 : 0 ;

    $unit1 = is_null($_POST["unit1"]) ? 0 : $_POST["unit1"];
    $unit2 = is_null($_POST["unit2"]) ? 0 : $_POST["unit2"];
    $unit3 = is_null($_POST["unit3"]) ? 0 : $_POST["unit3"];
    $unit4 = is_null($_POST["unit4"]) ? 0 : $_POST["unit4"];
    $unit5 = is_null($_POST["unit5"]) ? 0 : $_POST["unit5"];
    $unit6 = is_null($_POST["unit6"]) ? 0 : $_POST["unit6"];
    $unit7 = is_null($_POST["unit7"]) ? 0 : $_POST["unit7"];
    $unit8 = is_null($_POST["unit8"]) ? 0 : $_POST["unit8"];
    $unit9 = is_null($_POST["unit9"]) ? 0 : $_POST["unit9"];
    $unit10 = is_null($_POST["unit10"]) ? 0 : $_POST["unit10"];
    $unit11 = is_null($_POST["unit11"]) ? 0 : $_POST["unit11"];
    $unit12 = is_null($_POST["unit12"]) ? 0 : $_POST["unit12"];

    $unit13 = is_null($_POST["unit13"]) ? 0 : $_POST["unit13"];
    $unit14 = is_null($_POST["unit14"]) ? 0 : $_POST["unit14"];
    $unit15 = is_null($_POST["unit15"]) ? 0 : $_POST["unit15"];
    $unit16 = is_null($_POST["unit16"]) ? 0 : $_POST["unit16"];
    $unit17 = is_null($_POST["unit17"]) ? 0 : $_POST["unit17"];
    $unit18 = is_null($_POST["unit18"]) ? 0 : $_POST["unit18"];
    $unit19 = is_null($_POST["unit19"]) ? 0 : $_POST["unit19"];
    $unit20 = is_null($_POST["unit20"]) ? 0 : $_POST["unit20"];

    $comentarios = is_null($_POST["comentarios"]) ? '' : $_POST["comentarios"];
    $enviado1 = is_null($_POST["enviado1"]) ? false : $_POST["enviado1"];
    $enviado2 = is_null($_POST["enviado2"]) ? false : $_POST["enviado2"];


    // Busca en base de datos a ver si existeen las notas del alumno
    $res=GetNotas($idMatricula);

    // si las notas ya existían, la modifica
    if($notas=$res->fetch(PDO::FETCH_ASSOC)){


        try {
            $r = ModificaNotas($idMatricula, $speaking1, $speaking2, $listening1, $listening2, $writing1, $writing2, $reading1, $reading2,
                                $exEscrito1, $exEscrito2, $participacion1, $participacion2, $comportamiento1, $comportamiento2, $examenOral2,
                                $unit1, $unit2, $unit3, $unit4, $unit5, $unit6, $unit7, $unit8, $unit9, $unit10, $unit11, $unit12, $unit13,
                                $unit14, $unit15, $unit16, $unit17, $unit18, $unit19, $unit20, $comentarios, $enviado1, $enviado2);
            header("HTTP/1.1 200 OK");
            // echo 'modificado ok';

        } catch (\Throwable $th) {
            // echo 'error al modificar->' . $th->getMessage();
            header("HTTP/1.1 400 ERROR AL MODIFICAR LAS NOTAS");
        }



    }else{ // si NO existía el alumno, lo graba nuevo

        //try {
            $r = GrabacionNotas($idMatricula, $speaking1, $speaking2, $listening1, $listening2, $writing1, $writing2, $reading1, $reading2,
                                    $exEscrito1, $exEscrito2, $participacion1, $participacion2, $comportamiento1, $comportamiento2, $examenOral2,
                                    $unit1, $unit2, $unit3, $unit4, $unit5, $unit6, $unit7, $unit8, $unit9, $unit10, $unit11, $unit12, $unit13,
                                    $unit14, $unit15, $unit16, $unit17, $unit18, $unit19, $unit20, $comentarios);
            header("HTTP/1.1 200 OK");
            // echo 'grabacion ok';


        //} catch (\Throwable $th) {
            // echo 'error al grabar->' . $th->getMessage();
        //    header("HTTP/1.1 400 ERROR AL GRABAR LAS NOTAS");
        //}


    };



} 



?>

