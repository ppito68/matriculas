<?php

require_once("Db.php");
require_once("Utiles.php");

$fecha = $_POST["fecha"];
$centro = $_POST["idCentro"];
$idAula = $_POST["idAula"];
$curso = $_POST["curso"];
$horario = $_POST["horario"];


// obtiene el dia de la semana en letras
$diaSemana = getDiaSemanaEnLetras(date("N", strtotime($fecha)));

if($fecha==0){
    exit;
}

$grupos = CovGetGruposAInformar($fecha, $centro, $idAula, $curso, $horario);
while($grupo=$grupos->fetch(PDO::FETCH_ASSOC)){ 

    // Sólo informa a los grupos que tengan asistencia ON LINE
    if($grupo['online']!=0){

        $centroGrupo = $grupo['centro'];
        $idAulaGrupo = $grupo['idAula'];
        $cursoGrupo = $grupo['curso'];
        $horarioGrupo = $grupo['horario'];
        $diasSemana = $grupo['dias'];
        
        // obtener los alumnos del grupo en curso por orden de asistencia OnlIne
        $alumnos = CovGetAsistenciasGrupo($fecha, $centroGrupo, $idAulaGrupo, $cursoGrupo, $horarioGrupo);
        $fechaConFormato = date("d-m-Y", strtotime($fecha));
        while($alumno=$alumnos->fetch(PDO::FETCH_ASSOC)){

            $texto = "";

            if($alumno['asignado']=='a'){

                $texto = "Hola, te escribimos desde City School:" . "\n" . "\n" .
                    $alumno['nombre'] . " " . $alumno['apellidos'] .  ", te comunicamos que, según nuestro control interno de asistencia, el próximo día " . $diaSemana . ", " . $fechaConFormato . " la clase para ti será PRESENCIAL. " . "\n" . "\n" .
                    "En caso de no poder asistir, te rogamos nos lo comuniques con antelación para que tu plaza pueda ser ocupada por otro compañero." . "\n" . "\n" .
                    "Por favor, pulsa en el siguiente enlace para confirmar que ha recibido nuestro mensaje: https://www.cityschool.es/stf/cnf/wr.php?na=" . $alumno['numero'] . "&id=" . $alumno['idControlAsistencia'] . "\n" . "\n" .
                    "Muchas gracias por tu colaboración." . "\n" . "\n";
                
                $destinatario = $alumno['email']; 
                // $destinatario = 'jaguilar68@gmail.com'; //'miguelezluis@gmail.com'; //   

                if(  !EnviarEmail($destinatario, 'Comunicado clase PRESENCIAL ' . $diaSemana . ", " . $fechaConFormato , $texto)  ){
                    echo "error al enviar el correo a " . $alumno["nombre"] . " " . $alumno['apellidos'];
                }else{
                    // registra la fecha de counicacion
                    CovPutFechaComunicacionEmail($fecha, $alumno['numero'], date("Y-m-d H:i:s"));
                };

            }elseif($alumno['asignado']=='o'){

                $texto =  "Hola, te escribimos desde City School:" . "\n" . "\n" .
                $alumno['nombre'] . " " . $alumno['apellidos'] .  ", te comunicamos que, según nuestro control interno de asistencia, el próximo día " . $diaSemana . ", " . $fechaConFormato . " la clase para ti será ON LINE, cuyo enlace de conexión es el siguiente." . "\n" . "\n" .
                $alumno['url']  . "\n" . "\n" .
                "Por favor, pulsa en el siguiente enlace para confirmar que ha recibido nuestro mensaje: https://www.cityschool.es/stf/cnf/wr.php?na=" . $alumno['numero'] . "&id=" . $alumno['idControlAsistencia'] . "\n" . "\n" .
                "Muchas gracias por tu colaboración." . "\n" . "\n";

                $destinatario = $alumno['email']; 
                // $destinatario = 'jaguilar68@gmail.com'; //'miguelezluis@gmail.com'; //   

                if(!EnviarEmail($destinatario, 'Comunicado clase ON LINE ' . $diaSemana . ", " . $fechaConFormato, $texto)){
                    echo "error al enviar el correo a " . $alumno["nombre"] . " " . $alumno['apellidos'];
                }else{
                    // registra la fecha de counicacion
                    CovPutFechaComunicacionEmail($fecha, $alumno['numero'], date("Y-m-d H:i:s"));
                };

            }

        }

        $alumnos->closeCursor();
    }
}

$grupos->closeCursor();



?>




