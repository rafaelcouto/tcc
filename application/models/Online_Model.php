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
	
	public function entrar($canal)
	{
		$this->load->model(array('Usuario_Model', 'Mensagem_Model'));
		
		$usuario = $this->Usuario_Model->get_by_login('servico_canal');
		
		$this->Mensagem_Model->salvar($canal, $usuario, $this->login->get('login') . ' entrou no canal');
	}
	
    public function atualizar($canal)
    {
    	// Definindo informações
		$item['canal'] = array('nome' => $canal['nome']);
		
		$item['usuario'] = array(
								'login' => $this->login->get('login'),
								'nome' => $this->login->get('nome'),
								'imagem' => $this->login->get('imagem')
							  );
							  
		$item['tempo'] = time();
		
		// Selecionando status
		$itens = $this->mongo_db
					  	->where(array(
					  		'canal.nome' => $canal['nome'],
					  		'usuario.login' => $this->login->get('login')
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

}

/* End of file Online_Model.php */
/* Location: ./application/models/Online_Model.php */