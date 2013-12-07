// Formatos padrões
var mensagem_formato = "<img class='usuario_imagem' src='" + base_url + "assets/img/usuario/%s'></img> <span class='usuario_nome'>%s</span> <span class='usuario_login'>(@%s)</span> <span class='mensagem_data'>%s</span> <br /> <span class='mensagem_texto'>%s</span>"; 
var usuario_formato = "<img class='usuario_imagem' src='" + base_url + "assets/img/usuario/%s' /> <span class='usuario_nome'>%s</span> <br /> <a href='" + base_url + "usuario/perfil/%s' target='_blank'>@<span class='usuario_login'>%s</span></a>";

// Quando carregada a página
$(function($) {
	
	if (tecnologia != 'ws')
	{
		// Enviar mensagem
	    $('#nova_mensagem').bind('keypress', function(e) {
	       
	       	// Se pressionado ENTER
	        if (e.keyCode == 13)
	        {
	        	// Salvando mensagem
	            $.post(base_url + 'mensagem/salvar/' + canal_nome, {mensagem: $('#nova_mensagem').val()}, function(data) {
	                // Limpando
	                $('#nova_mensagem').val('');
	            }, 'json');
	        }
	        
	    });
	}
	
});