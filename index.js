$(document).ready(function() {

    // Combos del Panel de asistencias
    $('#cbxNotifCentros').change(function() {
        GetListaAulas();
    });

    $('#cbxNotifFechas').change(function() {
        ListaAsistencia();
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

    Disponibilidadcontroles($('#cbxNotifFechas').val());

})


function MttoAlumno(numero) {

    // oculta el panel de asistencias
    document.getElementById('notificaciones').style.display = 'none';

    // muestra el formulario de datos del alumno
    document.getElementById('mttoAlumno').style.display = 'block';

    // obtiene los datos del alumno y se los pasa al formulario de mtto del alumno
    $.getJSON("GetJsonAlumno.php", { numero: numero }, (alumno) => {

        $.ajax({
            type: "get",
            url: "frmMttoAlumnos2021.php",
            data: alumno,
            success: function(r) {
                $('#mttoAlumno').html(r);
                CargaComboCentrosMttoAlumnos(alumno.centro);
                CargaComboAulasMttoAlumnos(alumno.centro, alumno.idAula);
                CargaComboCursosMttoAlumnos(alumno.curso);
                CargaComboHorariosMttoAlumnos(alumno.horario);
            },

            error: function(error) {
                console.log(error);
            }
        });


    })
}



function PanelAsistencias() {
    // muestra el panel de asistencias
    var panel = document.getElementById('notificaciones');
    panel.style.display = 'block';

    // oculta elformulario de datos del alumno
    var mtto = document.getElementById('mttoAlumno');
    mtto.style.display = 'none';

    ListaAsistencia();
}