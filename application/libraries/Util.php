<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Util
{
    
	/**
	 * Constroi mensagem de erro
	 *
	 * @param string $text
	 * @return JSON
	 */
    public function error($text = null)
    {
        return $this->response(0, $text);
    }
    
	/**
	 * Constroi mensagem de sucesso
	 *
	 * @param string $text
	 * @return JSON
	 */
    public function success($text = null)
    {
        return $this->response(1, $text);
    }
    
	/**
	 * Constroi padrão de mensagem
	 *
	 * @param int $status
	 * @param string $text
	 * @return JSON
	 */
    private function response($status, $text)
    {
        return json_encode(array(
                                'status' => $status, 
                                'text' => $text
                                ));
    }
	
}

?>