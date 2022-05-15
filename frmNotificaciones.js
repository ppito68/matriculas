function ConmutaAsistencia(fecha, idMatricula, id) {

    const param = {
        fecha: fecha,
        idMatricula: idMatricula,
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

function EliminarMatricula(idMatricula) {
    const confirma = confirm("ATENCION!!! Esta opción elimina la matrícula del alumno.\r" +
                                "Los datos personales del alumno permanecerán en la base de edatos para futuras matriculaciones \r" + 
                                "¿ quiere eliminar la matrícula ?");
    if (confirma) {

        const param = {
            idMatricula: idMatricula
        }

        $.ajax({
            type: "post",
            url: "BajaMatricula.php",
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
            //GetListaCursos();
        },

        error: function(error) {
            console.log(error);
        }
    });
}

function GetListaFechas() {

    const params = {
        idDias: $('#cbxNotifDiasSemana').val(),
        idPromocion: $('#cbxNotifPromociones').val(),
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

    // const params = {
    //     idPromocion: $('#cbxNotifPromociones').val(),
    //     idCentro: $('#cbxNotifCentros').val(),
    //     fecha: $('#cbxNotifFechas').val(),
    //     idAula: $('#cbxNotifAulas').val()
    // }

    $.ajax({
        type: "post",
        url: "GetHtmlSelectCursos.php",
        // data: params,
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

    // const params = {
    //     idPromocion: $('#cbxNotifPromociones').val(),
    //     fecha: $('#cbxNotifFechas').val(),
    //     idCentro: $('#cbxNotifCentros').val(),
    //     idAula: $('#cbxNotifAulas').val(),
    //     curso: $('#cbxNotifCursos').val()
    // }

    $.ajax({
        type: "post",
        url: "GetHtmlSelectHorarios.php",
        // data: params,
        success: function(r) {
            $('#cbxNotifHorarios').html(r);
            // ListaAsistencia();
        },

        error: function(error) {
            console.log(error);
        }
    });

}

function GetAforo() {
    const params = {
        idPromocion: $('#cbxNotifPromociones').val(),
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
        idPromocion: $('#cbxNotifPromociones').val(),
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
            data: { centro: param.idCentro,
                    idPromocion: param.idPromocion
            },
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
    // La siguiente linea la quito porque el filtro de fecha vacía lo hago en GetHtmlCuadranteAsistenciaAlumnos.php
    // if (param.fecha != 0 && param.fecha != '') {

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

    // }

}


function GenerarAsistencias() {
    const confirma = confirm("Se va a generar las asistencias de la fecha seleccionada");
    if (confirma) {

        document.body.style.cursor = 'wait';

        const params = {
            idPromocion: $('#cbxNotifPromociones').val(),
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
            horario: $('#cbxNotifHorarios').val(),
            idPromocion: $('#cbxNotifPromociones').val()
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

function EnviarEmailsModoAsistencias() {
    const confirma = confirm("Se va a enviar correos de aviso a los alumnos del dia de la fecha seleccionada");
    if (confirma) {

        document.body.style.cursor = 'wait';

        const params = {
            fecha: $('#cbxNotifFechas').val(),
            idCentro: $('#cbxNotifCentros').val(),
            idAula: $('#cbxNotifAulas').val(),
            curso: $('#cbxNotifCursos').val(),
            horario: $('#cbxNotifHorarios').val(),
            idPromocion: $('#cbxNotifPromociones').val()
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

// envia las notas a un colectivo de alumnos
function EnviarEmailsConNotas() {
    const confirma = confirm("Se va a enviar correos con las notas de los alumnos");
    if (confirma) {

        document.body.style.cursor = 'wait';

        const params = {
            fecha: $('#cbxNotifFechas').val(),
            idCentro: $('#cbxNotifCentros').val(),
            idAula: $('#cbxNotifAulas').val(),
            curso: $('#cbxNotifCursos').val(),
            horario: $('#cbxNotifHorarios').val(),
            idPromocion: $('#cbxNotifPromociones').val()
        }

        $.ajax({
            type: "post",
            url: "EnviarCorreosConNotas.php",
            data: params,
            success: function() {
                ListaAsistencia();
                document.body.style.cursor = 'auto';
            },

            error: function(error) {
                document.body.style.cursor = 'auto';
            }
        });


    }
}


// envia las notas a un alumno
function EnviarNotas(idMatricula, nombreCompletoAlumno, emailAlumno, mostrarSolo) {


    if(!mostrarSolo){

        const confirma = confirm("Se va a enviar correos con las notas de los alumnos");

        if(!confirma){
            return;
        }

    }

    document.body.style.cursor = 'wait';

    $.ajax({
        type: "post",
        url: "EnviarNotas.php",
        data: {
            idMatricula: idMatricula,
            nombreCompletoAlumno: nombreCompletoAlumno,
            emailAlumno: emailAlumno,
            mostrarSolo: mostrarSolo,
        },
        success: function() {
            if(mostrarSolo){
                window.open("./pdfs/"+idMatricula+"pdf"); //$('#mttoNotas').html(r);
            }
            ListaAsistencia();
            document.body.style.cursor = 'auto';
        },

        error: function(error) {
            console.log(error);
            document.body.style.cursor = 'auto';
        }
    });


}