// Quando carregada a página
$(function($) {
	
	// Entrando
    $.post(base_url + 'canal/entrar/' + canal_nome, function(data) {
		// Iniciando processo de atualização
		atualizar();
	});
	
	// Definindo intervalo de atualização
	window.setInterval(atualizar, 3000);
	
});

function atualizar()
{
	// Última mensagem recebida
	var maior_que = $('#mensagem ul li').first().attr('id');
	
	// Atualizando
    $.post(base_url + 'canal/sp_atualizar/' + canal_nome, {maior_que: maior_que}, function(data) {
    	
    	// Mensagens
	  	$.each(data.mensagem, function() {
            $('#mensagem ul').prepend($('<li id="' + this._id.$id + '">' + sprintf(mensagem_formato, this.usuario.imagem, this.usuario.nome, this.usuario.login, $.format.date(this.data.sec*1000, 'dd/MM/yyyy HH:mm:ss'), this.texto) + '</li>').fadeIn('slow'));
        }); 
        
        // Limpando
        $('#usuario ul').html('');
        
        // Usuários
        $.each(data.usuario, function() {
        	$('#usuario ul').append($('<li id="' + this._id.$id + '">' + sprintf(usuario_formato, this.usuario.imagem, this.usuario.nome, this.usuario.login, this.usuario.login) + '</li>'));
        });
        
	}, 'json');
}