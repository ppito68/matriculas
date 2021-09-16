<?php

require_once('../fpdf/fpdf.php');
require_once('Db.php');


function EnviarEmailConAdjunto($Destinatario, $Asunto, $mensaje, $file){


    //remitente del correo
    $from = 'admin@cityschool.es';
    $fromName = 'CITY SCHOOL';

    //Encabezado para información del remitente
    $headers = "De: $fromName"." <".$from.">";

    // $headers .= "\ncc: administracion@grupodmc.com\r\n" . // esto sería copia oculta

    //Limite Email
    $semi_rand = md5(time()); 
    $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 

    //Encabezados para archivo adjunto 
    $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 

    //límite multiparte
    $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
    "Content-Transfer-Encoding: 7bit\n\n" . $mensaje . "\n\n"; 

    //preparación de archivo
    if(!empty($file) > 0){
        if(is_file($file)){
            $message .= "--{$mime_boundary}\n";
            $fp =    @fopen($file,"rb");
            $data =  @fread($fp,filesize($file));

            @fclose($fp);
            $data = chunk_split(base64_encode($data));
            $message .= "Content-Type: application/octet-stream; name=\"".basename($file)."\"\n" . 
            "Content-Description: ".basename($files[$i])."\n" .
            "Content-Disposition: attachment;\n" . " filename=\"".basename($file)."\"; size=".filesize($file).";\n" . 
            "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
        }
    }
    $message .= "--{$mime_boundary}--";
    $returnpath = "-f" . $from;

    //Enviar EMail
    $mail = @mail($Destinatario, $Asunto, $message, $headers, $returnpath); 

    //Estado de envío de correo electrónico
    //echo $mail?"<h1>Correo enviado.</h1>":"<h1>El envío de correo falló.</h1>";



}




Function EnviarEmail($Destinatario, $Asunto, $Mensaje){

    $header =  "MIME-Version: 1.0\r\n" .
               "Content-type: text/html; charset=iso-8859-1\r\n" .
               "From: CITY SCHOOL <admin@cityschool.es>\r\n" .
               "Bcc: admin@cityschool.es\r\n" . // esto sería copia oculta
               "Reply-To: admin@cityschool.es\r\n" .
               "Return-path: admin@cityschool.es\r\n"; 

    try{
        // if(mail($Destinatario, $Asunto, $Mensaje, 'admin@cityschool.es')){
        if(mail($Destinatario, $Asunto, $Mensaje, $header)){ 
            return true;

        }else{
            return false;

        }

    }catch(Exception $e){
        return false;

    }
}


function getDiaSemanaEnLetras($diasCifra){
    $dias = array('Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo');
    return $dias[$diasCifra-1];
}

