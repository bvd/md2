<?php



class Getchall extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		log_message('debug', '-------------------');
		log_message('debug', '- - -GETCHALL - - -');
		log_message('debug', '-------------------');
		log_message('debug', 'uri: ' . $this->uri->uri_string());
		log_message('debug', date("D M j G:i:s"));
		session_start();
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";
		$chall = "";
		for($i=0;$i<32;$i++)
			$chall .= substr($chars, rand() % 33, 1);
		$_SESSION['challenge'] = $chall;
		echo $chall;
	}
}
?>