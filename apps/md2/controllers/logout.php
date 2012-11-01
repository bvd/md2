<?php



class Logout extends CI_Controller {
	
	
	public function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		
		session_start();
		
		$reto = new stdClass();
		$reto->status = 'success';
		$reto->vars = 'user session destroyed';
		
		if(isset($_SESSION['user'])){
			$_SESSION['user'] = null;
			unset($_SESSION['user']);
		}
		
		if(isset($_SESSION['username'])){
			$_SESSION['username'] = null;
			unset($_SESSION['username']);
		}
		
		if(isset($_SESSION['challenge'])){
			$_SESSION['challenge'] = null;
			unset($_SESSION['challenge']);
		}
		
		$userfile = $this->config->item('hidden_dir') . 'users';
		
		if(!is_file($userfile)){
			log_message('error', 'cannot find userfile on logout');
		}else{
			if(!($contentString = file_get_contents($userfile))){
				log_message('error', 'cannot read from userfile on logout');
			}else{
				if(!($userdata = json_decode($contentString))){
					log_message('error', 'cannot decode json from userfile on logout');
				}else{
					$userdata->session->user = null;
					$userdata->session->cookie = null;
					$userdata->session->time = null;
					file_put_contents($userfile, json_encode($userdata));
				}
			}
		}
		log_message('debug', '---------logout: success----------');
		echo json_encode($reto);
	}
}
?>