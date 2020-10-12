
<?php 
require_once("FunctionsGetHtmlFillSelects.php"); 
require_once("Db.php"); 

$numero = $_GET["numero"];
$result = CovGetAlumno($numero);
$alum=$result->fetch(PDO::FETCH_ASSOC);

  
$html = '<script>
            $("#cbxMttoCentros").change(function() {
              var idCentro=$("#cbxMttoCentros").val();
              CargaComboAulasMttoAlumnos(idCentro, ' . $alum['idAula'] . ');
            });
        </script>
        <form action="GrabacionNuevoAlumno.php" method="post">
          <div class="container-fluid mt-3">
            <input type="hidden" name="idAula" id="idAula" value="' . $alum['idAula'] . '">
            <input type="hidden" name="curso" id="curso" value="' . $alum['curso'] . '">
            <div class="form-row">
              <div class="form-group col-md-1">
                <label for="numeroAlumno">NºAlumno</label>
                <input type="text" class="form-control" id="numero" name="numero" value="' . $alum[numero] . '" required>
              </div>
              <div class="form-group col-md-2">
                <label for="nombreAlumno">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="' . $alum[nombre] . '" required>
              </div>
              <div class="form-group col-md-4">
                <label for="Apellidos">Apellidos</label>
                <input type="text" class="form-control" id="apellidos" name="apellidos" value="' . $alum[apellidos] . '">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="email">email</label>
                <input type="email" class="form-control" id="email" name="email" value="' . $alum[email] . '" required>
              </div>
              <div class="form-group col-md-6">
                <label for="email2">email 2</label>
                <input type="email" class="form-control" id="email2" name="email2" value="' . $alum[email2] . '">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-3">
                <label for="cbxMttoCentros">Centro</label>
                <select id="cbxMttoCentros" class="form-control" name="centro" required>
                                                <!-- php echo GetHtmlFillSelectCentros($alum[centro]) -->
                </select>
              </div>
              <div class="form-group col-md-2">
                <label for="cbxMttoAulas">Aula</label>
                <select id="cbxMttoAulas" class="form-control" name="aula" required>
                                                <!-- php: echo GetHtmlFillSelectAulas($alum[idAula]) -->
                </select>
              </div>
              <div class="form-group col-md-2">
                <label for="cbxMttoProfesores">Profesor</label>
                <select id="cbxMttoProfesores" class="form-control" name="profesor" required>
                </select>
              </div>
              <div class="form-group col-md-2">
                <label for="cbxMttoCursos">Curso</label>
                <select id="cbxMttoCursos" class="form-control" name="curso" required>
                                            <!-- php echo GetHtmlFillSelectCursos($alum[curso]) -->
                </select>
              </div>
              <div class="form-group col-md-2">
                <label for="cbxMttoHorario">Horario</label>
                <select id="cbxMttoHorario" class="form-control" name="horario" required>
                                            <!-- php echo GetHtmlFillSelectHorarios($alum[horario]) -->
                </select>
              </div>
              <div class="form-group col-md-2">
                <label for="cbxMttoDiasSemana">Dias/Semana</label>
                <select id="cbxMttoDiasSemana" class="form-control" name="dias" required>
                  <option value="M-W"' . (($alum[dias]=='M-W') ? 'selected' : '') . '>Lunes y Miércoles</option>
                  <option value="T-TH"' . (($alum[dias]=='T-TH') ? 'selected' : '') . '>Martes y Jueves</option>
                </select>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-12">
                <label for="url">url enlace zoom</label>
                <input type="text" class="form-control" id="url" name="url" value="' . $alum[url] . '">
              </div>
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
            <button type="button" class="btn btn-secondary" onclick="PanelAsistencias()">VOLVER AL PANEL</button>
          </div>
        </form>';

echo $html;









