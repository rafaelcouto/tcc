<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

class Servidor extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		
		// Carregando classe do aplicativo
		$this->load->library('Websocket');
	}
	
	public function listen()
	{
		// Defininido servidor
		$server = IoServer::factory(
		    new WsServer(
		        new Websocket()
		    )
		  , 9000
		);
		
		// Instanciando
		$server->run();
	}
}