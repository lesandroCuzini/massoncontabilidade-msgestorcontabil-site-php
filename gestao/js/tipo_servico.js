$(document).ready(function () {
    $("#add_faturamento_tipo_servico").on("click", function () {

        var id_tipo_servico = $("#id_registro").val();
        var id_calculadora_faturamento = $("select#id_calculadora_faturamento").val();
        if (typeof id_tipo_servico != "undefined" && id_tipo_servico > 0) {
            $.ajax({
                url: window.location.href,
                method: 'post',
                data: {
                    acao: 'add_faturamento_tipo_servico',
                    id_tipo_servico: id_tipo_servico,
                    id_calculadora_faturamento: id_calculadora_faturamento,
                    valor: $("input#valor").val()
                },
                dataType: "json",
                success: function (response) {
                    $("select#id_calculadora_faturamento option[value='"+id_calculadora_faturamento+"']").remove();
                    getFaturamentosTiposServicos();
                }
            })
        }

    });
    $(document).on("click",'.excluir_tipo_servico_faturamento',function () {
        var linha = $(this).parent().parent();
        var id_calculadora_faturamento = $(this).parent().attr('data_id_calculadora_faturamento');
        var descricao_faturamento = $(linha).find('td:eq(0)').text();
        if (confirm("Deseja realmente apagar essa imagem?")) {
            $.ajax({
                url: window.location.href,
                method: 'post',
                data: {
                    acao: 'excluir_faturamento_tipo_servico',
                    id_tipo_servico_faturamento_calculadora: $(this).attr('data_id_tipo_servico_faturamento_calculadora'),
                },
                dataType: "json",
                success: function (response) {
                    getFaturamentosTiposServicos();
                    var option = "<option value="+id_calculadora_faturamento+">"+descricao_faturamento+"</option>";
                    $("select#id_calculadora_faturamento").append(option);

                }
            })
        }

    });
});
function getFaturamentosTiposServicos() {
    $.ajax({
        url: window.location.href,
        method: 'post',
        data: {
            acao: 'faturamentos_tipo_servico',
            id_tipo_servico_calculadora: $("#id_registro").val()
        },
        dataType: "JSON",
        success: function (response) {
            var html = "";
            for(var cont=0; cont<response.length; cont++){
                html += "<tr>";
                html +="<td>"+response[cont]["descricao_faturamento"]+"</td>";
                html +="<td>"+response[cont]["valor"]+"</td>";
                html +="<td data_id_calculadora_faturamento='"+response[cont]["id_calculadora_faturamento"]+"'><i data_id_tipo_servico_faturamento_calculadora='"+response[cont]["id_tipo_servico_faturamento_calculadora"]+"' class='fa fa-trash-o excluir_tipo_servico_faturamento' aria-hidden='true'></i></td>";
                html +="</tr>";
            }
            $("table#tabela_tipo_servicos_faturamentos tbody").html(html);
        }
    })

}