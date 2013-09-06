<h3>Lista de canais</h3>

<ul>
	<?php foreach ($canais as $canal): ?>
		<li><?php echo anchor('canal/' . $canal['nome'], '#' . $canal['nome']) ?></li>
	<?php endforeach ?>
</ul>

<p><?php echo anchor('usuario/sair', 'Sair') ?></p>
