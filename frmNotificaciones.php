<!-- Funciones para llenar las opciones de las listas de seleccion -->
<?php require_once("FunctionsGetHtmlFillSelects.php"); ?>
    
<!-- Contenedor de combos -->
<div class="container-fluid" id="opciones">
    <div class="row mt-1">
        <table class="table-sm table-borderless">
            <thead class="thead-light">

                <tr>
                
                </tr>

                <tr>
                    <!-- <th>
                        <div>
                            <img src="./img/city.png" width="75px" height="54px"> 
                        </div>
                    </th> -->
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

                    <!-- <th scope="col">
                        <div class="col-auto input-group">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="cbxNotifCentros">Días/Semana:</label>
                            </div>
                            <select class="custom-select" id="cbxNotifCentros" name="cbxNotifCentros" >
                                <?php //echo GetHtmlFillDiasSemana(0); ?>
                            </select>
                        </div>
                    </th> -->



                    <th scope="col">
                        <div class="col-auto input-group">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="cbxNotifDiasSemana">Días/Semana:</label>
                            </div>
                            <select class="custom-select" id="cbxNotifDiasSemana" name="cbxNotifDiasSemana" >
                                <?php echo GetHtmlFillDiasSemana(0); ?>
                            </select>
                        </div>
                        <div class="col-auto input-group mt-1">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="cbxNotifFechas">Fecha:</label>
                            </div>
                            <select class="custom-select" id="cbxNotifFechas" name="cbxNotifFechas">
                                 <?php echo GetHtmlFillSelectFechas(1, [0]); ?> <!-- el 1 es la promocion, y lo dejo de momento asi para salir del paso -->
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
                    
                    <!-- colspan=2 lo que hace es abarcar dos celdas de la tabla en horizontal para que quepan los botones  -->
                    <td colspan="2">

                        <button onclick="GenerarAsistencias()" type="button" class="btn btn-primary" id="btnGenerar" disabled>Generar</button>            
                        <button onclick="EliminarAsistencias()" type="button" class="btn btn-danger">Eliminar generados</button>
                        <button onclick="EnviarEmails()" type="button" class="btn btn-primary" id="btnEnviarEmail" disabled>Enviar Email</button>
                        <button onclick="MttoAlumno(0)" type="button" class="btn btn-primary">Añadir Alumno</button>
                        <!-- <button onclick="MttoAlumno(0)" type="button" class="btn btn-primary">Enviar Notas</button> -->
                        <div id="cantidadAlumnos" class="badge badge-primary text-wrap" style="width: 6rem;"></div>
                        <!-- <i class="fas fa-sync-alt fa-spin fa-5x mr-1"></i> -->

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
