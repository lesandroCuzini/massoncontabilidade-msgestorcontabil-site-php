// JavaScript Document
$(document).ready(function(){
  
  $(".data").mask("99/99/9999");
  
  //Rotina para estabelecer efeitos onclick nas linhas
  $(".linha_lista").mouseover(function(){
    $(this).find("td").each(function(){
      $(this).css({
        "border-top":"solid 2px #506EC3",
        "border-bottom":"solid 2px #506EC3",
        "background-color":"#E0E6F5",
        "cursor":"pointer"
      })      
    });
  }).mouseout(function(){
    $(this).find("td").each(function(){
      $(this).css({
        "border-top":"",
        "border-bottom":"",
        "background-color":"",
        "cursor":"default"
      })      
    });
  });

  $(".linha_lista td").bind("click", function() {
    var href = $(this).parent().find(".btn_action").attr("href");
    if(!$(this).hasClass("actions"))      
      window.location.href = href;
  });
  
  $("#filter_data").datepicker({
    showOtherMonths: true,
    selectOtherMonths: true,
    dateFormat: "yy-mm-dd"
  });

  $(".btn_excluir").bind("click", function(){
    if(!confirm("Deseja realmente excluir esse registro?"))
      return false;
    var id = $(this).attr("data-id");
    var page = $(this).attr("data-page");
    $.ajax({
      type: "DELETE",
      url: $(".btn_ver_site").attr("href") + "/ajax-"+page+".html",
      data: {
        id: id
      },
      success: function() {
        window.location.reload();
      }
    });
  });
  
});

//Ordena a tabela
function sortTable(tabela, col_default, coluna, numero) {
  var imagem = $("#coluna_"+coluna).attr('src');
  var array_imagem = imagem.split("/");
  var diretorio = imagem.replace(array_imagem[array_imagem.length-1], "");
  numero = (numero) ? "numeric" : "ascii";
  
  $("img.ordem_colunas").attr("src", diretorio+"ordem_default.gif");  
  if (array_imagem[array_imagem.length-1] == "ordem_default.gif") {
    $("#coluna_"+coluna).attr('src', diretorio+"ordem_asc.gif");
    
    $("#"+tabela).sortTable({
      onCol: coluna, 
      keepRelationships: true,
      sortType: numero
    });
    
  } else if (array_imagem[array_imagem.length-1] == "ordem_asc.gif") {
    $("#coluna_"+coluna).attr('src', diretorio+"ordem_desc.gif");
    
    $("#"+tabela).sortTable({
      onCol: coluna, 
      keepRelationships: true, 
      sortDesc: true,
      sortType: numero
    });
    
  } else if (array_imagem[array_imagem.length-1] == "ordem_desc.gif") {
    $("#coluna_"+coluna).attr('src', diretorio+"ordem_default.gif");
    
    $("#"+tabela).sortTable({
      onCol: col_default, 
      keepRelationships: true,
      sortType: numero
    });
    
  }
  
}

//Verifica a página selecionada
function paginacao(form, total_paginas) {
  
  var pagina_selecionada = form.pag.value;  
  if (parseInt(pagina_selecionada) > parseInt(total_paginas)) {
    form.pag.value = total_paginas;
    
  } else if (parseInt(pagina_selecionada) < 1) {
    form.pag.value = 1;
    
  }  
  return true;
  
}

//Verifica se foi pressionado o enter
function enterPress(form, e) {
  
  var key_code = e.keyCode ? e.keyCode : e.charCode ? e.charCode :  e.which ? e.which : void 0;
  
  if (key_code == 13)
    form.submit();
}