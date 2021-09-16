<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion City School</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="./css/bootstrap.min.css">

    <link rel="stylesheet" href="../fontawesome/css/all.css">

    <!-- animate.css -->
    <link rel="stylesheet" href="./css/animate.css">

    <script
        src="https://code.jquery.com/jquery-3.3.1.js" 
        integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" 
        crossorigin="anonymous">
    </script>

    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" 
            integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" 
            crossorigin="anonymous"></script>
    <script src="../js/bootstrap.min.js"></script>

    <script src="index.js"></script>
    <script src="frmNotificaciones.js"></script>
    <script src="frmMttoAlumnos.js"></script> 

    <style type="text/css">
        body { padding-top: 100px; } 

        #opciones {
            width: 100%;
            background: #f1891e;
            left: 0;
            top: 0;
            position: fixed; 
        }
    </style>

</head>

<body>

    <div id="mttoAlumno" class="container-fluid" style="display: none;">
    </div>
    
    <header>
        <div id="notificaciones" class="animate__fadeIn container-fluid" style="display: block;">
            <?php include("frmNotificaciones.php"); ?>
        </div>
    </header>

    <div id="mttoNotas" class="container-fluid" style="display: none;">
    </div>

</body>
</html>