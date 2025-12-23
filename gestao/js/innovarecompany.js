// Switchery
$(document).ready(function () {
    $('#menu-mobile').on('click', function () {
        if ($(this).hasClass('ativo')) {
            $(this).removeClass('ativo');
            $('#menu').slideUp(400);
        } else {
            $(this).addClass('ativo');
            $('#menu').slideDown(400);
        }
    });
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


    $("#form_admin").submit(function (e) {
        if (!validaFormulario("#form_admin")) {
            $('input,select').each(function () {
                if ($(this).parent().children('.erro-form').length > 0) {
                    $(this).parent().css('margin-bottom','15px');
                }
            });
            e.preventDefault();
        }
    });
    menuDetalhe();
});

function validaFormulario(form) {

    var error = false;
    var camposVazios = [];

    // faz a validacao
    $(form).find("input , select, textarea").each(function () {
        if (typeof $(this).attr('required') != "undefined") {
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
                var erros = {
                    'campo': $(this).attr("id"),
                    'msg': "O valor digitado não é um email."
                };
                camposVazios.push(erros);
            }
            else {
                $(this).next(".erro-form").remove();
            }

        }
    });
    if (camposVazios.length > 0) {
        //inputs
        for (var i = 0; i < camposVazios.length; i++) {
            $(form + " input" + "#" + camposVazios[i].campo).parent().append("<span style='color: #ed1c24;font-size: 12px;position: absolute;left: 0;bottom: -20px;line-height: 20px;'" +
                " class='erro-form'>" + camposVazios[i].msg + "</span>");
        }
        //selects
        for (var j = 0; j < camposVazios.length; j++) {
            $(form + " select" + "#" + camposVazios[j].campo).parent().parent().append("<span style='color: #ed1c24;font-size: 12px;display: block;line-height: 20px;' class='erro-form'>" + camposVazios[j].msg + "</span>");
        }
        for (var j = 0; j < camposVazios.length; j++) {
            $(form + " textarea" + "#" + camposVazios[j].campo).parent().append("<span style='color: #ed1c24;font-size: 12px;left: 0px;position: absolute;bottom: -18px' class='erro-form'>" + camposVazios[j].msg + "</span>");
        }
        return false;
    }
    return true;
}

function clearInput(dados) {
    $(dados).val('');
}
function menuDetalhe() {

    $('ul.nav.menu li.parent > a').each(function () {
        if ($(this).parent().children('ul').hasClass('collapse')) {
            $(this).children('span').removeClass('ativo');
        } else {
            $(this).children('span').addClass('ativo');
        }
    });
    $('ul.nav.menu li.parent > a span').on('click', function () {
        if ($(this).parent().parent().children('ul').hasClass('in')) {
            $(this).removeClass('ativo');
        } else {
            $(this).addClass('ativo');
        }
    });
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
            else {
                $("#caracteristicas-selecionaidas").html('<li>sem caracteristicas cadastradas no momento.</li>');
            }
        }
    });

}
function maskTelephone(textbox, blur) {

    var telephone = textbox.value.replace(/[^0-9]/g, '');

    if (/^\d{1,2}$/.test(telephone)) {
        telephone = '(' + telephone + ')';
    }
    if (/^\d{3,}$/.test(telephone)) {
        telephone = '(' + telephone.substring(0, 2) + ')' + telephone.substring(2);
    }
    if (/^.\d{2}.\d{5,8}$/.test(telephone)) {
        telephone = telephone.substring(0, 8) + '-' + telephone.substring(8);
    }
    if (/^.\d{2}.\d{9,}$/.test(telephone)) {
        telephone = telephone.substring(0, 9) + '-' + telephone.substring(9, 13);
    }
    var caretPos = getCursorPosition(textbox);
    var lastLength = textbox.value.length;
    textbox.value = telephone;
    var newLength = textbox.value.length;
    if (!blur) {
        setCursorPosition(textbox, caretPos + newLength - lastLength);
    }
}
function setCursorPosition(el, index) {
    if (el.createTextRange) {
        var range = el.createTextRange();
        range.move('character', index);
        range.select();
    } else if (el.selectionStart != null) {
        el.focus();
        el.setSelectionRange(index, index);
    }
}
function getCursorPosition(oField) {
    var iCaretPos = 0;
    // IE Support
    if (document.selection) {
        oField.focus();
        var oSel = document.selection.createRange();
        oSel.moveStart('character', -oField.value.length);
        iCaretPos = oSel.text.length;
    }
    // Firefox support
    else if (oField.selectionStart || oField.selectionStart == '0')
        iCaretPos = oField.selectionStart;
    return (iCaretPos);
}
