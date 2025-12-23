$(document).ready(function(){
	
	//$("div.login").effect( "shake" );
	//$("div.alert").delay(5000).slideUp();
	
	$('#form_login').submit(function(){
		var erro = false;
		if ($('#login').val().trim() == '') {
			$('#login').effect("shake");
			erro = true;
		}
		if ($('#senha').val().trim() == '') {
			$('#senha').effect("shake");
			erro = true;
		}
		if (erro) return false;
		
		//Verificar Login
		var captcha = 'nao';//($('.g-recaptcha').css('display') == 'block') ? 'sim': 'nao';
		var form = $(this).serialize();
		$.ajax({
		  	method: "POST",
		  	url: site_url+'/login.html?acao=login&validar_captcha='+captcha,
		  	dataType: 'json',
		  	data: form,
		  	success: function(retorno){
		  		if (retorno.qtde_tentativas >= 3) {
		  			$('.g-recaptcha').slideDown();
		  		}
		  		if (!retorno.sucesso) {
		  			var tempo_alerta = 5000;
					$('#login').val('');
					$('#senha').val('');
					switch(retorno.erro) {
						case 'erro_captcha':
							$("div.alert").html('Valide corretamente o campo <strong>reCaptcha "Não sou Robô"</strong>');
							$('.g-recaptcha').effect("shake");
							break;
						case 'erro_dia_nao_autorizado':
							$("div.alert").html('<strong>Você não pode acessar o painel hoje.</strong><br /> Já registramos seu IP atual ('+retorno.ip_remoto+') e o responsável pelo site será alertado sobre esta tentativa!');
							$('.g-recaptcha').effect("shake");
							tempo_alerta = 15000;
							break;
						case 'erro_horario_nao_autorizado':
							$("div.alert").html('<strong>Você não pode acessar o painel neste horário.</strong><br /> Já registramos seu IP atual ('+retorno.ip_remoto+') e o responsável pelo site será alertado sobre esta tentativa!');
							$('.g-recaptcha').effect("shake");
							break;
							tempo_alerta = 15000;
						default:
							$("div.alert").html('<strong>Usuário e/ou Senha Incorretos</strong>');
							$("div.login").effect("shake");
							break;
					}
					$("div.alert").slideDown().delay(tempo_alerta).slideUp();
				} else {
					window.location = retorno.redirect;
				}
		  	},
		  	error: function(err) {
		  		console.log(err);
		  		$("div.login").effect("shake");
		  	}
		});
		
		return false;
	});
});
