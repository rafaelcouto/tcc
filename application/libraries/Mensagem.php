<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mensagem 
{
	private $ci;
	private $clientes;

	public function iniciar(&$ci, &$clientes)
	{
		$this->ci = $ci;
		$this->clientes = $clientes;
	}
	
	/**
	 * Envia uma mensagem para todos os clientes conectados no canal determinado
	 *
	 * @param string $canal nome do canal
	 * @param string $mensagem
	 * @return void
	 */
	 
	public function broadcast($canal, $mensagem)
	{
		// Passandos pelos clientes
		foreach ($this->clientes as $cliente)
			// Se estiver no canal
			if ($cliente->canal['nome'] === $canal)
				// Enviando mensagem
				$cliente->recurso->send($mensagem);
	}
	
	/**
	 * Selecionando cliente através da conexão
	 *
	 * @param Array $usuario usuário que envia a mensagem
	 * @param string $mensagem
	 * @return JSON
	 */
	 
	public function construir($usuario, $mensagem)
	{
		$estilo = 'usuario';
		
		// Usuários especiais
		if (in_array($usuario['login'], array('servico_canal', 'sistema')))
			$estilo = $usuario['login'];

		// Substituindo canais
		$mensagem = preg_replace('/(^|\s)#(\w*[a-zA-Z_]+\w*)/', '\1<a href="' . base_url() . 'canal/$2" target="_blank">$0</a>', $mensagem);
		
		// Substituindo usuários
		$mensagem = preg_replace('#@(\w+)#', '<a href="' . base_url() . 'usuario/perfil/$1" target="_blank">$0</a> ', $mensagem);
		
		// Construindo e retornando mensagem
		return json_encode(array(
								'acao' => 'mensagem', 
							  	'texto' => $mensagem, 
							  	'usuario' => $usuario,
							  	'data' => new MongoDate(time()),
							  	'estilo' => $estilo
							 	)
							);
	}
	
}