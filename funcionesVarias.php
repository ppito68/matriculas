<?php

function SetColorBackGround($modoPrevisto, $modoReal){
    $bgColor = "";

    // Si está establecido el modo real, comprueba que es el modo de asistencia real es igual a la prevista
    // De ser diferentes establece un color de fondo rojizo, y si son iguales lo establce a gris
    // Si no está establecido el modo real, no le pone color de fondo.
    if($modoReal){

        if( $modoReal != $modoPrevisto ){
            $bgColor = " background: #ecbcbc ";

        }else{
            $bgColor = " background: lightgray ";
        }
    }
    return $bgColor;
}


// $fechaComunicacionEmail: Es la fecha en la que se conunicó por email al alumno el modo de asistencia a la clase, Puede ser NULL porque
//      no se haya notificado aun.
// $soloAsistenciaPrevista: Si true, no tendrá en cuenta la asistencia Real y devolvera icono de asistencia prevista. Se usa para 
//      mostrar la asistencia prevista en el popup que muestra el evento over 
// $iconosConEmail: Si true, muestra los iconos de los modos de asistencia con una muestra de haberle enviado un correo al alumno 
function GetUrlIconosAsistencia($modoAsistenciaPrevista, 
                                $modoAsistenciaReal, 
                                $fechaComunicacionEmail, 
                                $fechaRecibidoEmail, 
                                $asignacionManual, 
                                $soloAsistenciaPrevista = false){

    // Si el modo real está establecido, se muestran los iconos del modo real, en caso contrario se muestran los iconos del modo previsto.
    // La variable $modo recoge el valor del modo real si éste está establecido, si no, recoge el valor del modo previsto
    $modo = null;
    if($soloAsistenciaPrevista){ 
        $modo = $modoAsistenciaPrevista;
    }else{
        $modo = ( $modoAsistenciaReal ) ? $modoAsistenciaReal : $modoAsistenciaPrevista;
    }


    $url="";

    // Si la asignacion del modo fue manual antepone el string "pre" delante de la imagen, que es la imagen de la asignacion manual
    $pre = ( $asignacionManual == true )  ? "pre" : "";
    
    if($modo == "a"){

        // Si no quiere mostrar los iconos con el sobre de haber enviado la comunicacion al alumno.
        // Esto se usa para mostrar las asistencias reales, ya que éstas al existir, no tiene sentido mostrar los iconos con los
        //  sobres de haber enviado emails a los alumnos. 
        if(!$soloAsistenciaPrevista && $modoAsistenciaReal){
            $url="./img/" . $pre . "Asist.png";

        }else{

            // Si no se ha enviado email al alumno, coloca el icono de ASINTENCIAL sin el sobre de enviado
            if(is_null($fechaComunicacionEmail)){
                $url="./img/" . $pre . "Asist.png";
            
            }else{ // Si se ha enviado email al alumno, comprueba si lo ha leido o no y pone un icono u otro, según.

                // Si no ha marcado el email como leido, coloca el icono de asintencial pero con el sobre ROJO
                if(is_null($fechaRecibidoEmail)){
                    $url="./img/" . $pre . "Asist_EnviadoSinLeer.png";
                
                }else{// Si el alumno ha leido el email, coloca el icono de asistencial con el sobre VERDE
                    $url="./img/" . $pre . "Asist_EnviadoLeido.png";
                }
            
            }

        }

    } elseif ($modo == "o"){

        // Si no quiere mostrar los iconos con el sobre de haber enviado la comunicacion al alumno.
        // Esto se usa para mostrar las asistencias reales, ya que éstas al existir, no tiene sentido mostrar los iconos con los
        //  sobres de haber enviado emails a los alumnos. 
        if(!$soloAsistenciaPrevista && $modoAsistenciaReal){
            $url="./img/" . $pre . "Remote.png";

        }else{

            // Si no se ha enviado email al alumno, coloca el icono de REMOTO sin el sobre de enviado
            if(is_null($fechaComunicacionEmail)){
                $url="./img/" . $pre . "Remote.png";
            
            }else{ // Si se ha enviadoi email al alumno

                // Si el alumno no ha marcado el email como leido, colocal el icono de REMOTO pero con el sobre ROJO
                if(is_null($fechaRecibidoEmail)){
                    $url="./img/" . $pre . "Remote_EnviadoSinLeer.png";
                
                }else{// Si el alumno ha leido el email, coloca el icono de REMOTO con el sobre VERDE
                    $url="./img/" . $pre . "Remote_EnviadoLeido.png";
                }

            }

        }

    } elseif($modo == "n"){
        $url="./img/NoAsiste.png";
    }

    return $url;
}



?>