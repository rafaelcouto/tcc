<?php 
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Websocket implements MessageComponentInterface  {
	
	private $ci;
	protected $usuarios;
	private $dados;
	
	/**
	 * Construtor
	 *
	 * @return void
	 */
	 
    public function __construct() 
    {
    	$this->ci =& get_instance();
        $this->usuarios = new \SplObjectStorage;
		
		// Carregando modelos
		$this->ci->load->model(array('Usuario_Model', 'Online_Model', 'Canal_Model'));
    }
	
	/**
	 * Quando iniciado uma conexão
	 *
	 * @return void
	 */
	 
    public function onOpen(ConnectionInterface $conn) 
    {
    	// Adicionando recurso
        $this->usuarios->attach($conn);
    }
	
	/**
	 * Quando fechado uma conexão
	 *
	 * @return void
	 */
	 
	public function onClose(ConnectionInterface $conn) 
	{
		// Removendo recurso
        $this->usuarios->detach($conn);
        
		// Selecionando usuário
		$online = $this->ci->Online_Model->get_by_recurso($conn->resourceId);
		
		// Removendo usuário
		$this->ci->Online_Model->delete_by_id($online['_id']);
		
		// Atualizando usuários
		$this->usuarios_online($online['canal']['nome']);
		
    }
	
	/**
	 * Quando ocorrer alguma exceção
	 *
	 * @return void
	 */
	 
    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
	
	/**
	 * Quando recebido alguma mensagem do cliente
	 *
	 * @return void
	 */
	
    public function onMessage(ConnectionInterface $from, $msg) 
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
					$retorno = $this->entrar($from);
					break;
				// Enviar uma mensagem
				case 'mensagem':
					$retorno = $this->mensagem($from);
					break;
				default:
					$retorno = 'Nenhuma ação encontrada';
			}
		}
		
		// Enviando retorno
		if (!empty($retorno))
			$from->send(json_encode(array('acao' => 'erro', 'texto' => $retorno)));
    }
	
	/**
	 * Envia mensagem de um cliente para os outros conectados ao canal
	 *
	 * @param ConnectionInterface $ws
	 * @return void
	 */
	 
	private function mensagem($ws)
	{
		// Validações
		// Texto
		if (empty($this->dados->texto))
			return 'Nenhuma mensagem definida';
		
		// Canal
		if (empty($this->dados->canal))
			return 'Nenhum canal definido';

		// Selecionando usuário
		$online = $this->ci->Online_Model->get_by_recurso($ws->resourceId);
		
		// Se não existir
		if (empty($online))
			return 'Usuário não encontrado no canal';
		
		// Construindo mensagem
		$mensagem = json_encode(array(
									'acao' => 'mensagem', 
								  	'texto' => $this->dados->texto, 
								  	'usuario' => $online['usuario'],
								  	'data' => new MongoDate(time())
								 	)
								);
		
		// Enviando			 
		$this->broadcast($online['canal']['nome'], $mensagem);
	}
	
	/**
	 * Registra que o usuário está no canal
	 *
	 * @param ConnectionInterface $ws
	 * @return void
	 */
	 
    private function entrar($ws)
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
		
		// Definindo status
		$this->ci->Online_Model->atualizar($canal, $usuario, $ws->resourceId);

		// Atualizando usuários
		$this->usuarios_online($canal['nome']);
		
	}
	
	/**
	 * Envia para o cliente os usuários que estão online no canal
	 *
	 * @param string $canal nome do canal
	 * @return void
	 */
	 
	private function usuarios_online($canal)
	{
		// Selecionando usuários
		$onlines = $this->ci->Online_Model->get_by_canal($canal);
		
		// Atualizando usuários
		$mensagem = json_encode(array('acao' => 'usuario', 'usuario' => $onlines));
		$this->broadcast($canal, $mensagem);
	}
	
	/**
	 * Envia uma mensagem para todos os clientes conectados no canal determinado
	 *
	 * @param string $canal nome do canal
	 * @param string $mensagem
	 * @return void
	 */
	 
	private function broadcast($canal, $mensagem)
	{
		$recursos = array();
		
		// Selecionando somente recursos do canal
		$onlines = $this->ci->Online_Model->get_recurso_by_canal($canal);
		
		// Alocando recursos
		foreach ($onlines as $online)
			$recursos[] = $online['recurso'];
		
		// Passandos pelos clientes
		foreach ($this->usuarios as $usuario)
			// Se estiver no canal
			if (in_array($usuario->resourceId, $recursos))
				// Enviando mensagem
				$usuario->send($mensagem);
	}

}