$(document).ready(function () {
    $(document).on("click", "#imagens-upload div.container-img > span.apagar", function () {
        if (confirm("Deseja realmente apagar essa imagem?")) {
            var urlAjax = gestao_url + "/servicos-form.html?id=" + $("#id_registro").val();
            $.ajax({
                method: "POST",
                url: urlAjax,
                data: {
                    acao: "deleta_imagem",
                    data_id_objeto_foto: $(this).attr('data_id_objeto_foto'),
                    id_registro: $("#id_registro").val()
                },
                success: function (resposta) {
                    listagemFotos();
                }
            });
        }
    });
});

function listagemFotos() {
    var urlAjax = gestao_url + '/textos-form.html?id=' + $("#id_registro").val();
    $(".loader").show();
    /*Brendol Lourençon*/
    $.ajax({
        url: urlAjax,
        method: "POST",
        dataType: "json",
        data: {acao: "imagens-enviadas", id_registro: $("#id_registro").val()},
        beforeSend: function () {
            $(".loading").show();
            $(".loader").show();
        },
        success: function (resposta) {
            $("#imagens-upload").html('');
            $("#imagens-upload").append('<div class="loading"><div class="loader">Loading...</div></div>');
            for (var i = 0; i < resposta.length; i++) {
                $("#imagens-upload").append(
                    "<div class='container-img'>" +
                    "<div class='img'>" +
                    "<img src='" + site_uploads + "/textos/album/medias/" + resposta[i].url_imagem + "'></div>" +
                    "<span class='apagar' data_id_objeto_foto='" + resposta[i].id_texto_foto + "'>Apagar</span>" +
                    "</div>");
            }
            $(".loading").hide();
            $(".loader").hide();
        }
    });
}
function setImagemCapa(){
    return false;
}