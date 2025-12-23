$(document).ready(function (){
    // Swiper Banner
    var swiperBanner = new Swiper('.swiper-banner', {
        grabCursor: true,
        loop: false,
        autoplay: {
            delay: 8000,
        },
        pagination: {
            el: '.swiper-banner .swiper-pagination',
            clickable: true,
        },
        keyboard: {
            enabled: true,
            onlyInViewport: false
        }
    });

    // Cards Centralizados da Seção Nossos Serviços
    var qtdCards = $('.our-services .card-group .card').length;
    if (window.innerWidth < 768) {
        if (qtdCards == 1) {
            $('.our-services .card-group').addClass('justify-content-center');
        }
    }

    // Alert Form Newsletter
    var alertPlaceholder = document.getElementById('liveAlertPlaceholder');

    function alert(message, type) {
        var wrapper = document.createElement('div');
        wrapper.innerHTML = '<div class="alert alert-' + type + ' alert-dismissible fs-small mt-3 mb-0" role="alert">' + message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';

        alertPlaceholder.append(wrapper);
    }

    // Envio Formulário Newsletter
    $('#formNewsletter').submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = form.serializeArray();
        formData.push({name: 'acao', value:'addNewsletter'});
        if (validaFormulario("#formNewsletter")) {
            $.ajax({
                method: "post",
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if (data.retorno == 'sucesso') {
                        alert('OK, e-mail cadastrado com sucesso!', 'success');
                        $('#formNewsletter input').val("");
                    } else {
                        alert('Ops, houve uma falha ao cadastrar seu e-mail', 'danger');
                    }
                },
                error: function (err) {
                    alert('Ops, houve uma falha ao cadastrar seu e-mail', 'danger');
                }
            });
        }
    });


    $("input#preco").maskMoney({
        prefix: 'R$ ',
        allowNegative: true,
        thousands: '.',
        decimal: ',',
        affixesStay: false
    });

    if ($('form#formulario-simular-mensalidade').length) {
        simularMensalidade();
    }
    $("form#formulario-simular-mensalidade").on('change', ['input', 'select'], function () {
        simularMensalidade();
    });
    $("select#tipo-simulacao").on('change',function () {
        getFaturamentosByTipoServico($("select#tipo-simulacao").val());
    });
});

function validaFormulario(form) {

    var error = false;
    var camposVazios = [];

    // faz a validacao
    $(form).find("input, textarea").each(function () {
        if (typeof $(this).attr('required') != "undefined") {
            // Remove msg de erro para INPUT
            $(this).parent().parent().find(".erro-form").remove();

            // campos vázios
            if ($(this).val() == "" && !$(this).hasClass("opcional") && $(this).attr("type") != "hidden" && $(this).attr("type") != "email") {
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
                if ($(this).val() == "") {
                    var erros = {
                        'campo': $(this).attr("id"),
                        'msg': "Este campo está vázio."
                    };
                } else {
                    var erros = {
                        'campo': $(this).attr("id"),
                        'msg': "O valor digitado não é um email."
                    };
                }
                camposVazios.push(erros);
            }
            else {
                $(this).next(".erro-form").remove();
            }
        }
    });
    if (camposVazios.length > 0) {
        // Inputs
        for (var i = 0; i < camposVazios.length; i++) {
            $(form + " input" + "#" + camposVazios[i].campo).parent().parent().append("<span style='font-size: 12px; color: #ed1c24;' class='erro-form'>" + camposVazios[i].msg + "</span>");
        }
        // Textarea
        for (var j = 0; j < camposVazios.length; j++) {
            $(form + " textarea" + "#" + camposVazios[j].campo).parent().append("<span style='font-size: 12px; color: #ed1c24;' class='erro-form'>" + camposVazios[j].msg + "</span>");
        }
        return false;
    }
    return true;
}

function checkMail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}


function simularMensalidade() {
    var configuracoes_simulador = getConfiguracoesSimularMensalidade($("select#tipo-simulacao").val());
    var valor_por_socio = parseFloat(configuracoes_simulador.valor_socio.replace(",", ".")).toFixed(2);
    var valor_por_funcionario = parseFloat(configuracoes_simulador.valor_funcionario.replace(",", ".")).toFixed(2);
    var valor_adicional_socio = 0;
    var valor_adicional_funcionario = 0;

    var formulario = $("form#formulario-simular-mensalidade");
    var socios = $(formulario).find("input#socios").val();
    var funcionarios = $(formulario).find("input#funcionarios").val();

    var faturamento = parseFloat($(formulario).find("select#faturamento").val().replace(",", ".")).toFixed(2);

    if (socios > configuracoes_simulador.maximo_socios) {
        valor_adicional_socio = parseFloat((socios - configuracoes_simulador.maximo_socios) * valor_por_socio).toFixed(2);
    }
    if (funcionarios > configuracoes_simulador.maximo_funcionarios) {
        valor_adicional_funcionario = parseFloat((funcionarios- configuracoes_simulador.maximo_funcionarios) * valor_por_funcionario).toFixed(2);
    }
    var total_mensalidade = parseFloat(faturamento) + parseFloat(valor_adicional_socio) + parseFloat(valor_adicional_funcionario);
    if ($(formulario).find("input#preco").val() != "") {
        var aux_preco = $(formulario).find("input#preco").val();
        var preco = aux_preco.replace(".","");
        var economia_mensalidade = (parseFloat(preco)*12) - (parseFloat(total_mensalidade)*12);
        if (economia_mensalidade > 0) {
            $("div#container-valores-simulacao").find("span#valor_economia").text(String(parseFloat(economia_mensalidade).toFixed(2)).replace(".", ","));
        } else {
            $("div#container-valores-simulacao").find("span#valor_economia").text("0,00");
        }
    }
    else {
        $("div#container-valores-simulacao").find("span#valor_economia").text("0,00");
    }

    $("div#container-valores-simulacao").find("span#valor_mensalidade").text(String(parseFloat(total_mensalidade).toFixed(2)).replace(".", ","));
}
function getConfiguracoesSimularMensalidade(id_tipo_servico) {
    var retorno;
    var urlAjax = site_url + "/ajax-simular-mensalidade";
    $.ajax({
        method: "POST",
        dataType: "JSON",
        data:{
            id_tipo_servico_calculadora: id_tipo_servico
        },
        url: urlAjax,
        async: false,
        success: function (resposta) {
            retorno = resposta;
        }
    });

    return retorno;
}
function getFaturamentosByTipoServico(id_tipo_servico) {
    var urlAjax = site_url + "/ajax-get-faturamento-by-servico";
    $.ajax({
        method: "POST",
        dataType: "JSON",
        url: urlAjax,
        async: false,
        data:{
            acao : "faturamentos_tipo_servico",
            id_tipo_servico_calculadora: id_tipo_servico
        },
        success: function (resposta) {
            var html = "";
            for(var cont = 0; cont<resposta.length; cont++){
                html += "<option value='"+resposta[cont]["valor"]+"'>"+resposta[cont]["descricao_faturamento"]+"</option>";
            }
            $("select#faturamento").html(html);
        }
    });

}