<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Online_Model extends CI_Model {
    
	private $colecao = 'online';
	
	/**
	 * Busca os usuários online com o tempo menor que o informado
	 *
	 * @param int $tempo
	 * @return array
	 */
	public function buscar_por_tempo($tempo)
	{
		return $this->mongo_db->where_lt('tempo', $tempo)->get($this->colecao);
	}
	
	/**
	 * Busca os usuários online de um canal
	 *
	 * @param string $nome
	 * @return array
	 */
	public function buscar_por_canal($nome)
	{
		return $this->mongo_db
						->select(array('usuario.nome', 'usuario.login', 'usuario.imagem'))
						->where(array('canal.nome' => $nome))
						->get($this->colecao);
	}
	
	/**
	 * Registra a mensagem de entrada do usuário no canal
	 *
	 * @param array $canal - informações do canal
	 * @param array $usuario - informações do usuário
	 * @return void
	 */
	public function entrar($canal, $usuario)
    {
        $this->load->model(array('Usuario_Model', 'Mensagem_Model'));
        $this->Mensagem_Model->salvar($canal, $this->Usuario_Model->usuario_canal, '@' . $usuario['login'] . ' entrou no canal');
    }
	
	/**
	 * Registra a mensagem de saída do usuário no canal
	 *
	 * @param array $canal - informações do canal
	 * @param array $usuario - informações do usuário
	 * @return void
	 */
	public function sair($canal, $usuario)
	{
		$this->load->model(array('Usuario_Model', 'Mensagem_Model'));
        $this->Mensagem_Model->salvar($canal, $this->Usuario_Model->usuario_canal, '@' . $usuario['login'] . ' saiu no canal');
	}
	
	/**
	 * Atualiza o tempo do usuário no canal
	 *
	 * @param array $canal - informações do canal
	 * @param array $usuario - informações do usuário
	 * @return mixed
	 */
    public function atualizar($canal, $usuario)
    {
    	// Definindo informações
		$item['canal'] = $canal;
		$item['usuario'] = $usuario;		  
		$item['tempo'] = time();
		
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
                $this->entrar($canal, $usuario);
									
				// Retornando ID do documento
				return $id;	
			}
			else 
			{
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

	/**
	 * Remove o usuário online do canal pelo ID
	 *
	 * @param int $id
	 * @return bool
	 */
	public function remover_por_id($id)
	{
		return $this->mongo_db->where(array('_id' => $id))->delete($this->colecao);
	}
	
}

/* End of file Online_Model.php */
/* Location: ./application/models/Online_Model.php */