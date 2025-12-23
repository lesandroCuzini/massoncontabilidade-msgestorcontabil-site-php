$(document).ready(function () {
    if ($('form#formulario-simular-mensalidade').length) {
        simularMensalidade();
    }
    $("form#formulario-simular-mensalidade").on('change', ['input', 'select'], function () {
        //getFaturamentosByTipoServico($("select#tipo-simulacao").val());
        simularMensalidade();
    });
    placeholder();
    menu();
    $("header #menu nav ul.box_menu_small li.menu_small").on("click", function () {
        if ($("header #menu nav ul.box_menu_small li.menu_small").hasClass('active')) {
            $("header #menu nav ul.box_menu_small li.menu_small").removeClass('active');
            $("header #menu nav ul.box_menu_small li.menu_small").removeClass('fechado');
            $("header #menu nav ul.st-menu").slideToggle(400);
        }
        else {
            $("header #menu nav ul.box_menu_small li.menu_small").addClass('active');
            $("header #menu nav ul.box_menu_small li.menu_small").addClass('fechado');
            $("header #menu nav ul.st-menu").slideToggle(400);
        }
    });
    $('header #menu nav ul li span.menu-mobile-grover').on('click', function () {
        if ($(this).hasClass('grover')) {
            $(this).removeClass('grover');
            $(this).parent().children('ul').slideToggle(400);
        }
        else {
            $(this).addClass('grover');
            $(this).parent().children('ul').slideToggle(400);
        }
    });
    var page_id = document.getElementById('home');
    $("li.simule").on('click',function () {
        if (page_id == null) {
            window.location = site_url + '#simule';
            return false;
        }
        $('html, body').animate({scrollTop: $('div#info-mensalidades').offset().top - 194}, 1500);
        return false;
    });
    if(window.location.href.indexOf("#simule") > -1) {
        //alert("Opa, encontrou.");
        $('html, body').animate({scrollTop: $('div#info-contatos').offset().top - 500}, 1500);
    }
    if (($('#home #banners-detalhe img').length == 1)) {
        $('#home #banners-detalhe div.item').css('margin', '0px');
    }
    else {
        $('#home #banners-detalhe div.item').css('margin', '0px 5px');
    }
    if ($('#home #banners-detalhe').length > 0) {
        $('#home #banners-detalhe').owlCarousel({
            items: 1,
            stagePadding: ($('#home #banners-detalhe img').length > 1 ? getStagePadding() : false),
            nav: true,
            thumbs: true,
            center: true,
            callbacks: true,
            loop: ($('#home #banners-detalhe img').length > 1 ? true : false),
            navigation: true,
            autoplayHoverPause: true,
            responsiveClass: true,
            margin: 0,
            autoheight: true,
            animate: true,
            Default: 0,
            fallbackEasing: false,
            dots: true,
            dotsSpeed: 1500,
            navSpeed: 1500,
            URLhashListener: true,
            startPosition: 'URLHash',
            autoplay: true,
            autoplayTimeout: 10000,
            autoplaySpeed: 2500,
            callback: true
        });
    }
    if ($('#home #banners-parceiros').length > 0) {
        $('#home #banners-parceiros').owlCarousel({
            navigation: true,
            autoplayHoverPause: true,
            responsiveClass: true,
            margin: 0,
            autoheight: true,
            animate: true,
            Default: 0,
            fallbackEasing: false,
            nav: true,
            dots: false,
            loop: ($('#home #banners-parceiros img').length > 4 ? true : false),
            width: true,
            height: true,
            responsive: {
                0: {items: 2},
                300: {items: 2},
                600: {items: 2},
                1000: {items: 4},
                1600: {items: 4},
                1800: {items: 4}
            },
            autoplay: true,
            autoplayTimeout: 10000,
            autoplaySpeed: 2500,
            callback: true
        });
    }
    var imagens = $('#home #banners-parceiros img');
    if(imagens.length < 4){
        $("#home #banners-parceiros div.owl-stage").css('margin','0px auto');
    }
    else {
        return false;
    }
    $(window).scroll(function () {
        if ($(this).scrollTop() > 150) {
            $("#botao").removeClass("hide");
        }
        else {
            $("#botao").addClass("hide");
        }
        if ($(window).width() > 1024) {
            if ($(this).scrollTop() > 200) {
                $("body").css("padding-top", "326px");
                $('header #logotipo').parent('div').addClass("menu-scroll");
            }
            else {
                $("body").css("padding-top", "0px");
                $('header #logotipo').parent('div').removeClass("menu-scroll");
            }
        }
        if ($(window).width() < 1023) {
            $("body").css("padding-top", "0px");
            $('header #logotipo').parent('div').removeClass("menu-scroll");
        }
    });
    if ($(this).scrollTop() > 200) {
        $("#botao").removeClass("hide");
    }
    else {
        $("#botao").addClass("hide");
    }
    if ($(window).width() > 1024) {
        if ($(this).scrollTop() > 200) {
            $('header #logotipo').parent('div').addClass("menu-scroll");
        }
        else {
            $('header #logotipo').parent('div').removeClass("menu-scroll");
        }
    }
    if ($(window).width() < 1023) {
        $('header #logotipo').parent('div').removeClass("menu-scroll");
    }
    var home = document.getElementById('home');
    if (home != null) {
        $("header #menu div.logo-scroll").click(function () {
            $('html,body').animate({scrollTop: 0}, 1500, function () {
            });
        });
    }
    $("#botao").click(function () {
        $('html,body').animate({scrollTop: 0}, 1500, function () {
        });
    });

    if (!$("header #logotipo div.banner-topo").length) {
        $("header #logotipo").each(function () {
            $(this).parent().addClass('header');
            $("header #contatos").addClass('hide-contato');
        })
    }
    var contato = document.getElementById('contato');
    if (contato != null) {
        falidarformulario();
    }
    $("form#form-newsletter").on("submit", function () {
        var email = $(this).find("input#email").val();
        if (validaForm(this)) {
            cadastrarNewsLetter(email);
        }
        return false;
    });
    if ($('#home #perguntas div.padding').length == 0) {
        console.log($(this).children('padding').length);
        $('#home #perguntas h2').parent().removeClass('large-6');
        $('#home #perguntas h2').parent().addClass('text-center');
        $('#home #perguntas h2').addClass('margin');
        $('#home #perguntas #buttom div').css('margin', '0px auto 40px');
    }
    $("select#tipo-simulacao").on('change',function () {
        getFaturamentosByTipoServico($("select#tipo-simulacao").val());
    });
    $('#texto-cookie span').on('click', function () {
        $.ajax({
            type: 'POST',
            url: site_url + '/cookie',
            data: {
                ativar: 'cookie'
            },
            success: function (retorno) {
                if(retorno == 'ok'){
                    $('#texto-cookie').slideUp(200);
                }
            },
        });
    });
});
$(document).scroll(function () {
    var top_offset_menu = 250;
    var scroll_top = $(window).scrollTop();
    if (scroll_top >= top_offset_menu) {
        $("#botao").removeClass('hide');
    } else {
        $("#botao").addClass('hide');
    }
});
window.onresize = function () {
    if ($(window).width() > 1023) {
        $("header #menu nav ul.st-menu").css("display", "block");
    }
    else if ($("header #menu nav ul.box_menu_small li.menu_small").css('fechado')) {
        $("header #menu nav ul.st-menu").css("display", "none");
    }
    if ($(window).width() < 1023) {
        if ($("header #menu nav ul.box_menu_small li.menu_small").hasClass('active')) {
            $("header #menu nav ul.st-menu").css("display", "block");
        }
        else {
            $("header #menu nav ul.st-menu").css("display", "none");
            $("header #menu nav ul.box_menu_small li.menu_small").removeClass('active');
        }
    }
    if ($(window).width() > 1024) {
        if ($(this).scrollTop() > 200) {
            $('header #logotipo').parent('div').addClass("menu-scroll");
        }
        else {
            $('header #logotipo').parent('div').removeClass("menu-scroll");
        }
    }
    if ($(window).width() < 1023) {
        $('header #logotipo').parent('div').removeClass("menu-scroll");
    }
    var teste = document.getElementById('home');
    if (teste != null) {
        $("header #menu div.logo-scroll").click(function () {
            $('html,body').animate({scrollTop: 0}, 1500, function () {
            });
        });
    }
    var contato = document.getElementById('contato');
    if (contato != null) {
        falidarformulario();
    }
    placeholder();
    menu();
};
function getStagePadding() {
    var stagePadding = 350;
    var window_width = $(window).width();
    if (window_width > 640 && window_width < 1024) {
        stagePadding = 100;
    } else if (window_width < 640) {
        stagePadding = 50;
    }
    return stagePadding;
}
function placeholder() {
    $("footer #info-footer div.Newsletter form input").on("focus", function () {
        $(this).parent().children('label').css('color', '#585858');
        var getplaceholder = $(this).attr('placeholder');
        this.setAttribute('placeholder', '');
        var reporAttr = function () {
            this.setAttribute('placeholder', getplaceholder);
        };
        this.addEventListener('blur', reporAttr);
    });
}
function menu() {
    var teste = document.getElementById('home');
    if (teste != null) {
        if ($(window).width() < 640) {
            $("header #logotipo").each(function () {
                $(this).parent().addClass('header');
                $("header #contatos").addClass('hide-contato');
            });
        }
        if ($(window).width() > 640) {
            $("header #logotipo").each(function () {
                if ($("header #logotipo div.banner-topo img").attr('src') != null) {
                    $(this).parent().removeClass('header');
                    $("header #contatos").removeClass('hide-contato');
                }
            });
        }
    }
}
function validaForm(form, place) {
    var elementos = form.elements;
    console.log(elementos);
    var border;
    for (var i = 0; i < elementos.length; i++) {
        // ignorar campos com continue
        if ($("#" + elementos[i].id).hasClass("opcional"))
            continue;
        // teste próprio para emails
        if ((elementos[i].name == "email" && !checkMail(elementos[i].value)) ||
            (place && elementos[i].value == $("#" + elementos[i].id).attr("data-place"))) {
            elementos[i].focus();
            border = elementos[i].style.border;
            elementos[i].style.border = "1px solid #CC0000";
            setTimeout(function () {
                elementos[i].style.border = border;
            }, 1500);
            return false;
        }
        // campos-padrão
        else if (elementos[i].value == "" || (place && elementos[i].value == $("#" + elementos[i].id).attr("data-place"))) {
            elementos[i].focus();
            border = elementos[i].style.border;
            elementos[i].style.border = "1px solid #CC0000";
            setTimeout(function () {
                elementos[i].style.border = border;
            }, 1500);
            return false;
        }
    }
    return true;
}
function checkMail(email) {
    var er = /^[a-zA-Z0-9][a-zA-Z0-9\._-]+@([a-zA-Z0-9\._-]+\.)[a-zA-Z-0-9]{2}/;
    if (er.exec(email))
        return true;
    else
        return false;
}

