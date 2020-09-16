<!-- Funciones para llenar las opciones de las listas de seleccion -->
<?php require_once("FunctionsGetHtmlFillSelects.php"); ?>
    
<script src="frmNotificaciones.js"></script>


<!-- Contenedor de combos -->
<div class="container-fluid" id="opciones">
    <div class="row">
        
        <!-- Lista de seleccion de la fecha de impartición de la clase -->
        <div class="col-3 input-group mt-3">
            <div class="input-group-prepend">
                <label class="input-group-text" for="cbxNotifFechas">Fecha:</label>
            </div>
            <select class="custom-select" id="cbxNotifFechas" name="cbxNotifFechas">
                <?php echo GetHtmlFillSelectFechas(1); ?> <!--el 1 lo dejo de momento asi para salir del paso -->
            </select>
        </div>

        <!-- Lista de seleccion de Centro -->
        <div class="col-3 input-group mt-3">
            <div class="input-group-prepend">
                <label class="input-group-text" for="cbxNotifCentros">Centro:</label>
            </div>
            <select class="custom-select" id="cbxNotifCentros" name="cbxNotifCentros" >
                <!-- <?php //echo GetHtmlFillSelectCentros(0); ?> -->
            </select>
        </div>

        <!-- Lista de seleccion de la fecha de las aulas -->
        <div class="col-2 input-group mt-3">
            <div class="input-group-prepend">
                <label class="input-group-text" for="cbxNotifAulas">Aula:</label>
            </div>
            <select class="custom-select" id="cbxNotifAulas" name="cbxNotifAulas">
            </select>
        </div>

         <!-- Lista de seleccion de cursos -->
         <div class="col-2 input-group mt-3">
            <div class="input-group-prepend">
                <label class="input-group-text" for="cbxNotifCursos">Curso:</label>
            </div>
            <select class="custom-select" id="cbxNotifCursos" name="cbxNotifCursos">
            </select>
        </div>

         <!-- Lista de seleccion de cursos -->
         <div class="col-2 input-group mt-3">
            <div class="input-group-prepend">
                <label class="input-group-text" for="cbxNotifHorarios">Horario:</label>
            </div>
            <select class="custom-select" id="cbxNotifHorarios" name="cbxNotifHorarios">
            </select>
        </div>


    </div>
</div>

<!-- Contenedor de botones -->
<div class="container-fluid mt-3 ml-3" id="opciones" >
    <div class="row">
        <div class="btn-group" role="group" aria-label="Basic example">
        <!-- <div class="col-1 input-group mt-3"> -->
            <button onclick="GenerarAsistencias()" type="button" class="btn btn-primary">Generar</button>
        <!-- </div> -->

        <!-- <div class="col-2 input-group mt-3"> -->
            <button onclick="EnviarEmails()" type="button" class="btn btn-success">Enviar Email</button>
        <!-- </div> -->

        <!-- <div class="col-1 input-group mt-3"> -->
            <button type="button" onclick="EliminarAsistencias()" class="btn btn-danger">Eliminar</button>
        </div>
    </div>    
</div>
    
</div>
</div>

<hr>

<!-- Contenedor de MAtriculas -->
<div class="container-fluid" id="alumnosContainer"></div>
