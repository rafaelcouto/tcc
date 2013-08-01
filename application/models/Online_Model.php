<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Online_Model extends CI_Model {
    
	private $colecao = 'online';
	
	public function get_by_canal($nome)
	{
		return $this->mongo_db
						->select(array('usuario.nome', 'usuario.login', 'usuario.imagem'))
						->where(array('canal.nome' => $nome))
						->get($this->colecao);
	}
	
	public function get_recurso_by_canal($nome)
	{
		return $this->mongo_db
						->select(array('recurso'))
						->where(array('canal.nome' => $nome))
						->get($this->colecao);
	}
	
	public function entrar($canal)
	{
		$this->load->model(array('Usuario_Model', 'Mensagem_Model'));
		
		$usuario = $this->Usuario_Model->get_by_login('servico_canal');
		
		$this->Mensagem_Model->salvar($canal, $usuario, $this->login->get('login') . ' entrou no canal');
	}
	
    public function atualizar($canal, $usuario, $recurso = null)
    {
    	// Definindo informações
		$item['canal'] = array('nome' => $canal['nome']);
		
		$item['usuario'] = array(
								'login' => $usuario['login'],
								'nome' => $usuario['nome'],
								'imagem' => $usuario['imagem']
							  );
							  
		$item['tempo'] = time();
		
		// Se houver recurso (websocket)
		if (!empty($recurso))
			$item['recurso'] = $recurso;
		
		// Selecionando status
		$itens = $this->mongo_db
					  	->where(array(
					  		'canal.nome' => $canal['nome'],
					  		'usuario.login' => $usuario['login']
						))
					  	->get($this->colecao);
		
		// Se não existir
		if (empty($itens))
		{
			// Inserindo
			$id = $this->mongo_db->insert($this->colecao, $item);
			
			// Se inserido com sucesso
			if (!empty($id))
			{
				// Registrando mensagem de canal
				$this->entrar($canal);
				
				// Retornando ID do documento
				return $id;	
			}
			else {
				return null;
			}

		}
		else
			// Atualizando
			return $this->mongo_db
					  	->where(array('_id' => $itens[0]['_id']))
						->set($item)
						->update($this->colecao);
    }

	public function get_by_recurso($recurso = null)
	{
		$item = $this->mongo_db->where(array('recurso' => $recurso))->get('online');
		return (empty($item)) ? null : $item[0];
	}

}

/* End of file Online_Model.php */
/* Location: ./application/models/Online_Model.php */