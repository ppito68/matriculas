function GetListaCursos() {

    var params = {
        idCentro: $('#cbxCentros').val(),
        idAula: $('#cbxAulas').val()
    };

    $.ajax({
        type: "post",
        url: "GetHtmlSelectCursos.php",
        data: params,
        success: function(r) {
            $('#cbxCursos').html(r);
            GetListaDias();
        }
    });
}

function GetListaAulas() {

    const params = {
        idCentro: $('#cbxCentros').val(),
    }

    $.ajax({
        type: "post",
        url: "GetHtmlSelectAulas.php",
        data: params,
        success: function(r) {
            $('#cbxAulas').html(r);
        }
    });
}

function GetListaDias() {

    const param = {
        idCentro: $('#cbxCentros').val(),
        idCurso: $('#cbxCursos').val(),
    };

    $.ajax({
        type: "post",
        url: "../../CreaListaDiasSemana.php",
        data: param,
        success: function(r) {
            $('#cbxDias').html(r);
            GetListaHorarios();
        }
    });
}

function GetListaHorarios() {

    const param = {
        idCentro: $('#cbxCentros').val(),
        idCurso: $('#cbxCursos').val(),
        idDias: $('#cbxDias').val(),
    };

    $.ajax({
        type: "post",
        url: "../../CreaListaHorarios.php",
        data: param,
        success: function(r) {
            $('#cbxHorarios').html(r);
        }
    });
}

function ActualizaMatriculas() {
    const param = {
        idCentro: $('#cbxCentros').val(),
        idCurso: $('#cbxCursos').val(),
        idDia: $('#cbxDias').val(),
        idHorario: $('#cbxHorarios').val(),
        idAula: $('#cbxAulas').val()

    }

    $.ajax({
        type: "get",
        url: "GetHtmlMatriculas.php",
        data: param,
        success: function(r) {
            $('#matriculas').html(r);
        }
    });

}