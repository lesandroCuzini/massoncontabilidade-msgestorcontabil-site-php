var urlAjax = gestao_url + "/historia-form.html?id=" + $("#id_registro").val();

$(document).ready(function () {
    $(document).on("click", "#imagens-upload div.container-img > span.apagar", function () {
        if (confirm("Deseja realmente apagar essa imagem?")) {
            $.ajax({
                method: "POST",
                url: urlAjax,
                data: {
                    acao: "removerImagem",
                    id_foto: $(this).attr('data_id_objeto_foto'),
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
    $(".loader").show();
    $.ajax({
        url: urlAjax,
        method: "POST",
        dataType: "json",
        data: {
            acao: "galeriaImagens",
            id_registro: $("#id_registro").val()
        },
        beforeSend: function () {
            $(".loading").show();
            $(".loader").show();
        },
        success: function (resposta) {
            $("#imagens-upload").html('');
            $("#imagens-upload").append('<div class="loading"><div class="loader">Loading...</div></div>');
            for (var i = 0; i < resposta.length; i++) {
                $("#imagens-upload").append(
                    "<div class='col-xs-3 col-md-2 container-img'>" +
                    "<div class='img'>" +
                    "<img src='" + site_uploads + "/institucional/historia/galeria/small/" + resposta[i].url_imagem + "'></div>" +
                    "<span class='apagar' data_id_objeto_foto='" + resposta[i].id_historia_foto + "'><i class=\"fa fa-trash\"></i> Apagar</span>" +
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