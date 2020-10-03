<!-- Funciones para llenar las opciones de las listas de seleccion -->
<?php require_once("FunctionsGetHtmlFillSelects.php"); ?>
    
<!-- Contenedor de combos -->
<div class="container-fluid" id="opciones">
    <div class="row mt-1">
        <table class="table-sm table-borderless">
            <thead class="thead-light">
                <tr>
                    <th scope="col">
                        <div class="col-auto input-group">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="cbxNotifCentros">Centro:</label>
                            </div>
                            <select class="custom-select" id="cbxNotifCentros" name="cbxNotifCentros" >
                                <?php echo GetHtmlFillSelectCentros(0); ?>
                            </select>
                        </div>
                    </th>
                    <th scope="col">
                        <div class="col-auto input-group">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="cbxNotifFechas">Fecha:</label>
                            </div>
                            <select class="custom-select" id="cbxNotifFechas" name="cbxNotifFechas">
                                <?php echo GetHtmlFillSelectFechas(1); ?> <!--el 1 es la promocion, y lo dejo de momento asi para salir del paso -->
                            </select>
                        </div>                    
                    </th>
                    <th scope="col">
                        <div class="col-auto input-group">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="cbxNotifAulas">Aula:</label>
                            </div>
                            <select class="custom-select" id="cbxNotifAulas" name="cbxNotifAulas">
                            </select>
                        </div>
                    </th>
                    <th scope="col">
                        <div class="col-auto input-group">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="cbxNotifCursos">Curso:</label>
                            </div>
                            <select class="custom-select" id="cbxNotifCursos" name="cbxNotifCursos">
                            </select>
                        </div>
                    </th>
                    <th scope="col">
                        <div class="col-auto input-group">                    
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="cbxNotifHorarios">Horario:</label>
                            </div>
                            <select class="custom-select" id="cbxNotifHorarios" name="cbxNotifHorarios">
                            </select>
                        </div>                        
                    </th>
                </tr>
            </thead>

            <!-- Contenedor de botones -->
            <tbody>
                <tr>
                    <td>
                        <button onclick="GenerarAsistencias()" type="button" class="btn btn-primary" id="btnGenerar" disabled>Generar</button>            
                        <button onclick="EnviarEmails()" type="button" class="btn btn-success" id="btnEnviarEmail" disabled>Enviar Email</button>
                        <button onclick="EliminarAsistencias()" type="button" class="btn btn-danger">Eliminar</button>

                        <!-- <a href="frmMttoAlumnos2021.php">Añadir Alumno</a> -->
                        <button onclick="MttoAlumno(0)" type="button" class="btn btn-info">Añadir Alumno</button>

                    </td>

                    <!-- Se deja una columna de la tabla vacía para haer coincidir la leyenda del aforo debajo del combo del aula -->
                    <td>
                    </td>

                    <td>
                        <p id="aforo" class="small"></p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


<hr>

<!-- Contenedor del panel de asistencias -->
<div class="container-fluid" id="alumnosContainer"></div>
