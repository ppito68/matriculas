<?php

    // // esto es para que al volver a esta pagina no dé error de pagina y la muestre
    // header('Cache-Control: no cache');
    // session_cache_limiter('private_no_expire');

    // // Inicia Sesión
    // session_start();

    // include("Db.php");

    // $login = $_GET["login"];
    // $pass = $_GET["pass"];

    // // login
    // $userDt = loginStaff($login, $pass);
    // if ($userDt->rowCount() == 0) {
    //     header('location: LoginStaff.php'); //echo "<script>location.href='FrmLogin.php';</script>";  
    //     session_destroy();
    //     exit;
    // }

    // $_SESSION["user"] = $login;


?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matriculaciones</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">

    <!-- Funciones para llenar las opciones de las listas de seleccion -->
    <?php
        require_once("FunctionsGetHtmlFillSelects.php");  
    ?>

    <script
        src="https://code.jquery.com/jquery-3.3.1.js" 
        integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" 
        crossorigin="anonymous">
    </script>

    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <!-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" 
            integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" 
            crossorigin="anonymous"></script>
    <script src="../js/bootstrap.min.js"></script>

    <script src="selects.js"></script>
    <script src="frmMttoMatriculas2021.js"></script>

</head>



<body>

    <!-- Contenedor de combo de Centros y Aulas -->
    <div class="container-fluid" id="opciones">
        <div class="row">
            
            <!-- Lista de seleccion de Centro -->
            <div class="col-4 input-group mt-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" for="cbxCentros">Centros:</label>
                </div>
                <select class="custom-select" id="cbxCentros" name="cbxCentros" ><?php echo GetHtmlFillSelectCentros(0); ?>
                </select>
            </div>

            <!-- lista de Seleccion de Aula -->
            <div class="col-2 input-group mt-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" for="cbxAulas">Aulas:</label>
                </div>
                <select class="custom-select" id="cbxAulas" name="cbxAulas" >
                </select>
            </div>
            
            
            <div class="col-3" id="cantidadSolicitudes"></div> 
            <!-- <div class="col-2"><button type="button" onclick="lista()">Cambiar Visualización</button></div> -->
        </div>
    </div>

    <!-- contendor de combos dependientes de Centros y Aulas -->
    <div class="container-fluid" id="combos">
        <div class="row">

            <!-- Lista de seleccion de curso -->
            <div class="col-3 input-group mt-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" for="cbxCursos">Cursos</label>
                </div>
                <select class="custom-select" id="cbxCursos" name="cbxCursos"></select>
            </div>
            
            <!-- Lista de seleccion de Dias / Semana -->
            <div class="col-5 input-group mt-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" for="cbxDias">Dias/Semana</label>
                </div>
                <select class="custom-select" id="cbxDias" name="cbxDias"></select>
            </div>
            
            <!-- Lista de seleccion de Horario -->
            <div class="col-4 input-group mt-3">
                <div class="input-group-prepend">
                    <label class="input-group-text" for="cbxHorarios">Horario</label>
                </div>
                <select class="custom-select" id="cbxHorarios" name="cbxHorarios"></select>
            </div>

        </div>
    </div>
    <hr>

    <!-- Contenedor de MAtriculas -->
    <div class="container-fluid" id="matriculas"></div>
    
    <!-- Contenedor Estadistico -->
    <div style="display:none;" class="container-fluid" id="ContenedorPorCursos">
        <div class="row">
            <div class="col-5 align-items-center">
                <div class="container"><h4>POR CURSOS/DIAS/HORARIO</h4></div>
                <div class="container" style="font-size: small">Los alumnos que NO hayan seleccionado días y horario NO aparecen en estos recuentos</div>
                <div class="container" id="solicitudesPorCursos"></div>
            </div>
            <div class="col-5 align-items-center">
                <div class="container"><h4>CUADRANTE</h4></div>
                <div class="container" style="font-size: small">Los alumnos que NO hayan seleccionado días y horario NO aparecen en estos recuentos</div>
                <div class="container" id="cuadrante"></div>
            </div>
            <div class="col-2 align-items-center">
                <div class="container"><h5>Selección incompleta</h5></div>
                <div class="container" id="solicitudesSinDias"></div>
            </div>

        </div>
    </div> 

           
</body>
</html>