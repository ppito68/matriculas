function ConmutaAsistencia(fecha, numeroAlumno, id) {

    const param = {
        fecha: fecha,
        numeroAlumno: numeroAlumno,
    };

    $.ajax({
        type: "post",
        url: "SetAsistencia.php",
        data: param,
        success: function(r) {
            if (r) {
                $('#' + id).replaceWith(r);
            }
        },

        error: function(error) {
            console.log(error);
        }
    })
}

function EliminarAlumno(numero) {
    const confirma = confirm("Estás segur@ de eliminar el alumno " + numero + "?");
    if (confirma) {

        const param = {
            numero: numero
        }

        $.ajax({
            type: "post",
            url: "EliminarAlumno.php",
            data: param,
            success: function(r) {
                ListaAsistencia();
            },

            error: function(error) {
                console.log(error);
            }
        });

    }
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
        },

        error: function(error) {
            console.log(error);
        }
    });
}

function GetListaFechas() {

    const params = {
        idDias: $('#cbxNotifDiasSemana').val(),
        idPromocion: 1
    }

    $.ajax({
        type: "post",
        url: "GetHtmlSelectFechas.php",
        data: params,
        success: function(r) {
            $('#cbxNotifFechas').html(r);
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

function GetAforo() {
    const params = {
        idCentro: $('#cbxNotifCentros').val(),
        fecha: $('#cbxNotifFechas').val(),
        idAula: $('#cbxNotifAulas').val(),
        curso: $('#cbxNotifCursos').val(),
        horario: $('#cbxNotifHorarios').val()
    }

    $.ajax({
        type: "post",
        url: "GetHtmlAforo.php",
        data: params,
        success: function(r) {
            $('#aforo').html(r);
        },

        error: function(error) {
            console.log(error);
        }
    });
}

function Disponibilidadcontroles(fecha) {
    document.getElementById("btnEnviarEmail").disabled = fecha == 0;
    document.getElementById("btnGenerar").disabled = fecha == 0;
}

function ListaAsistencia() {

    const param = {
        idCentro: $('#cbxNotifCentros').val(),
        fecha: $('#cbxNotifFechas').val(),
        idAula: $('#cbxNotifAulas').val(),
        curso: $('#cbxNotifCursos').val(),
        horario: $('#cbxNotifHorarios').val()
    }

    // Visualizacion total de alumnos del centro seleccionado. Solo lo muestra si sólo existe el filtro del centro
    if (param.fecha == 0 && param.idAula == 0 && param.curso == 0 && param.horario == 0) {
        $.ajax({
            type: "get",
            url: "GetTotalAlumnos.php",
            data: { centro: param.idCentro },
            success: function(r) {
                $('#cantidadAlumnos').html(r);
            },

            error: function(error) {
                console.log(error);
            }

        })

    } else {
        let cant = document.getElementById("cantidadAlumnos");
        cant.innerHTML = "";
    }

    Disponibilidadcontroles(param.fecha);


    // Lista solo si hay una fecha seleccionada
    if (param.fecha != 0 && param.fecha != '') {

        document.body.style.cursor = 'wait';

        $.ajax({
            type: "post",
            url: "GetHtmlCuadranteAsistenciaAlumnos.php",
            data: param,
            success: function(r) {
                $('#alumnosContainer').html(r);
                GetAforo();
                document.body.style.cursor = 'auto';
            },

            error: function(error) {
                document.body.style.cursor = 'auto';
            }

        })

    }


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
            url: "GenerarAsistencias.php",
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