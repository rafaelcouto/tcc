<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Websocket implements MessageComponentInterface  
{
	
	/**
     * Instancia do codeigniter
	 * @var Object
     */
	private $ci;
	
	/**
     * Clientes conectados ao servidor
	 * @var \SplObjectStorage
     */
	protected $clientes;
	
	/**
     * Parâmetros recebidos do cliente
	 * @var Object
     */
	private $dados;
	
	/**
	 * Construtor
	 *
	 * @return void
	 */
	 
    public function __construct() 
    {
    	$this->ci =& get_instance();
        $this->clientes = new \SplObjectStorage;
		
		// Carregando modelos
		$this->ci->load->model(array('Usuario_Model', 'Online_Model', 'Canal_Model'));
		
		// Biblioteca de canal
		$this->ci->load->library(array('Canal_Util', 'Cliente'));
		$this->ci->canal_util->iniciar($this->ci, $this->clientes);
    }
	
	/**
	 * Quando iniciado uma conexão
	 *
	 * @param ConnectionInterface $recurso
	 * @return void
	 */
    public function onOpen(ConnectionInterface $recurso) 
    {
    	$cliente = new Cliente();
		$cliente->recurso = $recurso;
		
    	// Adicionando recurso
        $this->clientes->attach($cliente);
    }
	
	/**
	 * Quando fechado uma conexão
	 *
	 * @param ConnectionInterface $recurso
	 * @return void
	 */
	public function onClose(ConnectionInterface $recurso) 
	{
		// Passandos pelos clientes
		foreach ($this->clientes as $cliente)
		{
			// Se encontrado
			if ($cliente->recurso->resourceId === $recurso->resourceId)
			{
				// Removendo cliente
				$this->clientes->detach($cliente);
				break;
			}
		}

		// Enviando mensagem de saída
		$mensagem = $this->ci->canal_util->mensagem($this->ci->Usuario_Model->usuario_canal, sprintf('@%s saiu no canal', $cliente->usuario['login']));
		$this->ci->canal_util->difundir($cliente->canal['nome'], $mensagem);
		
		// Atualizando usuários
		$this->ci->canal_util->usuarios_online($cliente->canal['nome']);
		
		// Log
		echo sprintf('@%s saiu do canal #%s', $cliente->usuario['login'], $cliente->canal['nome']) . PHP_EOL;
    }
	
	/**
	 * Quando ocorrer algum erro de conexão
	 *
	 * @param ConnectionInterface $recurso
	 * @param \Exception $e
	 * @return void
	 */
    public function onError(ConnectionInterface $recurso, \Exception $e) 
    {
    	// Selecionando cliente
		$cliente = $this->ci->canal_util->buscar_cliente_por_recurso($recurso);

        // Fechando conexão
        $conn->close();
		
		// Log
		echo sprintf('@%s foi desconectado do canal #%s com erro: ' . $e->getMessage(), $cliente->usuario['login'], $cliente->canal['nome']) . PHP_EOL;
    }
	
	/**
	 * Quando recebido alguma mensagem do cliente
	 *
	 * @param ConnectionInterface $recurso
	 * @param string $msg
	 * @return void
	 */
    public function onMessage(ConnectionInterface $recurso, $msg) 
    {
    	$retorno = null;
		
    	// Decodificando mensagem
		$this->dados = json_decode($msg);
		
		// Se não houver ação
		if (!isset($this->dados->acao))
		{
			$retorno = 'Nenhuma ação definida';
		}
		else 
		{
			// Definindo ação
			switch ($this->dados->acao) 
			{
				// Entrar em um canal
			    case 'entrar':
					$retorno = $this->entrar($recurso);
					break;
				// Enviar uma mensagem
				case 'enviar_mensagem':
					$retorno = $this->enviar_mensagem($recurso);
					break;
				default:
					$retorno = 'Nenhuma ação encontrada';
			}
		}
		
		// Enviando retorno
		if (!empty($retorno))
			$recurso->send($this->ci->canal_util->mensagem($this->ci->Usuario_Model->usuario_sistema, $retorno));
			
    }
	
	/**
	 * Envia mensagem de um cliente para os outros conectados ao canal
	 *
	 * @param ConnectionInterface $recurso
	 * @return mixed
	 */
	private function enviar_mensagem($recurso)
	{
		$this->dados->texto = htmlspecialchars($this->dados->texto);
		
		// Validações
		// Texto
		if (empty($this->dados->texto))
			return 'Nenhuma mensagem definida';
		
		if (strlen($this->dados->texto) > 140)
			return 'A mensagem deve ter no máximo 250 caracteres';
		
		// Canal
		if (empty($this->dados->canal))
			return 'Nenhum canal definido';
		
		// Selecionando cliente
		$cliente = $this->ci->canal_util->buscar_cliente_por_recurso($recurso);
		
		// Se não existir
		if (empty($cliente))
			return 'Usuário não encontrado';
		
		// Enviando mensagem	 
		$this->ci->canal_util->difundir($cliente->canal['nome'], $this->ci->canal_util->mensagem($cliente->usuario, $this->dados->texto));
		
		// Log
		echo sprintf('@%s enviou uma mensagem para o canal #%s', $cliente->usuario['login'], $cliente->canal['nome']) . PHP_EOL;
		
		// Sucesso
		return null;
	}
	
	/**
	 * Registra que o usuário está no canal
	 *
	 * @param ConnectionInterface $ws
	 * @return mixed
	 */
    private function entrar($recurso)
	{
		// Validações
		// Usuário
		if (empty($this->dados->login))
			return 'Usuário não definido';
		
		// Senha
		if (empty($this->dados->senha))
			return 'Senha não definida';
			
		// Canal
		if (empty($this->dados->canal))
			return 'Canal não definido';

		// Selecionando usuário
		$usuario = $this->ci->Usuario_Model->buscar_por_autenticacao($this->dados->login, $this->dados->senha);
		
		// Se não existir
		if (empty($usuario))
			return 'Login ou senha inválido';
		
		// Selecionando canal
		$canal = $this->ci->Canal_Model->buscar_por_nome($this->dados->canal);
		
		// Se não existir
		if (empty($canal))
			return 'Canal não encontrado';

		// Passandos pelos clientes
		foreach ($this->clientes as $cliente)
		{
			// Se encontrado
			if ($cliente->recurso->resourceId === $recurso->resourceId)
			{
				// Definindo usuário e canal
				$cliente->usuario = $usuario;
				$cliente->canal = $canal;
				
				// Finalizando busca
				break;
			}			
		}

		// Enviando
		$mensagem = $this->ci->canal_util->mensagem($this->ci->Usuario_Model->usuario_canal, sprintf('@%s entrou no canal', $cliente->usuario['login']));
		$this->ci->canal_util->difundir($cliente->canal['nome'], $mensagem);
		
		// Atualizando usuários
		$this->ci->canal_util->usuarios_online($cliente->canal['nome']);
		
		// Log
		echo sprintf('@%s entrou no canal #%s', $cliente->usuario['login'], $cliente->canal['nome']) . PHP_EOL;
		
		// Sucesso
		return null;
	}

}