
<?php 
require_once("FunctionsGetHtmlFillSelects.php"); 
require_once("Db.php"); 

$numero = $_GET["numero"];
// $nombre = $_GET["nombre"];
// $apellidos = $_GET["apellidos"];
// $centro = $_GET["centro"];
// $idAula = $_GET["aula"];
// $curso = $_GET["curso"];
// $horario = $_GET["horario"];
// $dias = $_GET["dias"];
// $email = $_GET["email"];
// $email2 = $_GET["email2"];
// $url = $_GET["url"];

$result = CovGetAlumno($numero);
$alum=$result->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenimiento de alumnos City School</title>

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

    <script src="frmMttoAlumnos2021.js"></script>

</head>

<body>
  
  <form action="GrabacionNuevoAlumno.php" method="post">
    <div class="container-fluid mt-3">

      <input type="hidden" name="idAula" id="idAula" value="<?php echo $alum['idAula']; ?>">
      <input type="hidden" name="curso" id="curso" value="<?php echo $alum['curso']; ?>">
      
      <!-- Datos del alumno -->
      <div class="form-row">
        <div class="form-group col-md-1">
          <label for="numeroAlumno">NºAlumno</label>
          <input type="text" class="form-control" id="numero" name="numero" value="<?php echo $alum[numero]; ?>" required>
        </div>
        <div class="form-group col-md-2">
          <label for="nombreAlumno">Nombre</label>
          <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $alum[nombre]; ?>" required>
        </div>
        <div class="form-group col-md-4">
          <label for="Apellidos">Apellidos</label>
          <input type="text" class="form-control" id="apellidos" name="apellidos" value="<?php echo $alum[apellidos]; ?>">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="email">email</label>
          <input type="email" class="form-control" id="email" name="email" value="<?php echo $alum[email]; ?>" required>
        </div>
        <div class="form-group col-md-6">
          <label for="email2">email 2</label>
          <input type="email" class="form-control" id="email2" name="email2" value="<?php echo $alum[email2]; ?>">
        </div>
      </div>

      <!-- Combos -->
      <div class="form-row">
        <div class="form-group col-md-3">
          <label for="cbxMttoCentros">Centro</label>
          <select id="cbxMttoCentros" class="form-control" name="centro" required>
            <?php echo GetHtmlFillSelectCentros($alum[centro]); ?>
          </select>
        </div>
        <div class="form-group col-md-2">
          <label for="cbxMttoAulas">Aula</label>
          <select id="cbxMttoAulas" class="form-control" name="aula" required>
            <?php echo GetHtmlFillSelectAulas($alum[idAula]); ?>
          </select>
        </div>
        <div class="form-group col-md-2">
          <label for="cbxMttoCursos">Curso</label>
          <select id="cbxMttoCursos" class="form-control" name="curso" required>
            <?php echo GetHtmlFillSelectCursos($alum['curso']); ?>
          </select>
        </div>
        <div class="form-group col-md-2">
          <label for="cbxMttoHorario">Horario</label>
          <select id="cbxMttoHorario" class="form-control" name="horario" required>
            <?php echo GetHtmlFillSelectHorarios($alum[horario]); ?>
          </select>
        </div>
        <div class="form-group col-md-2">
          <label for="cbxMttoDiasSemana">Dias/Semana</label>
          <select id="cbxMttoDiasSemana" class="form-control" name="dias" required>
            <option value="M-W" <?php echo ($alum[dias]=='M-W') ? "selected" : ''; ?> >Lunes y Miércoles</option>
            <option value="T-TH" <?php echo ($alum[dias]=='T-TH') ? "selected" : ''; ?> >Martes y Jueves</option>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group col-md-12">
          <label for="url">url enlace zoom</label>
          <input type="text" class="form-control" id="url" name="url" value="<?php echo $alum[url]; ?>">
        </div>
      </div>
      
      <button type="submit" class="btn btn-primary">Guardar</button>
      <a href="index.php">VOLVER</a>
      
    </div>
  </form>

</body>








