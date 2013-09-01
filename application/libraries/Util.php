<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Util
{
    
    var $obj;
    
    function Util() {
        $this->obj =& get_instance(); // Criando instancia do CI
    }

    public function exception(Exception $e, $origem)
    {
        // Log
        log_message('error', $origem . ' :: ' . $e->getMessage());
        
        // Mensagem
        return $this->error('Não foi possível realizar esta operação no momento. 
                             Tente novamente em alguns instantes. 
                             Caso o erro persista, entre em contato com o administrador', 'Erro', 1);
    }
    
    public function error($text = null)
    {
        return $this->response(0, $text);
    }
    
    public function success($text = null)
    {
        return $this->response(1, $text);
    }
	
	public function info($text = null)
    {
        return $this->response(2, $text);
    }
    
    public function response($status, $text)
    {
        return json_encode(array(
                                'status' => $status, 
                                'text' => $text
                                ));
    }
    
}

?>