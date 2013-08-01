<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usuario_Model extends CI_Model {
    
	private $colecao = 'usuario';

    public function get_by_login($login = null)
    {
        $item = $this->mongo_db->where(array('login' => $login))->get($this->colecao);
		return (empty($item)) ? null : $item[0];
    }
	
	public function get_by_auth($login = null, $senha = null)
	{
		$item = $this->mongo_db->where(array('login' => $login, 'senha' => $senha))->get('usuario');
		return (empty($item)) ? null : $item[0];
	}

}

/* End of file Usuario_Model.php */
/* Location: ./application/models/Usuario_Model.php */