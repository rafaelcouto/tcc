<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mensagem extends CI_Controller {
	
	public function __construct() 
    {
        parent::__construct();
        
        // Carregando model
        $this->load->model(array('Canal_Model', 'Mensagem_Model', 'Usuario_Model'));      
    }

	public function salvar($canal)
	{
		if (!$this->login->verificar())
			exit;
		
		// Selecionando canal
		$canal = $this->Canal_Model->get_by_nome($canal);
		
		// Se não existir
		if (empty($canal))
			exit;
		
		// Selecionando usuário
		$usuario = $this->Usuario_Model->get_by_login($this->login->get('login'));
		
		// Salvando
		$this->Mensagem_Model->salvar($canal, $usuario, $this->input->post('mensagem', true));
		
	}
	
}

/* End of file mensagem.php */
/* Location: ./application/controllers/mensagem.php */