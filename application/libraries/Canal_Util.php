<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Canal_Util 
{
	/**
     * Instancia do codeigniter
	 * @var Object
     */
	private $ci;
	
	/**
     * Lista de clientes
	 * @var \SplObjectStorage
     */
	private $clientes;
	
	/**
	 * Inicia a biblioteca
	 *
	 * @param Object $ci - instancia do codeigniter
	 * @param \SplObjectStorage $clientes - lista de clientes
	 * @return void
	 */
	public function iniciar(&$ci, &$clientes)
	{
		$this->ci = $ci;
		$this->clientes = $clientes;
	}
	
	/**
	 * Envia uma mensagem para todos os clientes conectados no canal determinado
	 *
	 * @param string $canal - nome do canal
	 * @param string $texto
	 * @return void
	 */
	public function difundir($canal, $texto)
	{
		// Passandos pelos clientes
		foreach ($this->clientes as $cliente)
			// Se estiver no canal
			if ($cliente->canal['nome'] === $canal)
				// Enviando mensagem
				$cliente->recurso->send($texto);
	}
	
	/**
	 * Construindo mensagem a ser enviada
	 *
	 * @param Array $usuario - informações do usuário que envia a mensagem
	 * @param string $texto
	 * @return JSON
	 */
	public function mensagem($usuario, $texto)
	{
		$estilo = 'usuario';
		
		// Usuários especiais
		if (in_array($usuario['login'], array('servico_canal', 'sistema')))
			$estilo = $usuario['login'];

		// Construindo e retornando mensagem
		return json_encode(array(
								'acao' => 'mensagem', 
							  	'texto' => $this->tratar_texto($texto), 
							  	'usuario' => $usuario,
							  	'data' => new MongoDate(time()),
							  	'estilo' => $estilo
							 	)
							);
	}
	
	/**
	 * Aplicando links no texto
	 *
	 * @param string $texto
	 * @return string
	 */
	
	public function tratar_texto($texto)
	{
		// Substituindo canais
		$texto = preg_replace('/(^|\s)#(\w*[a-zA-Z_]+\w*)/', '\1<a href="' . base_url() . 'canal/$2" target="_blank">$0</a>', $texto);
		
		// Substituindo usuários
		$texto = preg_replace('#@(\w+)#', '<a href="' . base_url() . 'usuario/perfil/$1" target="_blank">$0</a> ', $texto);
		
		// Retornando
		return $texto;
	}
	
	/**
	 * Envia para o cliente os usuários que estão online no canal
	 *
	 * @param string $canal - nome do canal
	 * @return void
	 */
	public function usuarios_online($canal)
	{
		$clientes = array();
		
		// Passandos pelos clientes
		foreach ($this->clientes as $cliente)
			// Se estiver no canal
			if ($cliente->canal['nome'] === $canal)
				// Definindo cliente
				$clientes[] = array('usuario' => $cliente->usuario);
				
		// Atualizando usuários
		$mensagem = json_encode(array('acao' => 'usuario', 'clientes' => $clientes));
		$this->difundir($canal, $mensagem);
	}
	
	/**
	 * Selecionando cliente através da conexão
	 *
	 * @param ConnectionInterface $conn
	 * @return Cliente
	 */
	public function buscar_cliente_por_recurso($conn)
	{
		// Passandos pelos clientes
		foreach ($this->clientes as $cliente)
			// Se encontrado
			if ($cliente->recurso->resourceId === $conn->resourceId)
				// Retornando
				return $cliente;
		
		return null;
	}
	
}