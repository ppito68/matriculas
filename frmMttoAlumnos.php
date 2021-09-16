
<?php 
require_once("FunctionsGetHtmlFillSelects.php"); 
require_once("Db.php"); 

$numero = $_GET["numeroAlumno"];
$idPromocion = $_GET["idPromocion"];

// Carga datos del alumno
$resultAlum = GetAlumno($numero);
$alum = $resultAlum->fetch(PDO::FETCH_ASSOC);

//carga datos dela matricula, según la promocion
$resultMat = GetMatricula($alum["id"], $idPromocion);
$mat = $resultMat->fetch(PDO::FETCH_ASSOC);

// var_dump( $alum["id"], $idPromocion);

// Variables ocultas que yo habia puesto en el formulario pero que aparentemente nosirven para nada porque esos datos estan en los combos. Lo guardo porqie no estoy seguro de que no rompa nada quitando esto
// <input type="hidden" name="idAula" id="idAula" value="' . $alum['idAula'] . '">
// <input type="hidden" name="curso" id="curso" value="' . $alum['curso'] . '">

  
$html = '<script>
            $("#cbxMttoCentros").change(function() {
              const idCentro=$("#cbxMttoCentros").val();
              CargaComboAulasMttoAlumnos(idCentro, ' . $alum['idAula'] . ');
            });
        </script>
        
        <form action="GrabacionNuevoAlumno.php" method="post">

          <div class="container-fluid mt-3">

            <input type="hidden" name="idAlumno" id="idAlumno" value="' . $alum['id'] . '">
            <input type="hidden" name="idPromocion" id="idPromocion" value="' . $idPromocion . '">

            <div class="form-row">
              <div class="form-group col-md-1">
                <label for="numero">NºAlumno</label>
                <input type="text" onchange="MttoAlumno(this.value)" class="form-control" id="numero" name="numero" value="' . $numero . '" required>
              </div>
              <div class="form-group col-md-2">
                <label for="nombre">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="' . $alum["Nombre"] . '" required>
              </div>
              <div class="form-group col-md-4">
                <label for="apellidos">Apellidos</label>
                <input type="text" class="form-control" id="apellidos" name="apellidos" value="' . $alum["Apellidos"] . '">
              </div>
              <div class="form-group col-md-3">
                <label for="nif">NIF</label>
                <input type="text" class="form-control" id="nif" name="nif" value="' . $alum["Nif"] . '">
              </div>
              <div class="form-group col-md-2">
                <label for="fechaNacimiento">Fecha Nacimiento</label>
                <input type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento" value="' . $alum["FechaNacimiento"] . '">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-5">
                <label for="domicilio">Domicilio</label>
                <input type="text" class="form-control" id="domicilio" name="domicilio" value="' . $alum["Domicilio"] . '">
              </div>
              <div class="form-group col-md-5">
                <label for="poblacion">Municipio</label>
                <input type="text" class="form-control" id="municipio" name="municipio" value="' . $alum["Municipio"] . '">
              </div>
              <div class="form-group col-md-2">
                <label for="codPostal">Codigo Postal</label>
                <input type="text" class="form-control" id="codPostal" name="codPostal" value="' . $alum["CodigoPostal"] . '">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="email">email</label>
                <input type="email" class="form-control" id="email" name="email" value="' . $alum["emailPrincipal"] . '" required>
              </div>
              <div class="form-group col-md-6">
                <label for="email2">email 2</label>
                <input type="email" class="form-control" id="email2" name="email2" value="' . $alum["emailTutor2"] . '">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-12">
                <label for="observaciones">Observaciones</label>
                <input type="textarea" class="form-control" id="observaciones" name="observaciones" value="' . $alum["Observaciones"] . '">
              </div>
            </div>            

          </div>  





          <div id="matricula" class="container-fluid mt-3">
            <div class="form-row">
              <div class="form-group col-md-3">
                <label for="cbxMttoCentros">Centro</label>
                <select id="cbxMttoCentros" class="form-control" name="centro" required> ' . GetHtmlFillSelectCentros($mat["idCentro"]) . '
                </select>
              </div>
              <div class="form-group col-md-2">
                <label for="cbxMttoAulas">Aula</label>
                <select id="cbxMttoAulas" class="form-control" name="aula" required> ' . GetHtmlFillSelectAulas($mat["idAula"]) . '
                </select>
              </div>
              <div class="form-group col-md-2">
                <label for="cbxMttoProfesores">Profesor</label>
                <select id="cbxMttoProfesores" class="form-control" name="profesor" required> ' . GetHtmlFillSelectProfesores($mat["idProfesor"]) . '
                </select>
              </div>
              <div class="form-group col-md-2">
                <label for="cbxMttoCursos">Curso</label>
                <select id="cbxMttoCursos" class="form-control" name="curso" required> ' . GetHtmlFillSelectCursos($mat["curso"], $idPromocion) . '
                </select>
              </div>
              <div class="form-group col-md-2">
                <label for="cbxMttoHorario">Horario</label>
                <select id="cbxMttoHorario" class="form-control" name="horario" required> ' . GetHtmlFillSelectHorarios($mat["horario"], $idPromocion) . '
                </select>
              </div>
              <div class="form-group col-md-2">
                <label for="cbxMttoDiasSemana">Dias/Semana</label>
                <select id="cbxMttoDiasSemana" class="form-control" name="dias" required>
                  <option value="M-W"' . (($mat["dias"]=='M-W') ? 'selected' : '') . '>Lunes y Miércoles</option>
                  <option value="T-TH"' . (($mat["dias"]=='T-TH') ? 'selected' : '') . '>Martes y Jueves</option>
                </select>
              </div>

              <div class="form-check form-check-inline align-content-lg-center ml-5">
                <input class="form-check-input" 
                      type="checkbox" 
                      name="seEnviaCorreo" 
                      id="seEnviaCorreo" 
                      value="1" ' . (($mat["comunicarAsistencia"]) ? 'checked' : '') . '>
                <label class="form-check-label" 
                      for="seEnviaCorreo">
                    Se le envía correo electrónico para notificarle su modo de asistencia
                </label>
              </div>

            </div>

            <div class="form-row">
              <div class="form-group col-md-12">
                <label for="url">url enlace zoom</label>
                <input type="text" class="form-control" id="url" name="url" value="' . $mat["url"] . '">
              </div>
            </div>

            <button type="submit" class="btn btn-primary">Guardar</button>
            <button type="button" class="btn btn-secondary" onclick="PanelAsistencias()">VOLVER AL PANEL</button>

          </div>
        </form>';

echo $html;









