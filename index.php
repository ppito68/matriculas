<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion City School</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="./css/bootstrap.min.css">

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


</head>

<body>

    <ul class="nav nav-tabs">
    <li class="nav-item">
        <a data-toggle="tab" class="nav-link" href="#home">Grupos</a>
    </li>
    <li class="nav-item">
        <a data-toggle="tab" class="nav-link" href="#menu1">Matriculas</a>
    </li>
    <li class="nav-item">
        <a data-toggle="tab" class="nav-link" href="#menu2">Tablas</a>
    </li>
    <li class="nav-item">
        <a data-toggle="tab" class="nav-link" href="#notificaciones">Notificaciones</a>
    </li>
    <!-- <li class="nav-item">
        <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
    </li> -->
    </ul>

    <div class="tab-content">
        <div id="home" class="tab-pane fade in active">
            <?php include("frmMttoMatriculas2021.php"); ?>
        </div>
        
        <div id="menu1" class="tab-pane fade">
            <!-- <h3>Menu 2</h3>
            <p>Some content in menu 2.</p> -->
        </div>

        <div id="menu2" class="tab-pane fade">
            <!-- <h3>Menu 2</h3>
            <p>Some content in menu 2.</p> -->
        </div>

        <div id="notificaciones" class="tab-pane fade in active">
            <?php include("frmNotificaciones.php"); ?>
        </div>
    </div>

</body>
</html>