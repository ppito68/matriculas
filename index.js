$(document).ready(function() {

    // Combos del Panel de asistencias
    $('#cbxNotifPromociones').change(() => {
        GetListaFechas();
    });

    $('#cbxNotifCentros').change(() => {
        GetListaAulas();
    });

    $('#cbxNotifDiasSemana').change(() => {
        GetListaFechas();
    })

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


function MttoAlumno(numeroAlumno) {

    // oculta el panel de asistencias
    document.getElementById('notificaciones').style.display = 'none';

    // muestra el formulario de datos del alumno
    document.getElementById('mttoAlumno').style.display = 'block';

    const param =  { 
        numeroAlumno: numeroAlumno ,
        idPromocion: $('#cbxNotifPromociones').val()
    }

    // obtiene los datos del alumno y se los pasa al formulario de mtto del alumno
    // $.getJSON("GetJsonAlumno.php",param , (alumno) => {


        $.ajax({
            type: "get",
            url: "frmMttoAlumnos.php",
            data: param,
            success: function(r) {
                $('#mttoAlumno').html(r);
                // CargaComboCentrosMttoAlumnos(alumno.centro);
                // CargaComboAulasMttoAlumnos(alumno.centro, alumno.idAula);
                // CargaComboProfesoresMttoAlumnos(alumno.idProfesor);
                // CargaComboCursosMttoAlumnos(alumno.curso);
                // CargaComboHorariosMttoAlumnos(alumno.horario);
            },

            error: function(error) {
                console.log(error);
            }
        });

    // })


}


function MttoNotasAlumno(numeroAlumno){

    // oculta el panel de asistencias y muestra el panel de las notas del alumno
    document.getElementById('notificaciones').style.display = 'none';
    document.getElementById('mttoNotas').style.display = 'block';

    
    const param = {
        numero: numeroAlumno
    }

    $.ajax({
        type: "get",
        url: "frmMttoNotas.php",
        data: param,
        success: function(r) {
            $('#mttoNotas').html(r);
        },

        error: function(error) {
            //document.body.style.cursor = 'auto';
            console.log(error);
        }

    })


}

function GrabaNotas(){

    const param = {
        idMatricula: document.getElementById('idMatricula').value,
        speaking1: document.getElementById('speaking1').value,
        speaking2: document.getElementById('speaking2').value, 
        listening1: document.getElementById('listening1').value, 
        listening2: document.getElementById('listening2').value,
        writing1: document.getElementById('writing1').value,
        writing2: document.getElementById('writing2').value,
        reading1: document.getElementById('reading1').value,
        reading2: document.getElementById('reading2').value,
        exEscrito1: document.getElementById('examenEscrito1').value,
        exEscrito2: document.getElementById('examenEscrito2').value,
        participacion1: document.getElementById('participacion1').value,
        participacion2: document.getElementById('participacion2').value,
        comportamiento1: document.getElementById('comportamiento1').value,
        comportamiento2: document.getElementById('comportamiento2').value,
        examenOral: document.getElementById('examenOral').value,
        unit1: document.getElementById('unit1').value,
        unit2: document.getElementById('unit2').value,
        unit3: document.getElementById('unit3').value,
        unit4: document.getElementById('unit4').value,
        unit5: document.getElementById('unit5').value,
        unit6: document.getElementById('unit6').value,
        unit7: document.getElementById('unit7').value,
        unit8: document.getElementById('unit8').value,
        unit9: document.getElementById('unit9').value,
        unit10: document.getElementById('unit10').value,
        unit11: document.getElementById('unit11').value,
        unit12: document.getElementById('unit12').value,

        unit13: document.getElementById('unit13').value,
        unit14: document.getElementById('unit14').value,
        unit15: document.getElementById('unit15').value,
        unit16: document.getElementById('unit16').value,
        unit17: document.getElementById('unit17').value,
        unit18: document.getElementById('unit18').value,
        unit19: document.getElementById('unit19').value,
        unit20: document.getElementById('unit20').value,

        comentarios: document.getElementById('comentarios').value,
        enviado1: document.getElementById('enviado1').value,
        enviado2: document.getElementById('enviado2').value
    }

    $.ajax({
        type: "post",
        url: "GrabacionNotas.php",
        data: param,
        success: function(r) {
            PanelAsistencias();
        },

        error: function(error) {
            console.log(error);
        }
    })
}


function VerNotasEnPdf(){

    // const param = {
        // idMatricula: document.getElementById('idMatricula').value
    // };
    const idMatricula = document.getElementById('idMatricula').value;

    window.open('NotasPdf.php?idMatricula='+idMatricula);

    // $.ajax({
    //     type: "get",
    //     url: "ImprimirNotas.php",
    //     data: param,
    //     success: function(r) {
    //         window.open(r);
    //     },

    //     error: function(error) {
    //         console.log(error);
    //     }
    // })


}


function PanelAsistencias() {
   
    // oculta elformulario de datos del alumno
    var mttoAlum = document.getElementById('mttoAlumno');
    mttoAlum.style.display = 'none';

    // oculta elformulario de NOTAS del alumno
    var mttoNotas = document.getElementById('mttoNotas');
    mttoNotas.style.display = 'none';

    // muestra el panel de asistencias
    var panel = document.getElementById('notificaciones');
    panel.style.display = 'block';

    ListaAsistencia();
}