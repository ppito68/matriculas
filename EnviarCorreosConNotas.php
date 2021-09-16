<?php

require_once("Db.php");
require_once("Utiles.php"); // incluya las funciones de enviar corroes y crear las notas en pdf

$fecha = $_POST["fecha"];
$centro = $_POST["idCentro"];
$idAula = $_POST["idAula"];
$curso = $_POST["curso"];
$horario = $_POST["horario"];

$incidencias="";

$grupos = CovGetGruposAInformar($fecha, $centro, $idAula, $curso, $horario);
while($grupo=$grupos->fetch(PDO::FETCH_ASSOC)){ 


    $centroGrupo = $grupo['centro'];
    $idAulaGrupo = $grupo['idAula'];
    $cursoGrupo = $grupo['curso'];
    $horarioGrupo = $grupo['horario'];
    $diasSemana = $grupo['dias'];
    
    // obtener los alumnos del grupo 
    $alumnos = CovGetAsistenciasGrupo($fecha, $centroGrupo, $idAulaGrupo, $cursoGrupo, $horarioGrupo);
    $fechaConFormato = date("d-m-Y", strtotime($fecha));
    while($alumno=$alumnos->fetch(PDO::FETCH_ASSOC)){ 

        // solo envia las notas a los alumnos que aun no se le ha enviado
        if(!NotasEnviadas($alumno['idMatricula'])){

            // generacion del pdf
            GenerarNotasPdf($alumno['idMatricula'], false);
            //$incidencias .= "error al generar pdf del alumno " . $alumno["nombre"] . " " . $alumno['apellidos'] . "\n";

            // texto del mensaje
            $texto = "<html>
                            <body>
                                <p>Hola, te escribimos desde City School:</p>
                                <p><b>" . $alumno['nombre'] . " " . $alumno['apellidos'] . "</b></p>
                                <p>Tenemos el placer de enviarte tus calificaciones del curso.</p> 
                                <p>Gracias por confiar en nosotros.</p>
                                <p>Te deseamos un feliz verano y un merecido descanso.</p>
                                <p>Hasta la vuelta.</p>
                            </body>
                        </html>";
            
            $destinatario = $alumno['email']; 
            // $destinatario = 'jaguilar68@gmail.com';     // 'miguelezluis@gmail.com'; //  

            $filePath = './pdfs/' . $alumno["idMatricula"] . '.pdf';
            EnviarEmailConAdjunto($destinatario, 'ENVIO DE CALIFICACIONES DEL CURSO ', $texto, $filePath);
            MarcaComoNotasEnviadas($alumno['idMatricula']);

        }
            
    };

    $alumnos->closeCursor();

}

$grupos->closeCursor();

echo $incidencias;


?>




