<?php

class Info extends CI_Controller {
	
	
	public function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		session_start();
		if(!isset($_SESSION['user'])){
			die("no permission");
		}
		phpinfo();
	}
}

?>