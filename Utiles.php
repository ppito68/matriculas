<?php

Function EnviarEmail($Destinatario, $Asunto, $Mensaje){

    $header='From: CITY SCHOOL <admin@cityschool.es>'; //. "\r\n" .
    //'Bcc: admin@cityschool.es' . "\r\n"; // esto sería copia oculta
    // 'Reply-To: info@lucrecianovias.com' . "\r\n";

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