function GenerarNotasPdf($idMatricula, $soloImprmir){

    // Busca en base de datos a ver si existeen las notas del alumno
    $res=GetNotas($idMatricula);

    // si existe las notas, carga los datos del alumno.
    if($notas=$res->fetch(PDO::FETCH_ASSOC)){

        $y=0; // Inicializo la Ordenada
        
        $pdf=new FPDF();
        $pdf->AddPage();
    
        // Logo fondo marca de agua
        $pdf->Image('./img/LogofondoMa.jpg',0, $y, 210, 300);
    
        // ------------------------------
        //      C A B E C E R A
        // ------------------------------
        // Logo Cabecera
        $anchoLogo=40;
        $altoLogo=741/1115 * $anchoLogo;
        $y=10;
        $pdf->Image('./img/logo.png',30, $y, $anchoLogo, $altoLogo); //, 50, 50);
        $pdf->SetLineWidth(1);
        $y+=28;
        $pdf->Line(5, $y, 200, $y);
        $y+=2;
        $pdf->SetLineWidth(0.7);
        $pdf->Line(5, $y, 180, $y);
        $y+=2;
        $pdf->SetLineWidth(0.4);
        $pdf->Line(5, $y, 160, $y);
        $pdf->ln(33);
    
        // ---------------------------------------------------
        //       C U E R P O    D E L    B O L E T I N
        // ---------------------------------------------------
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(0,10,'NOTAS DEL CURSO 2020-2021', 0, 2, 'C');
        //
        // Nombre Alumno
        $pdf->ln(4);
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0, 10, utf8_decode($notas['nombre'] . ' ' . $notas['apellidos']), 1, 1, 'C');
        // nº alumno - Curso - Horario - dias/semana - profesor
        $pdf->ln(4);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(0, 10, utf8_decode('NºAlumno: ') . $notas['numero'] . '  -  Curso: ' . $notas['curso'] . '  -  Horario: ' . $notas['horario'] . ' ' . $notas['dias'] . '  -  Profesor: ' . $notas['nombreProfesor'] , 1, 1, 'C');
        //
        // Notas
        $pdf->SetLineWidth(0.1);
        $pdf->ln(5);
        $pdf->SetFont('Arial','B',12);
        $pdf->SetFillColor(175);
        $pdf->Cell(55, 7, 'NOTAS', 1, 0, 'C', true);
        //
        $pdf->SetY($pdf->GetY()+3);
        $pdf->SetFont('Arial','B',10);
        $pdf->SetX(100);
        $pdf->Cell(25, 5, utf8_decode('1ºEXAMEN'), 'B', 0, 'C');
        $pdf->SetX(130);
        $pdf->Cell(25, 5, utf8_decode('2ºEXAMEN'), 'B', 1, 'C');
        //
        // Calcula la nota media enrtre todas las habilidades
        $notaFinal1 = ( $notas[speaking1] + $notas[listening1] + $notas[writing1] + $notas[reading1] + $notas[examenEscrito1]  ) / 5;
        $notaFinal2 = ( $notas[speaking2] + $notas[listening2] + $notas[writing2] + $notas[reading2] + $notas[examenEscrito2]  ) / 5;

        $aAsignatura = array ( 'Expresión Oral', 'Comprensión Oral', 'Expresión Escrita', 'Comprensión Escrita', 'Examen Escrito', 'Nota Final' );
        $aNotas = array(     array($notas[speaking1], $notas[speaking2]), 
                            array($notas[listening1], $notas[listening2]),
                            array($notas[writing1], $notas[writing2]),
                            array($notas[reading1], $notas[reading2]),
                            array($notas[examenEscrito1], $notas[examenEscrito2]),
                            array($notaFinal1, $notaFinal2),
                    );
    
        // Notas por habilidades
        $n=count($aAsignatura); // obtiene el numero de elementos del array de notas
        for($i=0; $i<$n; $i++){
            $rellenaFondo = ($i==$n-1) ? true : false; // para que rellene el fondo de la celda del ultimo elemento del array que es la nota final
            $pdf->ln(3);
            $pdf->SetX(50);
            $pdf->Cell(40, 6, utf8_decode($aAsignatura[$i]), 1, 0, 'L', $rellenaFondo);
            $pdf->SetX(105);
            $pdf->Cell(15, 6, number_format($aNotas[$i][0],2), 1, 0, 'C', $rellenaFondo);
            $pdf->SetX(135);
            $pdf->Cell(15, 6, number_format($aNotas[$i][1],2), 1, 1, 'C', $rellenaFondo);
        }
    
        // Notas Informativas
        $pdf->ln(7);
        $pdf->SetFont('Arial','B',10);
        $pdf->SetFillColor(175);
        $pdf->Cell(55, 7, 'NOTAS INFORMATIVAS', 1, 1, 'C', true);
        
        $aActitudes = array ('Participación', 'Comportamiento', 'Examen Oral');
        $aNotasActitudes = array(   array($notas[participacion1], $notas[participacion2]),    
                                    array($notas[comportamiento1], $notas[comportamiento2]),    
                                    array('', $notas[examenOral2]),    
                                );
        for($a = 0; $a < count($aActitudes); $a++ ){//foreach($aActitudes as $aActitud){
            $pdf->ln(3);
            $pdf->SetX(50);
            $pdf->Cell(40, 6, utf8_decode($aActitudes[$a]), 1, 0, 'L');
            $pdf->SetX(105);
            $pdf->Cell(15, 6, number_format($aNotasActitudes[$a][0],2), 1, 0, 'C');
            $pdf->SetX(135);
            $pdf->Cell(15, 6, number_format($aNotasActitudes[$a][1],2), 1, 1, 'C');
        }
    
        // Unit Test y comentarios
        $pdf->ln(7);
        $pdf->SetFont('Arial','B',10);
        $pdf->SetFillColor(175);
        $pdf->Cell(55, 7, 'UNIT TEST Y COMENTARIOS', 1, 1, 'C', true);
        //

        $aUnits = array( $notas[unit1], $notas[unit2], $notas[unit3], $notas[unit4], $notas[unit5], $notas[unit6], $notas[unit7], 
                        $notas[unit8], $notas[unit9], $notas[unit10], $notas[unit11], $notas[unit12], $notas[unit13], $notas[unit14],
                        $notas[unit15], $notas[unit16], $notas[unit17], $notas[unit18], $notas[unit19], $notas[unit20] );
      
        // impresion numeros unit del 1 al 10
        $pdf->ln(3);
        for($x=1;$x<=10;$x++){
            $pdf->SetX($x*17);
            $pdf->Cell(11, 6, $x, 0, 0, 'C');
        }                        

        $pdf->ln(5);
        for($x=1;$x<=10;$x++){
            $pdf->SetX($x*17);
            $pdf->Cell(11, 6, number_format($aUnits[$x-1],2), 1, 0, 'C');
        }

        // impresion numeros unit del 11 al 20
        $pdf->ln(6);
        for($x=1;$x<=10;$x++){
            $pdf->SetX($x*17);
            $pdf->Cell(11, 6, $x+10, 0, 0, 'C');
        }

        $pdf->ln(5);
        for($x=1;$x<=10;$x++){
            $pdf->SetX($x*17);
            $pdf->Cell(11, 6, number_format($aUnits[$x+9],2), 1, 0, 'C');
        }

                
        // comentarios
        $pdf->SetFont('Arial','',9);
        $pdf->ln(10);
        $pdf->SetX(15);
        $pdf->MultiCell(176, 5, utf8_decode($notas['comentarios']), 1, 'J');
    
        // fecha de informe
        $pdf->ln(8);
        $pdf->SetX(120);
        $pdf->SetFont('Arial','',11);
        $pdf->Cell(55, 7, 'Sevilla, a 25 de Junio de 2021', 0, 1);
    
        if($soloImprmir){
            $pdf->Output();

        }else{
            $pdf->Output('F', './pdfs/' . $idMatricula . '.pdf');
        }

        $res->closeCursor();

        return true;

    }else{
        return false;

    }

}



?>