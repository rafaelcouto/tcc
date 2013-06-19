<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Canal extends CI_Controller {
	
	public function __construct() 
    {
        parent::__construct();
        
        // Carregando model
        $this->load->model('Canal_Model');      
    }
	
	public function index($canal = null)
    {
        if (!$this->login->verificar('usuario'))
			exit;
		
		$tecnologia = $this->session->userdata('tecnologia');
		
		// Selecionando canal
		$data['canal'] = $this->Canal_Model->get_by_nome($canal);
		
		// Se não existir
		if (empty($data['canal']))
			show_error('O canal não está registrado');
		
		// Assets
		$this->css = array('canal.css');
		$this->js = array('canal.js', "{$tecnologia}.js", 'lib/sprintf.js', 'lib/jquery.dateFormat.js');
		
		// View
		$this->layout->view("canal/index", $data);
		
    }
	
	public function sp_atualizar($canal)
	{
		if (!$this->login->verificar())
			exit;
		
		// Iniciando timer
		$this->load->library('timer');
        $this->timer->reset();
        $this->timer->start();	
		
		// Selecionando canal
		$canal = $this->Canal_Model->get_by_nome($canal);
		
		// Se não existir
		if (empty($canal))
			exit;
		
		// Carregando
		$this->load->model(array('Online_Model', 'Mensagem_Model'));
		
		// Atualizando status
		$this->Online_Model->atualizar($canal);
		
		$data = array();
		$data['mensagem'] = $this->Mensagem_Model->get_by_canal($canal['nome'], $this->input->post('maior_que'));
		$data['usuario'] = $this->Online_Model->get_by_canal($canal['nome']);

		// Finalizando timer
        $this->timer->stop();
            
        // Tempo de execução
        $data['latencia'] = $this->timer->get(Timer::MILLISECONDS);
		$data['memoria'] = round((memory_get_usage() / 1024) / 1024, 2);
		
		// Retornando dados
		echo json_encode($data);
		
	}
	
	public function lp_atualizar($canal)
	{
		if (!$this->login->verificar()) exit;
		
		// Desativando tempo limite do script
		set_time_limit(0);
		
		// Selecionando canal
		$canal = $this->Canal_Model->get_by_nome($canal);
		
		// Se não existir
		if (empty($canal)) exit;
		
		// Carregando
		$this->load->model(array('Online_Model', 'Mensagem_Model'));
		$this->load->library('timer');
		
		// Usuários online para comparação
		$usuario_online_atual = explode(',', $this->input->post('usuario_online'));

		while (true)
		{
			// Iniciando vetores
			$data = array();
			$usuario_online = array();
			
			// Iniciando timer
			$this->timer->reset();
        	$this->timer->start();

			// Atualizando status
			$this->Online_Model->atualizar($canal);
			
			// Buscando mensagens e usuários
			$data['mensagem'] = $this->Mensagem_Model->get_by_canal($canal['nome'], $this->input->post('maior_que'));
			$data['usuario'] = $this->Online_Model->get_by_canal($canal['nome']);
			
			// Alocando usuários online para comparação
			foreach ($data['usuario'] as $usuario)
				$usuario_online[] = $usuario['usuario']['login'];

			// Se houver mensagens ou alguma modificação nos usuários
			if ((!empty($data['mensagem'])) or (sizeof(array_diff($usuario_online, $usuario_online_atual)) > 0))
			{
				// Finalizando timer
        		$this->timer->stop();

				// Tempo de execução e memória
        		$data['latencia'] = $this->timer->get(Timer::MILLISECONDS);
				$data['memoria'] = round((memory_get_usage() / 1024) / 1024, 2);
				
				// Retornando dados
				echo json_encode($data);
				break;
			}
			else 
			{
				// Finalizando timer
        		$this->timer->stop();
				
				// Aguardando próxima busca
				sleep(3);
				continue;
			}

		}

	}
	
	public function sse_atualizar($canal)
	{
		if (!$this->login->verificar()) exit;
		
		// Cabeçalhos
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
			
		// Desativando tempo limite do script
		set_time_limit(0);
		
		// Selecionando canal
		$canal = $this->Canal_Model->get_by_nome($canal);
		
		// Se não existir
		if (empty($canal)) exit;
		
		// Carregando
		$this->load->model(array('Online_Model', 'Mensagem_Model'));
		$this->load->library('timer');
		
		$ultima_mensagem_id = null;
		$i = 0;
		$c = 0;
		
		do
		{
			// Iniciando vetores
			$data = array();
			
			// Iniciando timer
			$this->timer->reset();
        	$this->timer->start();

			// Atualizando status
			$this->Online_Model->atualizar($canal);
			
			// Buscando mensagens e usuários
			$data['usuario'] = $this->Online_Model->get_by_canal($canal['nome']);
			$data['mensagem'] = $this->Mensagem_Model->get_by_canal($canal['nome'], $ultima_mensagem_id->{'$id'});
			$c = count($data['mensagem']);
			
			if ($c > 0)
				$ultima_mensagem_id = $data['mensagem'][$c - 1]['_id'];
			
			// Finalizando timer
    		$this->timer->stop();
			
			// Tempo de execução e memória
			$data['latencia'] = $this->timer->get(Timer::MILLISECONDS);
			$data['memoria'] = round((memory_get_usage() / 1024) / 1024, 2);
			
			// Resposta
            echo "id: {$i}" . PHP_EOL; 
            echo 'data: ' . json_encode($data) . PHP_EOL;
			
			$i++;
			
			// Finalizando dados
            echo PHP_EOL;

            // Enviando saída
            ob_flush();
            flush();
            
            // Aguardando próximo loop
            sleep(3);
			
		} while (true);

	}
	
}

/* End of file canal.php */
/* Location: ./application/controllers/canal.php */