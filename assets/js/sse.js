$(function($) {
	
	// Entrando
    $.post(base_url + 'canal/entrar/' + canal_nome, function(data) {
		
		var es = new EventSource(base_url + 'canal/sse_atualizar/' + canal_nome);

		es.addEventListener('message', function(event) {
		    
		    var data = $.parseJSON(event.data);
		    
		    // Usuários
		    if (data.usuario != null)
		    {
		    	$('#usuario ul').empty();
		    
		    	$.each(data.usuario, function() {
		            $('#usuario ul').append($('<li id="' + this._id.$id + '">' + sprintf(usuario_formato, this.usuario.imagem, this.usuario.nome, this.usuario.login, this.usuario.login) + '</li>'));
		        });
		    }
		        
		    // Mensagens
		    if (data.mensagem != null)
			    $.each(data.mensagem, function() {
			        $('#mensagem ul').prepend($('<li id="' + this._id.$id + '">' + sprintf(mensagem_formato, this.usuario.imagem, this.usuario.nome, this.usuario.login, $.format.date(this.data.sec*1000, 'dd/MM/yyyy HH:mm:ss'), this.texto) + '</li>').fadeIn('slow'));
			    }); 
		    
		}, false);
		
	});
	
});
