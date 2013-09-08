<script type="text/javascript">
// Quando carregado a página
$(function($) {
 
    // Quando enviado o formulário
    $('.form-horizontal').submit(function() {
 
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

<script type="text/javascript">
    
    $(function($) {
      
        // Se enviado formulário
        $('.form-horizontal').submit(function() {
            
            $('.alert').hide();
            $('.loader').show();
            
            // Enviando formulário
            $(this).ajaxSubmit({
                
                dataType: 'json',
                
                // Se enviado com sucesso
                success : function(data) 
                {
                    if (data.status == 0)
                    {
                        $('.alert .text').html(data.text);
                        $('.alert').fadeIn();
                    }
                    else
                        window.location.href = base_url + 'usuario';
                        
                    $('.loader').hide();
                }
                
            });
            
            return false;
        
        });
 
    });
    
</script>

<form class="form-horizontal" method="post" action="<?php echo site_url('usuario/entrar/') ?>">
    
  <legend>Entrar</legend>
  
  <div class="alert alert-danger" style="display:none;">
    <button type="button" class="close">×</button>
    <span class="text"></span>
  </div>

  <div class="control-group">
    <label class="control-label" for="usuario">Usuário</label> 
    <div class="controls">
      <input type="text" name="usuario" placeholder="Usuário"> 
    </div>
  </div>
  
  <div class="control-group">
    <label class="control-label" for="senha">Senha</label>
    <div class="controls">
      <input type="password" name="senha" placeholder="Senha">
    </div>
  </div>
  
  <div class="control-group">
    <label class="control-label" for="tecnologia">Tecnologia</label>
    <div class="controls">
	    <select name="tecnologia">
		  <option value="sp">Short polling</option>
		  <option value="lp">Long polling</option>
		  <option value="sse">SSE</option>
		  <option value="ws">Websocket</option>
		</select>
	</div>
  </div>

  <div class="control-group">
    <div class="controls">
      <button type="submit" class="btn"><i class="icon-ok-sign"></i> Entrar</button>
      <span class="loader">&nbsp;<?php echo img('loader.gif') ?></span>
    </div>
  </div>

</form>