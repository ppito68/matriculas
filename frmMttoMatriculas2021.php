<!-- Funciones para llenar las opciones de las listas de seleccion -->
<?php require_once("FunctionsGetHtmlFillSelects.php"); ?>
    
<script src="selects.js"></script>
<script src="frmMttoMatriculas2021.js"></script>


<!-- Contenedor de combo de Centros y Aulas -->
<div class="container-fluid" id="opciones">
    <div class="row">
        
        <!-- Lista de seleccion de Centro -->
        <div class="col-4 input-group mt-3">
            <div class="input-group-prepend">
                <label class="input-group-text" for="cbxCentros">Centros:</label>
            </div>
            <select class="custom-select" id="cbxCentros" name="cbxCentros" ><?php echo GetHtmlFillSelectCentros(0); ?>
            </select>
        </div>

        <!-- lista de Seleccion de Aula -->
        <div class="col-2 input-group mt-3">
            <div class="input-group-prepend">
                <label class="input-group-text" for="cbxAulas">Aulas:</label>
            </div>
            <select class="custom-select" id="cbxAulas" name="cbxAulas" >
            </select>
        </div>
        
        
        <div class="col-3" id="cantidadSolicitudes"></div> 
        <!-- <div class="col-2"><button type="button" onclick="lista()">Cambiar Visualización</button></div> -->
    </div>
</div>

<!-- contendor de combos dependientes de Centros y Aulas -->
<div class="container-fluid" id="combos">
    <div class="row">

        <!-- Lista de seleccion de curso -->
        <div class="col-3 input-group mt-3">
            <div class="input-group-prepend">
                <label class="input-group-text" for="cbxCursos">Cursos</label>
            </div>
            <select class="custom-select" id="cbxCursos" name="cbxCursos"></select>
        </div>
        
        <!-- Lista de seleccion de Dias / Semana -->
        <div class="col-5 input-group mt-3">
            <div class="input-group-prepend">
                <label class="input-group-text" for="cbxDias">Dias/Semana</label>
            </div>
            <select class="custom-select" id="cbxDias" name="cbxDias"></select>
        </div>
        
        <!-- Lista de seleccion de Horario -->
        <div class="col-4 input-group mt-3">
            <div class="input-group-prepend">
                <label class="input-group-text" for="cbxHorarios">Horario</label>
            </div>
            <select class="custom-select" id="cbxHorarios" name="cbxHorarios"></select>
        </div>

    </div>
</div>
<hr>

<!-- Contenedor de MAtriculas -->
<div class="container-fluid" id="matriculas"></div>

<!-- Contenedor Estadistico -->
<div style="display:none;" class="container-fluid" id="ContenedorPorCursos">
    <div class="row">
        <div class="col-5 align-items-center">
            <div class="container"><h4>POR CURSOS/DIAS/HORARIO</h4></div>
            <div class="container" style="font-size: small">Los alumnos que NO hayan seleccionado días y horario NO aparecen en estos recuentos</div>
            <div class="container" id="solicitudesPorCursos"></div>
        </div>
        <div class="col-5 align-items-center">
            <div class="container"><h4>CUADRANTE</h4></div>
            <div class="container" style="font-size: small">Los alumnos que NO hayan seleccionado días y horario NO aparecen en estos recuentos</div>
            <div class="container" id="cuadrante"></div>
        </div>
        <div class="col-2 align-items-center">
            <div class="container"><h5>Selección incompleta</h5></div>
            <div class="container" id="solicitudesSinDias"></div>
        </div>

    </div>
</div> 