$(document).ready(function() {

    $('#cbxNotifFechas').change(function() {
        GetListaCentros();
    });

    $('#cbxNotifCentros').change(function() {
        GetListaAulas();
    });

    $('#cbxNotifAulas').change(function() {
        GetListaCursos();
    });

    $('#cbxNotifCursos').change(function() {
        GetListaHorarios();
    });

    $('#cbxNotifHorarios').change(function() {
        ListaAsistencia();
    });

})

function GetListaCentros() {

    $.ajax({
        type: "post",
        url: "GetHtmlSelectCentros.php",
        success: function(r) {
            $('#cbxNotifCentros').html(r);
            GetListaAulas();
            // ListaAsistencia();
        },

        error: function(error) {
            console.log(error);
        }
    });

}

function GetListaAulas() {

    const params = {
        idCentro: $('#cbxNotifCentros').val(),
    }

    $.ajax({
        type: "post",
        url: "GetHtmlSelectAulas.php",
        data: params,
        success: function(r) {
            $('#cbxNotifAulas').html(r);
            GetListaCursos();
            // ListaAsistencia();
        },

        error: function(error) {
            console.log(error);
        }
    });
}

function GetListaCursos() {

    const params = {
        idCentro: $('#cbxNotifCentros').val(),
        fecha: $('#cbxNotifFechas').val(),
        idAula: $('#cbxNotifAulas').val()
    }

    $.ajax({
        type: "post",
        url: "GetHtmlSelectCursos.php",
        data: params,
        success: function(r) {
            $('#cbxNotifCursos').html(r);
            GetListaHorarios();
            // ListaAsistencia();
        },

        error: function(error) {
            console.log(error);
        }
    });

}

function GetListaHorarios() {

    const params = {
        fecha: $('#cbxNotifFechas').val(),
        idCentro: $('#cbxNotifCentros').val(),
        idAula: $('#cbxNotifAulas').val(),
        curso: $('#cbxNotifCursos').val()
    }

    $.ajax({
        type: "post",
        url: "GetHtmlSelectHorarios.php",
        data: params,
        success: function(r) {
            $('#cbxNotifHorarios').html(r);
            ListaAsistencia();
        },

        error: function(error) {
            console.log(error);
        }
    });

}

function ListaAsistencia() {

    document.body.style.cursor = 'wait';

    const param = {
        idCentro: $('#cbxNotifCentros').val(),
        fecha: $('#cbxNotifFechas').val(),
        idAula: $('#cbxNotifAulas').val(),
        curso: $('#cbxNotifCursos').val(),
        horario: $('#cbxNotifHorarios').val()
    }

    $.ajax({
        type: "post",
        url: "GetHtmlCuadranteAsistenciaAlumnos.php",
        data: param,
        success: function(r) {
            $('#alumnosContainer').html(r);
            document.body.style.cursor = 'auto';
        },

        error: function(error) {
            document.body.style.cursor = 'auto';
        }

    })

}


function GenerarAsistencias() {
    const confirma = confirm("Se va a generar las asistencias de la fecha seleccionada");
    if (confirma) {

        document.body.style.cursor = 'wait';

        const params = {
            fecha: $('#cbxNotifFechas').val(),
            idCentro: $('#cbxNotifCentros').val(),
            idAula: $('#cbxNotifAulas').val(),
            curso: $('#cbxNotifCursos').val(),
            horario: $('#cbxNotifHorarios').val()
        }

        $.ajax({
            type: "post",
            url: "GenerarAsistencia.php",
            data: params,
            success: function(r) {
                ListaAsistencia();
                document.body.style.cursor = 'auto';
            },

            error: function(error) {
                document.body.style.cursor = 'auto';
            }
        });

    }
}


function EliminarAsistencias() {
    const confirma = confirm("Se va a ELIMINAR las asistencias de la fecha seleccionada");
    if (confirma) {

        document.body.style.cursor = 'wait';

        const params = {
            fecha: $('#cbxNotifFechas').val(),
            idCentro: $('#cbxNotifCentros').val(),
            idAula: $('#cbxNotifAulas').val(),
            curso: $('#cbxNotifCursos').val(),
            horario: $('#cbxNotifHorarios').val()
        }

        $.ajax({
            type: "post",
            url: "EliminarAsistencias.php",
            data: params,
            success: function(r) {
                ListaAsistencia();
                document.body.style.cursor = 'auto';
            },

            error: function(error) {
                document.body.style.cursor = 'auto';
            }
        });


    }

}

function EnviarEmails() {
    const confirma = confirm("Se va a enviar correos de aviso a los alumnos del dia de la fecha seleccionada");
    if (confirma) {

        document.body.style.cursor = 'wait';

        const params = {
            fecha: $('#cbxNotifFechas').val(),
            idCentro: $('#cbxNotifCentros').val(),
            idAula: $('#cbxNotifAulas').val(),
            curso: $('#cbxNotifCursos').val(),
            horario: $('#cbxNotifHorarios').val()
        }

        $.ajax({
            type: "post",
            url: "EnviarCorreosAsistencia.php",
            data: params,
            success: function(r) {
                ListaAsistencia();
                document.body.style.cursor = 'auto';
            },

            error: function(error) {
                document.body.style.cursor = 'auto';
            }
        });


    }
}