<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Canal_Model extends CI_Model {
    
	private $colecao = 'canal';
	
	/**
	 * Busca um canal através de seu nome
	 *
	 * @param string $nome
	 * @return array
	 */
    public function buscar_por_nome($nome)
    {
        $item = $this->mongo_db->where(array('nome' => $nome))->get($this->colecao);
		return (empty($item)) ? null : $item[0];
    }
	
	/**
	 * Busca todos os canais registrados
	 *
	 * @return array
	 */
	public function buscar()
	{
		return $this->mongo_db->get($this->colecao);
	}

}

/* End of file Canal_Model.php */
/* Location: ./application/models/Canal_Model.php */