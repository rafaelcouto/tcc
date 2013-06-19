<script type="text/javascript">
// Quando carregado a página
$(function($) {
 
    // Quando enviado o formulário
    $('#frmLogin').submit(function() {
 
        // Limpando mensagem de erro
        $('div.mensagem-erro').html('');
 
        // Mostrando loader
        $('div.loader').show();
 
        // Enviando informações do formulário via AJAX
        $(this).ajaxSubmit(function(resposta) {
 
            // Se não retornado nenhum erro
            if (!resposta)
                // Redirecionando para o painel
                window.location.href = '<?php echo site_url('usuario') ?>';
            else
            {
                // Escondendo loader
                $('div.loader').hide();
 
                // Exibimos a mensagem de erro
                $('div.mensagem-erro').html(resposta);
            }
 
        });
 
        // Retornando false para que o formulário não envie as informações da forma convencional
        return false;
 
    });
});
</script>

<form id="frmLogin" action="<?php echo site_url('usuario/entrar/') ?>" method="post">

    <fieldset>
        <legend>Entrar</legend>

        <div class="loader" style="display: none;"><?php echo img('icone/loader.gif', 'Carregando') ?></div>
        
        <div class="mensagem-erro"></div>

        <p>
            <label for="login">Usuário</label> <br />
            <input type="text" id="usuario" name="usuario" size="50" />
        </p>

        <p>
            <label for="password">Senha</label> <br />
            <input type="password" id="senha" name="senha" size="50" />
        </p>
		
		
		<p>
			<input type="radio" name="tecnologia" value="sp" checked>Short polling</input>
			<input type="radio" name="tecnologia" value="lp">Long polling</input>
			<input type="radio" name="tecnologia" value="sse">SSE</input>
			<input type="radio" name="tecnologia" value="ws">Websocket</input>
		</p>

        <input type="submit" value="Entrar" />
        
    </fieldset>
    
</form>