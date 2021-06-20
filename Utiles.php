<?php

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

?>