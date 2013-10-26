<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Util
{
    
    var $obj;
    
    function Util() {
        $this->obj =& get_instance(); // Criando instancia do CI
    }
    
    public function error($text = null)
    {
        return $this->response(0, $text);
    }
    
    public function success($text = null)
    {
        return $this->response(1, $text);
    }
    
    public function response($status, $text)
    {
        return json_encode(array(
                                'status' => $status, 
                                'text' => $text
                                ));
    }
    
	public function bytes_to_mb($bytes)
	{
		return round(($bytes / 1024) / 1024, 2);
	}
	
}

?>