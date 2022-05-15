
<?php 
require_once("Db.php"); 

// encolumnado de notas units
define("ANCHO_COL_XS", 'col-sm-12');
define("ANCHO_COL_SM", 'col-sm-6');
define("ANCHO_COL_MD", 'col-md-2');
define("ANCHO_COL_LG", 'col-lg-1');
define("ANCHO_COL_XL", 'col-xl-1');

$idMatricula = $_GET["idMatricula"];
//$idAlumno = $_GET["idAlumno"];

// Obtiene las notas
$resultGetNotas = GetNotas($idMatricula);
$notas = $resultGetNotas->fetch(PDO::FETCH_ASSOC);

// obtiene el alumno
$resAlum = GetAlumnoPorMatricula($idMatricula);
$alumno = $resAlum->fetch(PDO::FETCH_ASSOC);

// Calcula la nota media enrtre todas las habilidades
$notaFinal1 = ( $notas["speaking1"] + $notas["listening1"] + $notas["writing1"] + $notas["reading1"] + $notas["examenEscrito1"]  ) / 5;
$notaFinal2 = ( $notas["speaking2"] + $notas["listening2"] + $notas["writing2"] + $notas["reading2"] + $notas["examenEscrito2"]  ) / 5;

// Genera el formulario de mtto de las notas
$html = '<style type="text/css">
          input {
            font-weight:bold;
            text-align: center;
          }
        </style>

        <SCRIPT>
          function calculaMedia(numExamen) {
            var notaFinal = document.getElementById("notaFinal"+numExamen);
            const espeak = parseFloat(document.getElementById("speaking"+numExamen).value);
            const listen = parseFloat(document.getElementById("listening"+numExamen).value);
            const writin = parseFloat(document.getElementById("writing"+numExamen).value);
            const readin = parseFloat(document.getElementById("reading"+numExamen).value);
            const exaesc = parseFloat(document.getElementById("examenEscrito"+numExamen).value);

            notaFinal.value = (  (espeak + listen + writin + readin + exaesc) / 5  ).toFixed(2) ;
          }

          // no usado, pero lo dejo aqui.
          function round(num, decimales = 2) {
            var signo = (num >= 0 ? 1 : -1);
            num = num * signo;
            if (decimales === 0) //con 0 decimales
                return signo * Math.round(num);
            // round(x * 10 ^ decimales)
            num = num.toString().split("e");
            num = Math.round(+(num[0] + "e" + (num[1] ? (+num[1] + decimales) : decimales)));
            // x * 10 ^ (-decimales)
            num = num.toString().split("e");
            return signo * (num[0] + "e" + (num[1] ? (+num[1] - decimales) : -decimales));
          }

          function conFormato2Dec(num){
            return parseFloat(num).toFixed(2);
          }

        </SCRIPT>

        <form name="formNotas" action="GrabacionNotas.php" method="post">

          <input name="idMatricula" id="idMatricula" type="hidden" value="' . $idMatricula . '">
          <input name="enviado1" id="enviado1" type="hidden" value="' . $notas["enviado1"] . '">
          <input name="enviado2" id="enviado2" type="hidden" value="' . $notas["enviado2"] . '">

          <ul class="list-group">
          
            <li class="list-group-item active">' . $alumno["Nombre"] . ' ' . $alumno["Apellidos"] . '</li>
          
            <li class="list-group-item">  N O T E S  

              <div class="row mb-3">
                <div class="col-sm-3"></div>
                <div class="col-sm-3"></div>
                <div class="col-sm-2">
                  <label>Exam 1</label>
                </div>
                <div class="col-sm-2">
                  <label>Exam 2</label>
                </div>
              </div>


              <div class="row sm-3">
                <div class="col-sm-3"></div>
                <label for="reading1" class="col-sm-3 col-form-label">Reading</label>
                <div class="col-sm-2">
                  <input onblur="this.value = conFormato2Dec(this.value)" onchange="calculaMedia(1)" type="number" step="0.05" class="form-control form-control-sm" id="reading1" name="reading1" value="' . $notas["reading1"] . '">
                </div>
                <div class="col-sm-2">
                  <input onblur="this.value = conFormato2Dec(this.value)" onchange="calculaMedia(2)" type="number" step="0.05" class="form-control form-control-sm" id="reading2"  name="reading2" value="' . $notas["reading2"] . '">
                </div>                                
              </div>

              <div class="row sm-3">
                <div class="col-sm-3"></div>
                <label for="expresionEscrita1" class="col-sm-3 col-form-label">Writing</label>
                <div class="col-sm-2">
                  <input onblur="this.value = conFormato2Dec(this.value)" onchange="calculaMedia(1)" type="number" step="0.05" class="form-control form-control-sm" id="writing1" name="writing1" value="' . $notas["writing1"] . '">
                </div>
                <div class="col-sm-2">
                  <input onblur="this.value = conFormato2Dec(this.value)" onchange="calculaMedia(2)" type="number" step="0.05" class="form-control form-control-sm" id="writing2" name="writing2" value="' . $notas["writing2"] . '">
                </div>                                
              </div>

              <div class="row sm-3">
                <div class="col-sm-3"></div>
                <label for="listening1" class="col-sm-3 col-form-label">Listening</label>
                <div class="col-sm-2">
                  <input onblur="this.value = conFormato2Dec(this.value)" onchange="calculaMedia(1)" type="number" step="0.05" class="form-control form-control-sm" id="listening1" name="listening1" value="' . $notas["listening1"] . '">
                </div>
                <div class="col-sm-2">
                  <input onblur="this.value = conFormato2Dec(this.value)" onchange="calculaMedia(2)" type="number" step="0.05" class="form-control form-control-sm" id="listening2" name="listening2" value="' . $notas["listening2"] . '">
                </div>                
              </div>

              <div class="row sm-3">
                <div class="col-sm-3"></div>
                <label for="speaking1" class="col-sm-3 col-form-label">Speaking</label>
                <div class="col-sm-2">
                  <input onblur="this.value = conFormato2Dec(this.value)" onchange="calculaMedia(1)" type="number" step="0.05" class="form-control form-control-sm" id="speaking1" name="speaking1" value="' . $notas["speaking1"] . '">
                </div>
                <div class="col-sm-2">
                  <input onblur="this.value = conFormato2Dec(this.value)" onchange="calculaMedia(2)" type="number" step="0.05" class="form-control form-control-sm" id="speaking2" name="speaking2" value="' . $notas["speaking2"] . '">
                </div>
              </div>


              <div class="row sm-3">
                <div class="col-sm-3"></div>
                <label for="examenEscrito1" class="col-sm-3 col-form-label">Written exam</label>
                <div class="col-sm-2">
                  <input onblur="this.value = conFormato2Dec(this.value)" onchange="calculaMedia(1)" type="number" step="0.05" class="form-control form-control-sm" id="examenEscrito1" name="examenEscrito1" value="' . $notas["examenEscrito1"] . '">
                </div>
                <div class="col-sm-2">
                  <input onblur="this.value = conFormato2Dec(this.value)" onchange="calculaMedia(2)" type="number" step="0.05" class="form-control form-control-sm" id="examenEscrito2" name="examenEscrito2" value="' . $notas["examenEscrito2"] . '">
                </div>                                
              </div>

              <div class="row sm-3">
                <div class="col-sm-3"></div>
                <label for="notaFinal2" class="col-sm-3 col-form-label">Final note</label>
                <div class="col-sm-2">
                  <input type="number" step="0.05" class="form-control form-control-sm" id="notaFinal1" name="notaFinal1" value="' . number_format($notaFinal1, 2) . '" disabled>
                </div>
                <div class="col-sm-2">
                  <input type="number" step="0.05" class="form-control form-control-sm" id="notaFinal2" name="notaFinal2" value="' .number_format($notaFinal2, 2) . '" disabled>
                </div>                                
              </div>
              
              
            </li>


            <li class="list-group-item">BRIEFING NOTES
            
              <div class="row sm-3">
                <div class="col-sm-3"></div>
                <label for="participacion1" class="col-sm-3 col-form-label">Participation</label>
                <div class="col-sm-2">
                  <input onblur="this.value = conFormato2Dec(this.value)" type="number" step="0.05" class="form-control form-control-sm" id="participacion1" name="participacion1" value="' . $notas["participacion1"] . '">
                </div>
                <div class="col-sm-2">
                  <input onblur="this.value = conFormato2Dec(this.value)" type="number" step="0.05" class="form-control form-control-sm" id="participacion2" name="participacion2" value="' . $notas["participacion2"] . '">
                </div>                                
              </div>

              <div class="row sm-3">
                <div class="col-sm-3"></div>
                <label for="comportamiento1" class="col-sm-3 col-form-label">Behaviour</label>
                <div class="col-sm-2">
                  <input onblur="this.value = conFormato2Dec(this.value)" type="number" step="0.05" class="form-control form-control-sm" id="comportamiento1" name="comportamiento1" value="' . $notas["comportamiento1"] . '">
                </div>
                <div class="col-sm-2">
                  <input onblur="this.value = conFormato2Dec(this.value)" type="number" step="0.05" class="form-control form-control-sm" id="comportamiento2" name="comportamiento2" value="' . $notas["comportamiento2"] . '">
                </div>                                
              </div>

              <div class="row sm-3">
                <div class="col-sm-3"></div>
                <label for="examenOral" class="col-sm-3 col-form-label">Oral exam</label>
                <div class="col-sm-2"></div>
                <div class="col-sm-2">
                  <input onblur="this.value = conFormato2Dec(this.value)" type="number" step="0.05" class="form-control form-control-sm" id="examenOral" name="examenOral" value="' . $notas["examenOral2"] . '">
                </div>                                
              </div>

              

        
            </li>
            
            <li class="list-group-item">UNIT TEST AND COMMENTS
            
              <div class="row sm-3">
                
                <div class="' . ANCHO_COL_SM . ' ' . ANCHO_COL_MD . ' ' . ANCHO_COL_LG . '">
                  <label for="ut1" class="form-label">1</label>
                  <input onblur="this.value = conFormato2Dec(this.value)" type="number" class="form-control form-control-sm" id="unit1" name="unit1" value="' . $notas["unit1"] . '">
                </div>
                
                <div class="' . ANCHO_COL_SM . ' ' . ANCHO_COL_MD . ' ' . ANCHO_COL_LG . '">
                  <label for="ut2" class="form-label">2</label>
                  <input onblur="this.value = conFormato2Dec(this.value)" type="number" class="form-control form-control-sm" id="unit2" name="unit2" value="' . $notas["unit2"] . '">
                </div>
              
                <div class="' . ANCHO_COL_SM . ' ' . ANCHO_COL_MD . ' ' . ANCHO_COL_LG . '">
                  <label for="ut3" class="form-label">3</label>
                  <input onblur="this.value = conFormato2Dec(this.value)" type="number" class="form-control form-control-sm" id="unit3" name="unit3" value="' . $notas["unit3"] . '">
                </div>

                <div class="' . ANCHO_COL_SM . ' ' . ANCHO_COL_MD . ' ' . ANCHO_COL_LG . '">
                  <label for="ut4" class="form-label">4</label>
                  <input onblur="this.value = conFormato2Dec(this.value)" type="number" class="form-control form-control-sm" id="unit4" name="unit4" value="' . $notas["unit4"] . '">
                </div>
              
                <div class="' . ANCHO_COL_SM . ' ' . ANCHO_COL_MD . ' ' . ANCHO_COL_LG . '">
                  <label for="ut5" class="form-label">5</label>
                  <input onblur="this.value = conFormato2Dec(this.value)" type="number" class="form-control form-control-sm" id="unit5" name="unit5" value="' . $notas["unit5"] . '">
                </div>

                <div class="' . ANCHO_COL_SM . ' ' . ANCHO_COL_MD . ' ' . ANCHO_COL_LG . '">
                  <label for="ut6" class="form-label">6</label>
                  <input onblur="this.value = conFormato2Dec(this.value)" type="number" class="form-control form-control-sm" id="unit6" name="unit6" value="' . $notas["unit6"] . '">
                </div>

                <div class="' . ANCHO_COL_SM . ' ' . ANCHO_COL_MD . ' ' . ANCHO_COL_LG . '">
                  <label for="ut7" class="form-label">7</label>
                  <input onblur="this.value = conFormato2Dec(this.value)" type="number" class="form-control form-control-sm" id="unit7" name="unit7" value="' . $notas["unit7"] . '">
                </div>

                <div class="' . ANCHO_COL_SM . ' ' . ANCHO_COL_MD . ' ' . ANCHO_COL_LG . '">
                  <label for="ut8" class="form-label">8</label>
                  <input onblur="this.value = conFormato2Dec(this.value)" type="number" class="form-control form-control-sm" id="unit8" name="unit8" value="' . $notas["unit8"] . '">
                </div>
                
                <div class="' . ANCHO_COL_SM . ' ' . ANCHO_COL_MD . ' ' . ANCHO_COL_LG . '">
                  <label for="ut9" class="form-label">9</label>
                  <input onblur="this.value = conFormato2Dec(this.value)" type="number" class="form-control form-control-sm" id="unit9" name="unit9" value="' . $notas["unit9"] . '">
                </div>

                <div class="' . ANCHO_COL_SM . ' ' . ANCHO_COL_MD . ' ' . ANCHO_COL_LG . '">
                  <label for="ut10" class="form-label">10</label>
                  <input onblur="this.value = conFormato2Dec(this.value)" type="number" class="form-control form-control-sm" id="unit10" name="unit10" value="' . $notas["unit10"] . '">
                </div>

                <div class="' . ANCHO_COL_SM . ' ' . ANCHO_COL_MD . ' ' . ANCHO_COL_LG . '">
                  <label for="ut11" class="form-label">11</label>
                  <input onblur="this.value = conFormato2Dec(this.value)" type="number" class="form-control form-control-sm" id="unit11" name="unit11" value="' . $notas["unit11"] . '">
                </div>

                <div class="' . ANCHO_COL_SM . ' ' . ANCHO_COL_MD . ' ' . ANCHO_COL_LG . '">
                  <label for="ut12" class="form-label">12</label>
                  <input onblur="this.value = conFormato2Dec(this.value)" type="number" class="form-control form-control-sm" id="unit12" name="unit12" value="' . $notas["unit12"] . '">
                </div>

                <div class="' . ANCHO_COL_SM . ' ' . ANCHO_COL_MD . ' ' . ANCHO_COL_LG . '">
                  <label for="ut13" class="form-label">13</label>
                  <input onblur="this.value = conFormato2Dec(this.value)" type="number" class="form-control form-control-sm" id="unit13" name="unit13" value="' . $notas["unit13"] . '">
                </div>

                <div class="' . ANCHO_COL_SM . ' ' . ANCHO_COL_MD . ' ' . ANCHO_COL_LG . '">
                  <label for="ut14" class="form-label">14</label>
                  <input onblur="this.value = conFormato2Dec(this.value)" type="number" class="form-control form-control-sm" id="unit14" name="unit14" value="' . $notas["unit14"] . '">
                </div>

                <div class="' . ANCHO_COL_SM . ' ' . ANCHO_COL_MD . ' ' . ANCHO_COL_LG . '">
                  <label for="ut15" class="form-label">15</label>
                  <input onblur="this.value = conFormato2Dec(this.value)" type="number" class="form-control form-control-sm" id="unit15" name="unit15" value="' . $notas["unit15"] . '">
                </div>

                <div class="' . ANCHO_COL_SM . ' ' . ANCHO_COL_MD . ' ' . ANCHO_COL_LG . '">
                  <label for="ut16" class="form-label">16</label>
                  <input onblur="this.value = conFormato2Dec(this.value)" type="number" class="form-control form-control-sm" id="unit16" name="unit16" value="' . $notas["unit16"] . '">
                </div>

                <div class="' . ANCHO_COL_SM . ' ' . ANCHO_COL_MD . ' ' . ANCHO_COL_LG . '">
                  <label for="ut17" class="form-label">17</label>
                  <input onblur="this.value = conFormato2Dec(this.value)" type="number" class="form-control form-control-sm" id="unit17" name="unit17" value="' . $notas["unit17"] . '">
                </div>

                <div class="' . ANCHO_COL_SM . ' ' . ANCHO_COL_MD . ' ' . ANCHO_COL_LG . '">
                  <label for="ut18" class="form-label">18</label>
                  <input onblur="this.value = conFormato2Dec(this.value)" type="number" class="form-control form-control-sm" id="unit18" name="unit18" value="' . $notas["unit18"] . '">
                </div>

               <div class="' . ANCHO_COL_SM . ' ' . ANCHO_COL_MD . ' ' . ANCHO_COL_LG . '">
                  <label for="ut19" class="form-label">19</label>
                  <input onblur="this.value = conFormato2Dec(this.value)" type="number" class="form-control form-control-sm" id="unit19" name="unit19" value="' . $notas["unit19"] . '">
                </div>

                <div class="' . ANCHO_COL_SM . ' ' . ANCHO_COL_MD . ' ' . ANCHO_COL_LG . '">
                  <label for="ut20" class="form-label">20</label>
                  <input onblur="this.value = conFormato2Dec(this.value)" type="number" class="form-control form-control-sm" id="unit20" name="unit20" value="' . $notas["unit20"] . '">
                </div>

              </div>
            
            </li>

            <li class="list-group-item">

              <div class="row sm-3">

                <div class="col-sm-12">
                  <label for="comentarios" class="form-label">Comments</label>
                  <textarea class="form-control form-control-sm" id="comentarios" name="comentarios" rows="5">' . $notas["comentarios"] . '</textarea>
                </div>

              </div>

            </li>

          </ul>

          <div>
            <button type="button" class="btn btn-primary" onclick="GrabaNotas()">SAVE</button>
            <button type="button" class="btn btn-primary" onclick="EnviarNotas(' . $idMatricula . ', \'' . $alumno["Nombre"] . ' ' . $alumno["Apellidos"] . '\', \'' . $alumno["emailPrincipal"] . '\', 0)">Enviar Notas</button>
            <button type="button" class="btn btn-primary" onclick="EnviarNotas(' . $idMatricula . ', \'' . $alumno["Nombre"] . ' ' . $alumno["Apellidos"] . '\', \'' . $alumno["emailPrincipal"] . '\', 1)">Mostrar en pdf</button>
            <button type="button" class="btn btn-secondary" onclick="PanelAsistencias()">VOLVER A LA LISTA DE ALUMNOS</button>
            
          </div>

        </form>';

echo $html;









