function CargaComboCentrosMttoAlumnos(idCentroPreSelect) {

    const params = {
        idCentroPreSelect: idCentroPreSelect
    }

    $.ajax({
        type: "post",
        url: "GetHtmlSelectCentros.php",
        data: params,
        success: function(r) {
            $('#cbxMttoCentros').html(r);
        },

        error: function(error) {
            console.log(error);
        }
    });
}



function CargaComboAulasMttoAlumnos(idCentro, idAulaPreSelect) {

    const params = {
        idCentro: idCentro,
        idAulaPreSelect: idAulaPreSelect
    }

    $.ajax({
        type: "post",
        url: "GetHtmlSelectAulas.php",
        data: params,
        success: function(r) {
            $('#cbxMttoAulas').html(r);
        },

        error: function(error) {
            console.log(error);
        }
    });
}

function CargaComboCursosMttoAlumnos(cursoPreSelect) {

    const params = {
        cursoPreSelect: cursoPreSelect
    }

    $.ajax({
        type: "post",
        url: "GetHtmlSelectCursos.php",
        data: params,
        success: function(r) {
            $('#cbxMttoCursos').html(r);
        },

        error: function(error) {
            console.log(error);
        }
    });
}

function CargaComboHorariosMttoAlumnos(horarioPreSelect) {

    const params = {
        horarioPreSelect: horarioPreSelect
    }

    $.ajax({
        type: "post",
        url: "GetHtmlSelectHorarios.php",
        data: params,
        success: function(r) {
            $('#cbxMttoHorario').html(r);
        },

        error: function(error) {
            console.log(error);
        }
    });
}