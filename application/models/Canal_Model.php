<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Canal_Model extends CI_Model {
    
	private $colecao = 'canal';
	
	public function get_by_id($canal_id = null)
    {
        $item = $this->mongo_db->where(array('_id' => new MongoId($canal_id)))->get($this->colecao);
		return (empty($item)) ? null : $item[0];
    }
	
    public function get_by_nome($nome = null)
    {
        $item = $this->mongo_db->where(array('nome' => $nome))->get($this->colecao);
		return (empty($item)) ? null : $item[0];
    }

}

/* End of file Canal_Model.php */
/* Location: ./application/models/Canal_Model.php */