<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Bash
{
    private $ci;
    private $args = array();
    private $cliente;
	private $clientes;
	
	public function iniciar(&$ci, &$clientes)
	{
		$this->ci = $ci;
		$this->clientes = $clientes;
	}
    
    function interpretar($cliente, $texto)
    {
        // Definindo informações
        $this->texto = $texto;
        $this->cliente = $cliente;
        
        // 'Quebrando' argumentos e comando
        $this->args = explode(' ', substr($texto, 1, strlen($texto)));
        $comando = mb_strtolower($this->args[0], 'UTF-8');
        
        // Selecionando comando
        switch ($comando)
        {
			case 'kick':
				
				$usuario = (isset($this->args[1])) ? $this->args[1] : null;
                return $this->_kick($usuario); break;

            default:
                return 'Comando não encontrado';
        }
        
    }
	
	private function _kick($usuario)
    {
		$cliente_alvo = null;
		
		// Passando pelos clientes
		foreach ($this->clientes as $cliente)
		{
			// Se encontrado usuário no canal
			if (($cliente->usuario['login'] === $usuario) and ($cliente->canal['nome'] === $this->cliente->canal['nome']))
			{
				$cliente_alvo = $cliente;
				break;
			}
		}
		
		// Se nenhum usuário encontrado
		if (empty($cliente_alvo))
			return 'Usuário não encontrado no canal';

		// Se for ele mesmo
		if ($this->cliente->usuario['login'] == $cliente_alvo->usuario['login'])
			return 'Você não pode kickar si mesmo';
		
		// Enviando mensagem para alvo
		$mensagem = $this->ci->mensagem->construir($this->ci->Usuario_Model->usuario_sistema, 'Você foi kickado do canal');
		$cliente_alvo->recurso->send($mensagem);
		
		// Desconectando alvo
		$cliente_alvo->recurso->close();
		
		// Enviando mensagem para usuários
		$texto = sprintf('O usuário @%s foi kickado do canal por @%s', $cliente_alvo->usuario['login'], $this->cliente->usuario['login']);
		$mensagem = $this->ci->mensagem->construir($this->ci->Usuario_Model->usuario_canal, $texto);
		$this->ci->mensagem->broadcast($cliente_alvo->canal['nome'], $mensagem);

		// Log
		echo sprintf('@%s foi kickado do canal #%s por @%s',  $cliente_alvo->usuario['login'], $cliente_alvo->canal['nome'], $this->cliente->usuario['login']) . PHP_EOL;
		
		// Sucesso
		return null;
    }
    
    
}
?>