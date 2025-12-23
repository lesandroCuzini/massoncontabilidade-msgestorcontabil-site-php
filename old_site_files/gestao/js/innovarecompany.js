// Switchery
$(document).ready(function () {
    if ($(".js-switch")[0]) {
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        elems.forEach(function (html) {
            var switchery = new Switchery(html, {
                size: 'small',
                color: '#26B99A'
            });
        });

        //Checkbox personalizado
        if ($('.flag_form').length > 0) {
            $('.flag_form').change(function () {
                var txt_ativo = $(this).data('active');
                var txt_inativo = $(this).data('inactive');
                if (this.checked) {
                    $(this).parent('div').find('.label_flag').each(function () {
                        $(this).html(txt_ativo);
                    });
                } else {
                    $(this).parent('div').find('.label_flag').each(function () {
                        $(this).html(txt_inativo);
                    });
                }
            });
        }
    }

    //Retração automática do alert
    if ($('.alert').length > 0) {
        $('.alert').delay(5000).slideUp('slow');
    }

    //Submeter formulário
    $('.btn_submit_form').click(function () {
        $('#form_admin').submit();

        return false;
    });
    $('.btn_submit_form_new').click(function () {
        $('#acao_posterior').val('new');
        $('#form_admin').submit();

        return false;
    });
    
    $('form').submit(function () {
        var erro = false;
        $(this).find('input','select','textarea').each(function () {
            if (typeof $(this).attr('required') != "undefined") {
                if ($(this).val() == "") {
                    erro = true;
                    $(this).closest('.input_form').append('<span class="data_error"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> ' + $(this).data('error') + '</span>');
                }
            }
        });

        if (erro) return false;
    });

    //Contador de caracteres do title e description
    if ($('.contador').length > 0) {
        $('.contador').keyup(function () {
            var tam_texto = $(this).val().length;
            var total = 0;
            $(this).closest('.input-group').find('#count_caracteres').each(function () {
                total = parseInt($(this).data('total'));
            });

            $(this).closest('.input-group').find('#count_caracteres').each(function () {
                var restante = total - tam_texto;
                if (restante >= 0) {
                    $(this).html(restante);
                } else {
                    $(this).html('<span style="color:#F00">' + restante + '</span>');
                }
            });
        });
    }

    contadorCaracter($("#meta_title"), "label .quantidade", 70);

    contadorCaracter($("#meta_description"), "label .quantidade", 160);

    //Transform RichText
    /*if ($('.texteditor').length > 0) {
     initToolbarBootstrapBindings();
     var box_destino = $('.texteditor').data('destino');
     $('.texteditor')
     .html($('#'+box_destino).val())
     .wysiwyg();
     $('.texteditor').change(function(){
     $('#'+box_destino).val($(this).html());
     });
     }*/

    $("#id_caracteristica").on("change", function () {
        if ($(this).val() != "") {
            $("#id_caracteristica_valor").html('<option>aguarde...</option>');
            $.ajax({
                method: "post",
                url: site_url + "/ajax-caracteristicas-valores.html",
                data: {acao: "getValoresByCaracteristica", id_caracteristica: $(this).val()},
                dataType: "json",
                success: function (resposta) {
                    if (resposta) {
                        $("#id_caracteristica_valor").html('<option value="0">selecione</option>');
                        for (var i = 0; i < resposta.length; i++) {
                            $("#id_caracteristica_valor").append(
                                "<option value='" + resposta[i].id_caracteristica_valor + "' >" + resposta[i].descricao + "</option>"
                            );
                        }
                    }
                    else {
                        $("#id_caracteristica_valor").html('');
                    }
                }
            })
        }
    });

    $("#adicionar-caracteristica").on("click", function () {
        $.ajax({
            url: site_url + "/ajax-produtos.html",
            method: "post",
            data: {
                acao: "insere-caracteristica",
                id_produto: $("#id_registro").val(),
                id_caracteristica: $("#id_caracteristica").val(),
                id_caracteristica_valor: $("#id_caracteristica_valor").val(),
                valor_caracteristica: $("#valor_caracteristica").val()
            },
            success: function (resposta) {
                clearInput("#id_caracteristica");
                clearInput("#id_caracteristica_valor");
                clearInput("#valor_caracteristica");
                listaCatacteristicasProduto();
            }
        })
    });

    $(document).on("click", ".apagar-caracteristica", function () {
        if (confirm("deseja apagar esta caracteristica?")) {
            var idProdutoCaracteristica = $(this).attr('data-id-caracteristica');
            $.ajax({
                url: site_url + "/ajax-produtos.html",
                method: "post",
                data: {
                    acao: "apagar-caracteristica",
                    id_produto_caracteristica: idProdutoCaracteristica
                },
                success: function (resposta) {
                    if (resposta) {
                        listaCatacteristicasProduto();
                    }
                }
            });
        }
    });

    $(".form-produtos").submit(function (e) {
        if(!validaFormulario(".form-produtos")){
            e.preventDefault();
        }
    });


});

