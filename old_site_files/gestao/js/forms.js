// JavaScript Document
$(document).ready(function () {

    $("#form_registro").validationEngine();

    $(".datahora").mask("99/99/9999 99:99:99");
    $(".data").mask("99/99/9999");
    $(".moeda").mask("#.##0,00", {
        reverse: true,
        placeholder: '0,00'
    });

    $(".btn_excluir").bind("click", function () {
        if (!confirm("Deseja realmente excluir esse registro?"))
            return false;
        var id = $(this).attr("data-id");
        var page = $(this).attr("data-page");
        $.ajax({
            type: "DELETE",
            url: $(".btn_ver_site").attr("href") + "/ajax-" + page + ".html",
            data: {
                id: id
            },
            success: function () {
                window.location.href = page + "-lista.html";
            }
        });
    });

    //Procurar por nome
    $("#nome").autocomplete({
        source: function (request, response) {
            $.getJSON("/wmjoias-erp/ajax-produtos.html?acao=buscar_produtos&tipo=1&busca=" + request.term, function (data) {
                response($.map(data.produtos, function (a, b, c, d) {
                    return {
                        id: a,
                        reference: b,
                        label: c,
                        value: d
                    };
                }));
            });
        },
        select: function (event, ui) {
            $('#referencia').val(ui.item.id.reference);
            $('#id_produto').val(ui.item.id.id);
            $('#nome').val(ui.item.id.value);
            return false;
        }
    }).data("ui-autocomplete")._renderItem = function (ul, item) {
        return $("<li></li>")
            .data("item.autocomplete", item.id.value)
            .append("<a>" + item.id.label + "</a>")
            .appendTo(ul);
    };

    //Procurar por referencia
    $("#referencia").autocomplete({
        source: function (request, response) {
            $.getJSON("/wmjoias-erp/ajax-produtos.html?acao=buscar_produtos&tipo=2&busca=" + request.term, function (data) {
                response($.map(data.produtos, function (a, b, c, d) {
                    return {
                        id: a,
                        reference: b,
                        label: c,
                        value: d
                    };
                }));
            });
        },
        select: function (event, ui) {
            $('#referencia').val(ui.item.id.reference);
            $('#id_produto').val(ui.item.id.id);
            $('#nome').val(ui.item.id.value);
            return false;
        }
    }).data("ui-autocomplete")._renderItem = function (ul, item) {
        return $("<li></li>")
            .data("item.autocomplete", item.id.value)
            .append("<a>" + item.id.label + "</a>")
            .appendTo(ul);
    };
});