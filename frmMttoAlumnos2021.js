$(document).ready(function() {

    $('#cbxMttoCentros').change(function() {
        GetMttoListaAulas();
    });

})

function GetMttoListaAulas() {

    const params = {
        idCentro: $('#cbxMttoCentros').val(),
        idAulaPreSelect: $('#idAula').val()
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