<script type="text/javascript">
	var canal_nome = '<?php echo $canal['nome'] ?>';
</script>

<div id="canal">
	
	 <div id="header">
        
        <div id="imagem"><?php echo img('canal/' . $canal['imagem']) ?></div>
        
        <h2>#<?php echo $canal['nome'] ?></h2>
        
        <div id="topico"><?php echo $canal['topico'] ?></div>
        
    </div>
    
    <div id="latencia"></div>
 	<div id="memoria"></div>
     
    <input type="text" id="nova_mensagem" placeholder="Digite uma mensagem aqui" />

	<div id="mensagem">
		<ul></ul>
	</div>
	
	<div id="usuario">
		<ul></ul>
	</div>
	
	<div style="clear: both"></div>
	
</div>
