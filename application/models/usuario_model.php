<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usuario_Model extends CI_Model {
    
	private $colecao = 'usuario';
	
	/**
     * Usuário de serviço de canal
	 * @var Object
     */
	public $usuario_canal;
	
	/**
     * Usuário de sistema
	 * @var Object
     */
	public $usuario_sistema;
	
	/**
	 * Construtor
	 */
	public function __construct()
	{
        parent::__construct();
		
		// Selecionando usuários especiais
		$this->usuario_canal = $this->buscar_por_login('servico_canal');
		$this->usuario_sistema = $this->buscar_por_login('sistema');
	}
	
	/**
	 * Busca um usuário pelo login
	 *
	 * @param string $login
	 * @return array
	 */
    public function buscar_por_login($login)
    {
        $item = $this->mongo_db->where(array('login' => $login))->get($this->colecao);
		return (empty($item)) ? null : $item[0];
    }
	
	/**
	 * Busca um usuário pelo pelo login e senha
	 *
	 * @param string $login
	 * @param string $senha
	 * @return array
	 */
	public function buscar_por_autenticacao($login, $senha)
	{
		$item = $this->mongo_db->where(array('login' => $login, 'senha' => $senha))->get('usuario');
		return (empty($item)) ? null : $item[0];
	}

}

/* End of file Usuario_Model.php */
/* Location: ./application/models/Usuario_Model.php */