function validaFormulario(form) {

    var error = false;
    var camposVazios = [];

    // faz a validacao
    $(form).find("input , select").each(function () {

        $(this).parent().find(".erro-form").remove();
        // campos vázios
        if (!$(this).hasClass("opcional") && $(this).val() == "" && $(this).attr("type") != "hidden") {
            var erros = {
                'campo': $(this).attr("id"),
                'msg': "Este campo está vázio."
            };
            camposVazios.push(erros);
        }
        else {
            $(this).next(".erro-form").remove();
        }

        if ($(this).attr("type") == "email" && !checkMail($(this).val())) {
            //$(this).parent().append("<span class='erro-form'>Este campo está vázio</span>");
            var erros = {
                'campo': $(this).attr("id"),
                'msg': "O valor digitado não é um email."
            };
            camposVazios.push(erros);
        }
        else {
            $(this).next(".erro-form").remove();
        }

    });
    if (camposVazios.length > 0) {
        $(".erros-formulario").addClass('alert alert-danger').html("Existe campos vázios");
        //inputs
        for (var i = 0; i < camposVazios.length; i++) {
            $(form + " input" + "#" + camposVazios[i].campo).parent().parent().append("<span class='data_error'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i>" + camposVazios[i].msg + "</span>");
        }
        //selects
        for (var j = 0; j < camposVazios.length; j++) {
            $(form + " select" + "#" + camposVazios[j].campo).parent().parent().append("<span class='data_error'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i>" + camposVazios[j].msg + "</span>");
        }
        return false;
    }
    return true;
}

function clearInput(dados) {
    $(dados).val('');
}

function listaCatacteristicasProduto() {
    $.ajax({
        url: site_url + "/ajax-produtos.html",
        method: "post",
        data: {acao: "getCaracteristica", id_produto: $("#id_registro").val()},
        dataType: "json",
        success: function (resposta) {
            $("#caracteristicas-selecionaidas").html('');
            if (resposta) {
                for (var i = 0; i < resposta.length; i++) {
                    var nomeValorCaracteristica = (resposta[i].nome_valor_caracteristica != null) ? "[" + resposta[i].nome_valor_caracteristica + "]" : '';
                    var valor = (resposta[i].valor_adicionado != null) ? resposta[i].valor_adicionado : '';
                    $("#caracteristicas-selecionaidas").append(
                        "<li> <span class='apagar-caracteristica' data-id-caracteristica='" + resposta[i].id_produto_caracteristica + "'>[x]</span> " + resposta[i].nome_caracteristica + ': ' + nomeValorCaracteristica + ' ' + valor + "</li>"
                    );
                }
            }
            else{
                $("#caracteristicas-selecionaidas").html('<li>sem caracteristicas cadastradas no momento.</li>');
            }
        }
    });

}

/*Brendol Lourençon*/
function contadorCaracter(divContar, divPrintar, quantidadePermitida) {
    if (divContar.length) {
        divContar.parent().parent().find(divPrintar).text((quantidadePermitida - divContar.val().length) + " caracteres restantes.");
        divContar.on("keyup", function () {
            var quantidade = $(this).val().length;
            divContar.parent().parent().find(divPrintar).text((quantidadePermitida - quantidade) + " caracteres restantes.");
        });
    }
}

// /Switchery
/*
 function initToolbarBootstrapBindings() {
 var fonts = ['Serif', 'Sans', 'Arial', 'Arial Black', 'Courier',
 'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande', 'Lucida Sans', 'Tahoma', 'Times',
 'Times New Roman', 'Verdana'
 ],
 fontTarget = $('[title=Font]').siblings('.dropdown-menu');
 $.each(fonts, function(idx, fontName) {
 fontTarget.append($('<li><a data-edit="fontName ' + fontName + '" style="font-family:\'' + fontName + '\'">' + fontName + '</a></li>'));
 });
 $('a[title]').tooltip({
 container: 'body'
 });
 $('.dropdown-menu input').click(function() {
 return false;
 })
 .change(function() {
 $(this).parent('.dropdown-menu').siblings('.dropdown-toggle').dropdown('toggle');
 })
 .keydown('esc', function() {
 this.value = '';
 $(this).change();
 });

 $('[data-role=magic-overlay]').each(function() {
 var overlay = $(this),
 target = $(overlay.data('target'));
 overlay.css('opacity', 0).css('position', 'absolute').offset(target.offset()).width(target.outerWidth()).height(target.outerHeight());
 });

 if ("onwebkitspeechchange" in document.createElement("input")) {
 var editorOffset = $('#editor').offset();

 $('.voiceBtn').css('position', 'absolute').offset({
 top: editorOffset.top,
 left: editorOffset.left + $('#editor').innerWidth() - 35
 });
 } else {
 $('.voiceBtn').hide();
 }
 }

 function showErrorAlert(reason, detail) {
 var msg = '';
 if (reason === 'unsupported-file-type') {
 msg = "Unsupported format " + detail;
 } else {
 console.log("error uploading file", reason, detail);
 }
 $('<div class="alert"> <button type="button" class="close" data-dismiss="alert">&times;</button>' +
 '<strong>File upload error</strong> ' + msg + ' </div>').prependTo('#alerts');
 }*/