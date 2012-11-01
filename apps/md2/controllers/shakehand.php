<?php



class Shakehand extends CI_Controller {
	
	
	public function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		log_message('debug', '-------------------');
		log_message('debug', '- - SHAKE-HAND- - -');
		log_message('debug', '-------------------');
		log_message('debug', 'uri: ' . $this->uri->uri_string());
		log_message('debug', date("D M j G:i:s"));
		
		session_start();
		
		$reto = new stdClass();
		$reto->status = new stdClass();
		$reto->vars = new stdClass();
		
		if(!isset($_SESSION['challenge'])){
			echo json_encode((object)array('login' => 'failed, no session challenge'));
			return;
		}
		$chall = $_SESSION['challenge'];
		$_SESSION['challenge'] = "";
		unset($_SESSION['challenge']);
		
		if(!isset($_POST['username'])){
			$reto->status = "failed";
			$reto->vars = "missing param username";
			echo json_encode($reto);
			return;
		}
		if(!isset($_POST['handshake'])){
			$reto->status = "failed";
			$reto->vars = "missing param handshake";
			echo json_encode($reto);
			return;
		}
		
		// get the password of this user from the userdata
		$userfile = $this->config->item('db_dir') . 'users';
		if(!is_file($userfile)){
			$reto->status = "failed";
			$reto->vars = "missing userfile";
			echo json_encode($reto);
			return;
		}
		if(!($contentString = file_get_contents($userfile))){
			$reto->status = "failed";
			$reto->vars = "unreadable userfile";
			echo json_encode($reto);
			return;
		}
		if(!($userdata = json_decode($contentString))){
			$reto->status = "failed";
			$reto->vars = "corrupted userfile";
			echo json_encode($reto);
			return;
		}
		
		// does user exist?
		if(!isset($userdata->users->$_POST['username'])){
			$reto->status = "failed";
			$reto->vars = "user does not exist";
			echo json_encode($reto);
			return;
		}
		
		// is password set?
		if(!isset($userdata->users->$_POST['username']->password)){
			$reto->status = "failed";
			$reto->vars = "user has no password";
			echo json_encode($reto);
			return;
		}
		
		// test handshake
		$tested = hash('sha256', $chall . $userdata->users->$_POST['username']->password);
		if($tested == $_POST['handshake']){
			
			// is another session already active?
			if($userdata->session->user != null){
				
				//is the currently registered session over time?
				if( time() - $userdata->session->time > $this->config->item('registered_session_overtime_seconds')){
					$userdata->session->user = $_POST['username'];
					$userdata->session->cookie = session_id();
					$userdata->session->time = time();
					$_SESSION['username'] = $_POST['username'];
					file_put_contents($userfile, json_encode($userdata));
					$reto->status = "success";
					$reto->vars = $_SESSION['username'];
					echo json_encode($reto);
					return;
				}
				// is the registered session the same as this session?
				if( $userdata->session->cookie == session_id() ){
					$userdata->session->user = $_POST['username'];
					$userdata->session->cookie = session_id();
					$userdata->session->time = time();
					$_SESSION['username'] = $_POST['username'];
					file_put_contents($userfile, json_encode($userdata));
					$reto->status = "success";
					$reto->vars = $_SESSION['username'];
					echo json_encode($reto);
					return;
				}
				
				$reto->status = "failed";
				$reto->vars = "otherUser";
				echo json_encode($reto);
				return;
				
			}else{
				$userdata->session->user = $_POST['username'];
				$userdata->session->cookie = session_id();
				$_SESSION['username'] = $_POST['username'];
				file_put_contents($userfile, json_encode($userdata));
				$reto->status = "success";
				$reto->vars = $_SESSION['username'];
				echo json_encode($reto);
				return;
			}	
		}else{
			$reto->status = "failed";
			$reto->vars = "wrong password";
			echo json_encode($reto);
			return;
		}
		
		
		
		echo json_encode((object)array('submit' => $_POST['handshake'], 'tested' => $tested));
	}
}
?>
