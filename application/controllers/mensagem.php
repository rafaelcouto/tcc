<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mensagem extends CI_Controller {
	
	public function __construct() 
    {
        parent::__construct();
        
        // Carregando model
        $this->load->model('Mensagem_Model');      
    }
	
	/**
	 * Método responsável por salvar mensagem utilizando SP, LP e SSE.
	 *
	 * @param string $canal - nome do canal
	 * @return void
	 */
	public function salvar($canal)
	{
		if (!$this->login->verificar()) exit;
		
		// Carregando model
        $this->load->model(array('Canal_Model', 'Usuario_Model'));      
		
		// Selecionando canal
		$canal = $this->Canal_Model->buscar_por_nome($canal);
		
		// Se não existir
		if (empty($canal)) exit;
		
		// Salvando
		$this->Mensagem_Model->salvar($canal, $this->login->usuario(), $this->input->post('mensagem', true));
		
	}
	
}

/* End of file mensagem.php */
/* Location: ./application/controllers/mensagem.php */