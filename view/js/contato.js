$(document).ready(function() {
    $('#form-contact').submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = form.serializeArray();
        formData.push({name: 'acao', value:'setContato'});
        if (validaFormulario("#form-contact")) {
            $.ajax({
                method: "post",
                url: site_url + "/contato",
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if (data.retorno == 'sucesso') {
                        $('#modalSuccess').modal('show');
                        $('#form-contact').each(function () {
                            this.reset();
                        });
                    } else {
                        $('#modalFail').modal('show');
                    }
                },
                error: function (err) {
                    $('#modalFail').modal('show');
                }
            });
        }
    });
});

function validaFormulario(form) {

    var error = false;
    var camposVazios = [];

    // faz a validacao
    $(form).find("input , select, textarea").each(function () {
        if (typeof $(this).attr('required') != "undefined") {
            // Remove msg de erro para INPUT
            $(this).parent().find(".erro-form").remove();
            // Remove msg de erro para SELECT
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
            $(form + " input" + "#" + camposVazios[i].campo).parent().append("<span style='position: absolute;font-size: 12px;color: #ed1c24;' class='erro-form'>" + camposVazios[i].msg + "</span>");
        }
        // Selects
        for (var j = 0; j < camposVazios.length; j++) {
            $(form + " select" + "#" + camposVazios[j].campo).parent().parent().append("<span style='position: absolute;font-size: 12px;color: #ed1c24;' class='erro-form'>" + camposVazios[j].msg + "</span>");
        }
        // Textarea
        for (var j = 0; j < camposVazios.length; j++) {
            $(form + " textarea" + "#" + camposVazios[j].campo).parent().append("<span style='position: absolute;font-size: 12px;color: #ed1c24;' class='erro-form'>" + camposVazios[j].msg + "</span>");
        }
        return false;
    }
    return true;
}

function checkMail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}