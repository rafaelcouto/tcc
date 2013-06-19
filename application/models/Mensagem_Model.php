<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mensagem_Model extends CI_Model {
    
	private $colecao = 'mensagem';
	
    public function get_by_canal($nome, $maior_que = null)
    {
        $this->mongo_db
        		->select(array('data', 'texto', 'usuario.nome', 'usuario.login', 'usuario.imagem'))
				->where(array('canal.nome' => $nome));
		
		// Se houver filtro		
		if (!empty($maior_que))
			// Filtra a partir da mensagem informada
			$this->mongo_db->where_gt('_id', new MongoId($maior_que));
		
        return $this->mongo_db
        				->order_by(array('_id' => 'ASC'))
        				->get($this->colecao);
    }
	
	public function salvar($canal, $usuario, $mensagem)
	{
		// Dados
		$item = array(
			'canal' => array('nome' => $canal['nome']),
			'usuario' => array(
								'login' => $usuario['login'],
								'nome' => $usuario['nome'],
								'imagem' => $usuario['imagem']
							  ),
			'data' => new MongoDate(strtotime(date('Y-m-d H:i:s'))),
			'texto' => $mensagem
		);
		
		return $this->mongo_db->insert($this->colecao, $item);
	}

}

/* End of file Mensagem_Model.php */
/* Location: ./application/models/Mensagem_Model.php */