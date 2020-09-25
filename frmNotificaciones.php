<!-- Funciones para llenar las opciones de las listas de seleccion -->
<?php require_once("FunctionsGetHtmlFillSelects.php"); ?>
    
<script src="frmNotificaciones.js"></script>


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
                        <a href="frmMttoAlumnos2021.php">Añadir Alumno</a>
                    </td>
                    <td>

                    <!-- <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Cras justo odio
                            <span class="badge badge-primary badge-pill">14</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Dapibus ac facilisis in
                            <span class="badge badge-primary badge-pill">2</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Morbi leo risus
                            <span class="badge badge-primary badge-pill">1</span>
                        </li>
                    </ul> -->


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

<!-- Contenedor de MAtriculas -->
<div class="container-fluid" id="alumnosContainer"></div>
