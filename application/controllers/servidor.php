<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

class Servidor extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Instancia um servidor de WS
	 *
	 * @return void
	 */
	public function websocket($porta = 9000)
	{
		// Carregando classe do aplicativo
		$this->load->library('Websocket');
		
		// Defininido servidor
		$server = IoServer::factory(
		    new WsServer(
		        new Websocket()
		    )
		  , $porta
		);
		
		// Instanciando
		$server->run();
	}
	
	/**
	 * Instancia um servidor de SP, LP e SSE, objetivos:
	 * - Limpar as mensagens expiradas
	 * - Removenddo os usuários offline
	 *
	 * @return void
	 */
	public function generico()
	{
		// Desativando tempo limite do script
		set_time_limit(0);
		
		// Carregando modelos
		$this->load->model(array('Mensagem_Model', 'Online_Model'));
		
		do 
		{
			// Removendo
			// Usuários
			$onlines = $this->Online_Model->buscar_por_tempo(time() - 4);
			
			foreach ($onlines as $online)
			{
				// Enviando mensagem
				$this->Online_Model->sair($online['canal'], $online['usuario']);
				
				// Removendo
				$this->Online_Model->remover_por_id($online['_id']);
			}

	        // Mensagens
	        $this->Mensagem_Model->remover_por_tempo(time() - (1 * 60 * 60));
			
			// Log
			echo '[' . date('d/m/Y H:i:s') . '] Trabalhando' . PHP_EOL; 
			
			// Aguardando próximo loop
            sleep(5);
			
		} while (true);
	}
}