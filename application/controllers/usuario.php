<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usuario extends CI_Controller {

	/**
	 * Página principal do usuário, caso não esteja logado, 
	 * carrega página para logar
	 *
	 * @return void
	 */
	public function index()
    {
        if (!$this->login->verificar())
        {
        	$this->js = array('lib/jquery.form.js');
            $this->layout->view('usuario/entrar');
        }
        else
        {
        	
			// Carregando modelos
			$this->load->model('Canal_Model');
			
			// Selecionando canais
			$data['canais'] = $this->Canal_Model->buscar();
			
			// View
            $this->layout->view('usuario/index', $data);
        }
    }
    
	/**
	 * Loga o usuário no sistema
	 *
	 * @return void
	 */
    public function entrar()
    { 
        if ($this->login->logar($this->input->post('usuario', true), md5($this->input->post('senha', true))))
        {
        	// Setando tecnologia
			$this->session->set_userdata('tecnologia', $this->input->post('tecnologia'));
			
			// Sucesso
        	echo $this->util->success();
        }
        else
            echo $this->util->error('Login ou senha inválido');
		
    }
    
	/**
	 * Desloga o usuário do sistema
	 *
	 * @return void
	 */
    public function sair()
    {
        $this->login->logout('usuario');
    }
	
	/**
	 * Perfil do usuário no aplicativo
	 *
	 * @return void
	 */
	 public function perfil($usuario)
	 {
	 	show_error('Perfil de usuário não implementado no aplicativo');
	 }

}

/* End of file usuario.php */
/* Location: ./application/controllers/usuario.php */