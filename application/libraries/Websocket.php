<?php 
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Websocket implements MessageComponentInterface  {
	
	private $ci;
	protected $usuarios;
	protected $canais;	
	private $dados;
	
    public function __construct() 
    {
    	$this->ci =& get_instance();
        $this->usuarios = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->usuarios->attach($conn);
    }
	
	public function onClose(ConnectionInterface $conn) 
	{
        $this->usuarios->detach($conn);
        
		// Carregando modelos
		$this->ci->load->model(array('Online_Model'));
		
		// Selecionando usuário
		$online = $this->ci->Online_Model->get_by_recurso($conn->resourceId);
		
		// Removendo
		$this->ci->mongo_db->where(array('_id' => $online['_id']))->delete('online');
		
		// Atualizando usuários
		$this->atualizar_usuarios($online['canal']['nome']);
		
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
	
    public function onMessage(ConnectionInterface $from, $msg) 
    {
    	$retorno = null;
		
    	// Alocando parâmetros
		$this->dados = json_decode($msg);
		
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
			$from->send($retorno);
    }
	
	private function mensagem($from)
	{
		// Validações
		// Texto
		if (empty($this->dados->texto))
			return 'Nenhuma mensagem definida';

		// Carregando modelos
		$this->ci->load->model(array('Online_Model'));
		
		// Selecionando usuário
		$online = $this->ci->Online_Model->get_by_recurso($from->resourceId);
		
		// Se não existir
		if (empty($online))
			return 'Usuário não encontrado';
		
		$mensagem = json_encode(array('acao' => 'mensagem', 
									 'texto' => $this->dados->texto, 
									 'usuario' => $online['usuario'],
									 'data' => new MongoDate(time())
									 ));
									 
		$this->broadcast($online['canal']['nome'], $mensagem);
	}
	
    private function entrar($from)
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
		
		$this->ci->load->model(array('Usuario_Model', 'Online_Model', 'Canal_Model'));
		
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
		$this->ci->Online_Model->atualizar($canal, $usuario, $from->resourceId);
		
		// Atualizando usuários
		$this->atualizar_usuarios($canal['nome']);
	}

	private function atualizar_usuarios($canal)
	{
		// Selecionando usuários
		$onlines = $this->ci->Online_Model->get_by_canal($canal);
		
		// Atualizando usuários
		$mensagem = json_encode(array('acao' => 'usuario', 'usuario' => $onlines));
		$this->broadcast($canal, $mensagem);
	}

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