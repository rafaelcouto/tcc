<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cliente 
{
	/**
     * Objeto de conexão com websocket
	 * @var ConnectionInterface
     */
	public $recurso;
	
	/**
     * Informações do usuário
	 * @var array
     */
	public $usuario;
	
	/**
     * Informações do canal
	 * @var array
     */
	public $canal;
}