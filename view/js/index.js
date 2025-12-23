var id_registro = $('body').attr('id');
$(document).ready(function () {
    if ($('#home #banner').length > 0) {
        $('#home #banner .owl-carousel').owlCarousel({
            nav: true,
            dots: true,
            margin: false,
            animate: true,
            loop: false,
            width: true,
            height: true,
            responsive: {
                0: {items: 1},
                300: {items: 1},
                600: {items: 1},
                1000: {items: 1},
                1600: {items: 1},
                1800: {items: 1}
            },
            autoplay: true,
            autoplayTimeout: 8000
        });
    }

    $('header .menu-mobile ul').on('click', function () {
        if ($(this).hasClass('ativo')){
            $(this).removeClass('ativo');
            $("#menu").removeClass('active');
            $("#info_conteudo,header,footer").removeClass('active');
            $("body").removeClass('overflow');
        }else{
            $(this).addClass('ativo');
            $("#menu").addClass('active');
            $("#info_conteudo,header,footer").addClass('active');
            $("body").addClass('overflow');
        }
    });

    $(document).on('click','#comercializacao #informacao .fechar, #comercializacao #informacao .ocultar',  function () {
        $(this).parent().parent().removeClass('ativo');
    });
    $(document).on('click','#comercializacao #info .link',  function () {
        $(this).parent().find('#informacao').addClass('ativo');
    });

    $(document).on('click', '#botao', function () {
        $('html,body').animate({scrollTop: 0}, 0, function () {});
    });
    $("#botao").addClass('hidescroll');

    if (id_registro == 'contato') {
        $("#contato #form form").submit(function (event) {
            var form = $(this);
            if (!validaFormulario($("#contato #form form").selector)) {
                event.preventDefault();
            } else {
                $('#loading').css('display', 'flex');
                $('body').addClass('overflow');
                window.setTimeout(function () {
                    $.ajax({
                        url: window.location.href,
                        method: "POST",
                        data: {
                            nome: form.find('input#nome').val(),
                            email: form.find('input#email').val(),
                            telefone: form.find('input#telefone').val(),
                            localizacao: form.find('input#localizacao').val(),
                            mensagem: form.find('textarea#mensagem').val(),
                            acao: 'incluir',
                        },
                        success: function (resposta) {
                            console.log(resposta);
                            $('#loading').css('display', 'none');
                            if (resposta == 'ok') {
                                $('#form_mensagem .texto').html('Contato salvo com sucesso, em breve entraremos em contato com você.');
                                $('#form_mensagem').addClass('ativo');
                                $('#form_mensagem div.mensagem').addClass('success');
                            } else {
                                $('#form_mensagem .texto').html('Não foi possível realizar contato, tente novamente mais tarde...');
                                $('#form_mensagem').addClass('ativo');
                                $('#form_mensagem div.mensagem').removeClass('success');
                            }
                        },
                        error: function () {
                            $('#loading').css('display', 'none');
                            $('#form_mensagem .texto').html('Não foi possível realizar contato, tente novamente mais tarde...');
                            $('#form_mensagem').addClass('ativo');
                            $('#form_mensagem div.mensagem').removeClass('success');
                        }
                    });
                }, 1000);
                return false;
            }
        });
    }

    $('#form_mensagem i.icone_fechar').on('click', function () {
        $('body').removeClass('overflow');
        $('#form_mensagem').removeClass('ativo');
        if ($('#form_mensagem div.mensagem').hasClass('success')) {
            $('#form_mensagem div.mensagem').removeClass('success');
            $('form input,form textarea,form select').val('');
        }
    });

    if ($('input[id="telefone"]').length) {
        var telMask = ['(99) 9999-99999', '(99) 99999-9999'];
        var tel = document.querySelector('input[id="telefone"]');
        VMasker(tel).maskPattern(telMask[0]);
        tel.addEventListener('input', inputHandler.bind(undefined, telMask, 14), false);
    }
    var liberar = 6;
    $(document).on('click','#comercializacao #info .ver-mais', function () {
        var contador = 0;
        var liberados = 0;
        $('#comercializacao .grid').each( function () {
            if ($(this).hasClass('none')) {
                contador = contador + $(this).length;
                if (contador <= liberar) {
                    $(this).removeClass('none');
                    liberados = liberados + 1;
                }
            } else {
                liberados = liberados + 1;

            } 
        });
        if (liberados == $('#comercializacao #info').attr('count')) {
            $(this).remove();
        }
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

    window.sr = ScrollReveal({reset: true});
    sr.reveal('.top-scroll, #breadcrumb h1', {
        origin: "top",
        distance: "50px",
        duration: 1000,
        delay: 0,
        scale: 0
    });
    sr.reveal('.left-scroll', {
        origin: "left",
        distance: "50px",
        duration: 1000,
        delay: 0,
        scale: 0
    });
    sr.reveal('.right-scroll', {
        origin: "right",
        distance: "50px",
        duration: 1000,
        delay: 0,
        scale: 0
    });

    menu();
});
var top_offset_menu = $("header").height();
window.onresize = function () {
    menu();
    if (!$("header").hasClass('scroll')) {
        top_offset_menu = $("header").height();
    }
};
var returns = "";
$(document).scroll(function () {
    if (!$("header").hasClass('scroll')) {
        top_offset_menu = $("header").height();
    }
    var scroll_top = $(window).scrollTop();
    if (scroll_top >= top_offset_menu) {
        returns = 'true';
        if ($(window).width() > 992) {
            $("#botao").removeClass('hidescroll');
        }
    } else if (returns == 'true' && scroll_top < top_offset_menu) {
        if ($(window).width() > 992) {
            $("#botao").addClass('hidescroll');
        }
    }
});
function inputHandler(masks, max, event) {
    var c = event.target;
    var v = c.value.replace(/\D/g, '');
    var m = c.value.length > max ? 1 : 0;
    VMasker(c).unMask();
    VMasker(c).maskPattern(masks[m]);
    c.value = VMasker.toPattern(v, masks[m]);
}
function menu() {
    if($(window).width() > 992){
        $('#menu').insertAfter('header .logotipo');
        $("body").removeClass('overflow');
    } else {
        $('#menu').insertBefore('header');
    }
}
function validaFormulario(form) {

    var error = false;
    var camposVazios = [];

    // faz a validacao
    $(form).find("input , select, textarea").each(function () {

        $(this).parent().find(".erro-form").remove();
        // campos vázios
        if (id_registro == 'contato') {
            if (!$(this).hasClass("opcional") && $(this).val() == "" && $(this).attr("type") != "hidden" && !$(this).parent().hasClass('not')) {
                var erros = {
                    'campo': $(this).attr("id"),
                    'msg': "Este campo está vázio."
                };
                camposVazios.push(erros);
            }
            else {
                $(this).next(".erro-form").remove();
            }
        }else{
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
        }
    });
    if (camposVazios.length > 0) {
        //inputs
        for (var i = 0; i < camposVazios.length; i++) {
            $(form + " input" + "#" + camposVazios[i].campo).parent().append("<span class='erro-form'><span>" + camposVazios[i].msg + "</span></span>");
        }
        //select
        for (var y = 0; y < camposVazios.length; y++) {
            $(form + " select" + "#" + camposVazios[y].campo).parent().append("<span class='erro-form'><span>" + camposVazios[y].msg + "</span></span>");
        }
        //textarea
        for (var j = 0; j < camposVazios.length; j++) {
            $(form + " textarea" + "#" + camposVazios[j].campo).parent().append("<span class='erro-form'><span>" + camposVazios[j].msg + "</span></span>");
        }
        return false;
    }
    return true;
}