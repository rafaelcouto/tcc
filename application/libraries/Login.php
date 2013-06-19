<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Login 
{
    var $obj;
    
    function Login() {
        $this->obj =& get_instance(); // Criando instancia do CI
        $this->obj->load->library('session'); // Carrerando biblioteca de sessões
    }
    
    function getUser()
    {
        return $this->obj->session->userdata('usuario');
    }
    
    function get($key)
    {
         $user = $this->obj->session->userdata('usuario');
         return (string) $user[$key];
    }
	
    function logar($login, $senha, $redireciona = null) {
        
            // Tratando informações e procurando usuário
            $senha = addslashes($senha);
            $login = addslashes($login);
            $usuarios = $this->obj->mongo_db->where(array('login' => $login, 'senha' => $senha))->get('usuario');
            
            // Se encontrado o usuário
            if(count($usuarios))
            {
                $this->obj->session->set_userdata('usuario', $usuarios[0]);
                
                // Se informado redirecionamento
                if ($redireciona !== null)
                    redirect($redireciona);
                else
                    return true;
            }
            else
            {
                return false;
            }

    }

    function verificar($redireciona = null) {
            // Se as sessões estiverem setadas
            if($this->obj->session->userdata('usuario'))
                return true;
            else
            {
                if ($redireciona !== null)
                    redirect($redireciona);
                else
                    return false;
                    
                exit;
            }

    }

    function logout($redireciona = null) {
            // Destruindo sessão
            $this->obj->session->unset_userdata('usuario');

            if ($redireciona !== null)
                redirect($redireciona);
    }
}
?>