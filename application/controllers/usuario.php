<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usuario extends CI_Controller {

	public function index()
    {
        if (!$this->login->verificar())
        {
        	$this->js = array('lib/jquery.form.js');
            $this->layout->view('usuario/entrar');
        }
        else
        {
            $this->layout->view('usuario/index');
        }
    }
    
    public function entrar()
    { 
        if ($this->login->logar($this->input->post('usuario', true), md5($this->input->post('senha', true))))
        {
        	// Setando tecnologia
			$this->session->set_userdata('tecnologia', $this->input->post('tecnologia'));
			
			// Sucesso
        	echo false;
        }
        else
            echo 'Usuário ou senha inválido';
    }
    
    public function sair()
    {
        $this->login->logout('usuario');
    }

}

/* End of file usuario.php */
/* Location: ./application/controllers/usuario.php */