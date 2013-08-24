<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mensagem_Model extends CI_Model {
    
	private $colecao = 'mensagem';
	
	/**
	 * Busca mensagens por canal
	 *
	 * @param string $nome - nome do canal
	 * @param int $maior_que - ID da última mensagem recebida
	 * @return array
	 */
    public function buscar_por_canal($nome, $maior_que = null)
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
	
	/**
	 * Salva a mensagem de um usuário para um canal
	 *
	 * @param array $canal - informações do canal
	 * @param array $usuario - informações do usuário
	 * @param string $texto
	 * @return MongoId
	 */
	public function salvar($canal, $usuario, $texto)
	{
		$this->load->library('Canal_Util');

		// Dados
		$item = array(
			'canal' => array('nome' => $canal['nome']),
			'usuario' => array(
								'login' => $usuario['login'],
								'nome' => $usuario['nome'],
								'imagem' => $usuario['imagem']
							  ),
			'data' => new MongoDate(strtotime(date('Y-m-d H:i:s'))),
			'texto' => $this->canal_util->tratar_texto($texto),
			'tempo' => time()
		);
		
		return $this->mongo_db->insert($this->colecao, $item);
	}
	
	/**
	 * Remove as mensagens com o tempo menor que o informado
	 *
	 * @param int $tempo
	 * @return bool
	 */
	public function remover_por_tempo($tempo)
	{
		return $this->mongo_db->where_lt('tempo', $tempo)->delete($this->colecao);
	}

}

/* End of file Mensagem_Model.php */
/* Location: ./application/models/Mensagem_Model.php */