function falidarformulario() {
    $('#telefone').mask('(00) 0000-00000');
    $('#celular').focusout(function () {
        var phone, element;
        element = $(this);
        element.unmask();
        phone = element.val().replace(/\D/g, '');
        if (phone.length > 10) {
            element.mask("(99) 99999-9999");
        } else {
            element.mask("(99) 9999-99999");
        }
    }).trigger('focusout');
}
function cadastrarNewsLetter(email) {
    var urlAjax = site_url + "ajax-news.html";
    $.ajax({
        method: "POST",
        url: urlAjax,
        dataType: "html",
        data: {
            acao: "add_news_site",
            ajax: true,
            email: email
        },
        success: function (resposta) {
            if (resposta == "ok") {
                $("footer span#feedback-newsletter").text("Cadastro no Newsletter feito com sucesso!");
                $("footer span#feedback-newsletter").removeClass("feedback-erro");
                $("footer span#feedback-newsletter").addClass("feedback-ok");
            } else {
                $("footer span#feedback-newsletter").text("Email já cadastrado no Newsletter!");
                $("footer span#feedback-newsletter").removeClass("feedback-ok");
                $("footer span#feedback-newsletter").addClass("feedback-erro");
            }
            $("footer span#feedback-newsletter").slideDown();
        },
        error: function () {
            $("footer span#feedback-newsletter").text("Erro ao cadastrar no Newsletter,por favor tente novamente!");
            $("footer span#feedback-newsletter").addClass("feedback-erro");
            $("footer span#feedback-newsletter").css("display", "block");
        }
    });

    setTimeout(function () {
        $("footer span#feedback-newsletter").slideUp();
    }, 4000);

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
        if(economia_mensalidade > 0) {
            $("div#container-valores-simulacao").find("span#valor_economia").text(String(parseFloat(economia_mensalidade).toFixed(2)).replace(".", ","));
        }else{
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
    var urlAjax = site_url + "/ajax-simular-mensalidade.html";
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
    var urlAjax = site_url + "/ajax-get-faturamento-by-servico.html";
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