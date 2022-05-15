<?php

require_once("Db.php");
require_once("Utiles.php"); // incluya las funciones de enviar corroes y crear las notas en pdf

$idMatricula = $_POST["idMatricula"];
$nombreCompletoAlumno = $_POST["nombreCompletoAlumno"];
$emailAlumno = $_POST["emailAlumno"];
$mostrarSolo = $_POST["mostrarSolo"];

// obtiene los datos del alumno


// generacion del pdf
GenerarNotasPdf($idMatricula, $mostrarSolo);



// si el parametro $mostrarColo viene a _false_ entonces enviará las notas por email
if(!$mostrarSolo){

    // texto del mensaje
    $texto = "<html>
                <body>
                    <p>Hola, te escribimos desde City School:</p>
                    <p><b>" . $nombreCompletoAlumno . "</b></p>
                    <p>Tenemos el placer de enviarte tus calificaciones.</p> 
                    <p>Gracias por confiar en nosotros.</p>
                </body>
            </html>";

    $filePath = './pdfs/' . $idMatricula . '.pdf';

    // $destinatario = 'jaguilar68@gmail.com';     // 'miguelezluis@gmail.com'; //  
    $destinatario = $emailAlumno; 

    EnviarEmailConAdjunto($destinatario, 'ENVIO DE CALIFICACIONES', $texto, $filePath);
    MarcaComoNotasEnviadas($idMatricula);

}






?>




