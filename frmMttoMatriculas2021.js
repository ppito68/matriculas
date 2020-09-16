$(document).ready(function() {

    $('#cbxCentros').change(function() {
        GetListaAulas();
        GetListaCursos();

        // ActualizaMatriculas();
        // ActulizaSolicitudesPorCursos();
        // ActualizaCuadrante();
        // ActualizaSolicitudesSinDiasSeleccionado();
        // ActualizaCantidadSolicitudes();
    });

    $('#cbxAulas').change(function() {
        GetListaCursos();
    });

    $('#cbxCursos').change(function() {
        GetListaDias();
        //ActualizaMatriculas();
    });

    $('#cbxDias').change(function() {
        GetListaHorarios();
        ActualizaMatriculas();
    });

    $('#cbxHorarios').change(function() {
        ActualizaMatriculas();
    });

})

function lista() {
    alternaPaneles();
    //ActulizaSolicitudesPorCursos();
}

function alternaPaneles() {
    $('#combos').toggle();
    $('#matriculas').toggle();
    $('#ContenedorPorCursos').toggle();
}

function ActulizaSolicitudesPorCursos() {

    const param = {
        idCentro: $('#cbxCentros').val(),
    };

    $.ajax({
        type: "post",
        url: "GetHtmlSolicitudesPorCursos.php",
        data: param,
        success: function(r) {
            $('#solicitudesPorCursos').html(r);
        }
    });
}

function ActualizaCuadrante() {
    const param = {
        idCentro: $('#cbxCentros').val(),
    };

    $.ajax({
        type: "post",
        url: "GetHtmlCuadrante.php",
        data: param,
        success: function(r) {
            $('#cuadrante').html(r);
        }
    });
}

function ActualizaSolicitudesSinDiasSeleccionado() {
    const param = {
        idCentro: $('#cbxCentros').val(),
    };

    $.ajax({
        type: "post",
        url: "GetHtmlSolicitudesPorCursosSinDiasSeleccionado.php",
        data: param,
        success: function(r) {
            $('#solicitudesSinDias').html(r);
        }
    });
}

function ActualizaCantidadSolicitudes() {

    const param = {
        idCentro: $('#cbxCentros').val(),
    };

    $.ajax({
        type: "post",
        url: "GetHtmlTotalMatriculas.php",
        data: param,
        success: function(r) {
            $('#cantidadSolicitudes').html(r);
        }
    });
}