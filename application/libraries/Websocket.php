<?php 
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
		
		// Biblioteca de mensagens
		$this->ci->load->library('Mensagem');
		$this->ci->mensagem->iniciar($this->ci, $this->clientes);
    }
	
	/**
	 * Quando iniciado uma conexão
	 *
	 * @return void
	 */
	 
    public function onOpen(ConnectionInterface $conn) 
    {
    	$this->ci->load->library('Cliente');
		
    	$cliente = new Cliente();
		$cliente->recurso = $conn;
		
    	// Adicionando recurso
        $this->clientes->attach($cliente);
    }
	
	/**
	 * Quando fechado uma conexão
	 *
	 * @return void
	 */
	 
	public function onClose(ConnectionInterface $conn) 
	{
		// Passandos pelos clientes
		foreach ($this->clientes as $cliente)
		{
			// Se encontrado
			if ($cliente->recurso->resourceId === $conn->resourceId)
			{
				// Removendo cliente
				$this->clientes->detach($cliente);
				break;
			}
		}

		// Enviando mensagem de saída
		$mensagem = $this->ci->mensagem->construir($this->ci->Usuario_Model->usuario_canal, sprintf('@%s saiu no canal', $cliente->usuario['login']));
		$this->ci->mensagem->broadcast($cliente->canal['nome'], $mensagem);
		
		// Atualizando usuários
		$this->usuarios_online($cliente->canal['nome']);
		
		// Log
		echo sprintf('@%s saiu do canal #%s', $cliente->usuario['login'], $cliente->canal['nome']) . PHP_EOL;
    }
	
	/**
	 * Quando ocorrer alguma exceção
	 *
	 * @return void
	 */
	 
    public function onError(ConnectionInterface $conn, \Exception $e) 
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
	
	/**
	 * Quando recebido alguma mensagem do cliente
	 *
	 * @return void
	 */
	
    public function onMessage(ConnectionInterface $conn, $msg) 
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
					$retorno = $this->entrar($conn);
					break;
				// Enviar uma mensagem
				case 'enviar_mensagem':
					$retorno = $this->enviar_mensagem($conn);
					break;
				default:
					$retorno = 'Nenhuma ação encontrada';
			}
		}
		
		// Enviando retorno
		if (!empty($retorno))
			$conn->send($this->ci->mensagem->construir($this->ci->Usuario_Model->usuario_sistema, $retorno));
			
    }
	
	/**
	 * Envia mensagem de um cliente para os outros conectados ao canal
	 *
	 * @param ConnectionInterface $ws
	 * @return mixed
	 */
	 
	private function enviar_mensagem($conn)
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
		$cliente = $this->get_cliente($conn);
		
		// Se não existir
		if (empty($cliente))
			return 'Cliente não encontrado';
		
		// Se comando
        if (substr($this->dados->texto, 0, 1) == '/')
        {
            $this->ci->load->library('bash', array('clientes' => $this->clientes));
			$this->ci->bash->iniciar($this->ci, $this->clientes);
			$retorno = $this->ci->bash->interpretar($cliente, $this->dados->texto);
			
			// Se houver retorno
			if (!empty($retorno))
            	$conn->send($this->ci->mensagem->construir($this->ci->Usuario_Model->usuario_sistema, $retorno));

			// Finalizando
           	return;
        } 
		
		// Enviando mensagem	 
		$this->ci->mensagem->broadcast($cliente->canal['nome'], $this->ci->mensagem->construir($cliente->usuario, $this->dados->texto));
		
		// Log
		echo sprintf('@%s enviou uma mensagem para o canal #%s', $cliente->usuario['login'], $cliente->canal['nome']) . PHP_EOL;
	}
	
	/**
	 * Registra que o usuário está no canal
	 *
	 * @param ConnectionInterface $ws
	 * @return mixed
	 */
	 
    private function entrar($conn)
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
		$usuario = $this->ci->Usuario_Model->get_by_auth($this->dados->login, $this->dados->senha);
		
		// Se não existir
		if (empty($usuario))
			return 'Login ou senha inválido';
		
		// Selecionando canal
		$canal = $this->ci->Canal_Model->get_by_nome($this->dados->canal);
		
		// Se não existir
		if (empty($canal))
			return 'Canal não encontrado';
		
		// Passandos pelos clientes
		foreach ($this->clientes as $cliente)
		{
			// Se encontrado
			if ($cliente->recurso->resourceId === $conn->resourceId)
			{
				// Definindo usuário e canal
				$cliente->usuario = $usuario;
				$cliente->canal = $canal;
				
				// Finalizando busca
				break;
			}			
		}
		
		// Enviando
		$mensagem = $this->ci->mensagem->construir($this->ci->Usuario_Model->usuario_canal, sprintf('@%s entrou no canal', $cliente->usuario['login']));
		$this->ci->mensagem->broadcast($cliente->canal['nome'], $mensagem);
		
		// Atualizando usuários
		$this->usuarios_online($cliente->canal['nome']);
		
		// Log
		echo sprintf('@%s entrou no canal #%s', $cliente->usuario['login'], $cliente->canal['nome']) . PHP_EOL;
	}
	
	/**
	 * Envia para o cliente os usuários que estão online no canal
	 *
	 * @param string $canal nome do canal
	 * @return void
	 */
	 
	private function usuarios_online($canal)
	{
		$usuarios = array();
		
		// Passandos pelos clientes
		foreach ($this->clientes as $cliente)
			// Se estiver no canal
			if ($cliente->canal['nome'] === $canal)
				// Enviando mensagem
				$usuarios[] = $cliente->usuario;

		// Atualizando usuários
		$mensagem = json_encode(array('acao' => 'usuario', 'usuarios' => $usuarios));
		$this->ci->mensagem->broadcast($canal, $mensagem);
	}
	
	
	
	/**
	 * Selecionando cliente através da conexão
	 *
	 * @param ConnectionInterface $conn
	 * @return Cliente
	 */
	 
	private function get_cliente($conn)
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