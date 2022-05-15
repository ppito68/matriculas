<?php

require_once("Db.php");
require_once("Utiles.php");

$fecha = $_POST["fecha"];
$centro = $_POST["idCentro"];
$idAula = $_POST["idAula"];
$curso = $_POST["curso"];
$horario = $_POST["horario"];
$idPromocion = $_POST["idPromocion"];


// obtiene el dia de la semana en letras
$diaSemana = getDiaSemanaEnLetras(date("N", strtotime($fecha)));

if($fecha==0){
    exit;
}

$incidencias="";

$grupos = GetGruposAInformar($fecha, $centro, $idAula, $curso, $horario, $idPromocion);
while($grupo=$grupos->fetch(PDO::FETCH_ASSOC)){ 

    // *** A partir del 2021 despues de la navidad se informa a todos los grupos, tengan o no online
    // Sólo informa a los grupos que tengan asistencia ON LINE
    // if($grupo['online']!=0){

        $centroGrupo = $grupo['centro'];
        $idAulaGrupo = $grupo['idAula'];
        $cursoGrupo = $grupo['curso'];
        $horarioGrupo = $grupo['horario'];
        $diasSemana = $grupo['dias'];
        
        // obtener los alumnos del grupo en curso por orden de asistencia OnlIne
        $alumnos = GetAsistenciasGrupo($fecha, $centroGrupo, $idAulaGrupo, $cursoGrupo, $horarioGrupo, $idPromocion);
        $fechaConFormato = date("d-m-Y", strtotime($fecha));
        while($alumno=$alumnos->fetch(PDO::FETCH_ASSOC)){ 

            if($alumno['comunicarAsistencia']){

                $texto = "";

                if($alumno['asignado']=='a'){
    
                    // $texto = "Hola, te escribimos desde City School:" . "\n" . "\n" .
                    //     $alumno['nombre'] . " " . $alumno['apellidos'] .  ", te comunicamos que, según nuestro control interno de asistencia, el próximo día " . $diaSemana . ", " . $fechaConFormato . " la clase para ti será PRESENCIAL. " . "\n" . "\n" .
                    //     "En caso de no poder asistir, te rogamos nos lo comuniques con antelación para que tu plaza pueda ser ocupada por otro compañero." . "\n" . "\n" .
                    //     "Por favor, pulsa en el siguiente enlace para confirmar que ha recibido nuestro mensaje: https://www.cityschool.es/stf/cnf/wr.php?na=" . $alumno['numero'] . "&id=" . $alumno['idControlAsistencia'] . "\n" . "\n" .
                    //     "Muchas gracias por tu colaboración." . "\n" . "\n";
                    $texto = "<html>
                                    <body>
                                        <p>Hola, te escribimos desde City School:</p>
                                        <p><b>" . $alumno['nombre'] . " " . $alumno['apellidos'] .  "</b>, te comunicamos que, según nuestro control interno de asistencia, el próximo día <b>" . 
                                            $diaSemana . ", " . $fechaConFormato . "</b> la clase para ti será <b>PRESENCIAL.</b></p>
                                        <p>En caso de no poder asistir, te rogamos nos lo comuniques con antelación para que tu plaza pueda ser ocupada por otro compañero.</p>
                                        <p>Por favor, pulsa en el siguiente enlace para confirmar que ha recibido nuestro mensaje: https://www.cityschool.es/stf/cnf/wr.php?na=" . 
                                            $alumno['numero'] . "&id=" . $alumno['idControlAsistencia'] . "</p>
                                        <p>Muchas gracias por tu colaboración.</p>
                                    </body>
                                </html>";
                    
                    $destinatario = $alumno['email']; 
                    //  $destinatario = 'jaguilar68@gmail.com'; //'miguelezluis@gmail.com'; //   
    
                    if(  !EnviarEmail($destinatario, 'Comunicado clase PRESENCIAL ' . $diaSemana . ", " . $fechaConFormato , $texto)  ){
                        $incidencias .= "error al enviar el correo a " . $alumno["nombre"] . " " . $alumno['apellidos'] . "\n";

                    }else{
                        // registra la fecha de counicacion
                        CovPutFechaComunicacionEmail($fecha, $alumno['numero'], date("Y-m-d H:i:s"));
                    };
    
                }elseif($alumno['asignado']=='o'){
    
                    // $texto =  "Hola, te escribimos desde City School:" . "\n" . "\n" .
                    //         $alumno['nombre'] . " " . $alumno['apellidos'] .  ", te comunicamos que, según nuestro control interno de asistencia, el próximo día " . $diaSemana . ", " . $fechaConFormato . " la clase para ti será ON LINE, cuyo enlace de conexión es el siguiente." . "\n" . "\n" .
                    //         $alumno['url']  . "\n" . "\n" .
                    //         "Por favor, pulsa en el siguiente enlace para confirmar que ha recibido nuestro mensaje: https://www.cityschool.es/stf/cnf/wr.php?na=" . $alumno['numero'] . "&id=" . $alumno['idControlAsistencia'] . "\n" . "\n" .
                    //         "Muchas gracias por tu colaboración." . "\n" . "\n";
                    $texto = "<html>
                                <body>
                                    <p>Hola, te escribimos desde City School:</p>
                                    <p><b>" . $alumno['nombre'] . " " . $alumno['apellidos'] .  "</b>, te comunicamos que, según nuestro control interno de asistencia, el próximo día <b>" . $diaSemana . ", " . 
                                        $fechaConFormato . "</b> la clase para ti será <b>ON LINE</b>, cuyo enlace de conexión es el siguiente.<b>" . $alumno['url']  . "</p>
                                    <p>Por favor, pulsa en el siguiente enlace para confirmar que ha recibido nuestro mensaje: https://www.cityschool.es/stf/cnf/wr.php?na=" . 
                                        $alumno['numero'] . "&id=" . $alumno['idControlAsistencia'] . "</p>
                                    <p>Muchas gracias por tu colaboración.</p><br>
                                </body>
                            </html>";


                    $destinatario = $alumno['email']; 
                    //  $destinatario = 'jaguilar68@gmail.com'; //'miguelezluis@gmail.com'; //   
    
                    if(!EnviarEmail($destinatario, 'Comunicado clase ON LINE ' . $diaSemana . ", " . $fechaConFormato, $texto)){
                        echo "error al enviar el correo a " . $alumno["nombre"] . " " . $alumno['apellidos'];
                    }else{
                        // registra la fecha de counicacion
                        CovPutFechaComunicacionEmail($fecha, $alumno['numero'], date("Y-m-d H:i:s"));
                    };
    
                }
    
            }

        }

        $alumnos->closeCursor();
    // }
}

$grupos->closeCursor();

echo $incidencias;


?>




