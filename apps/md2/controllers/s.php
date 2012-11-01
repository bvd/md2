<?php

require_once(APPPATH . "controllers/fcf/sessioncontroller.php");

/**
 * API'ish session access
 */

class S extends Sessioncontroller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model("Session");
	}
	
	function index()
	{
		log_message('info', 's-' . date("D M j G:i:s"));
		echo json_encode($this->Session->get());
	}
	// tested
	function set($key,$value){
		$this->load->model("Session");
		echo json_encode($this->Session->set($key,$value));
	}
	// tested
	function get($key_or_keys_array){
		$this->load->model("Session");
		echo json_encode($this->Session->get($key_or_keys_array));
	}
	// tested
	function getAvailable($key){
		$this->load->model("Session");
		echo json_encode($this->Session->getAvailable($key));
	}
	// tested
	function getWritableKeys(){
		$this->load->model("Session");
		echo json_encode($this->Session->getWritableKeys());
	}
	// tested
	function getReadableKeys(){
		$this->load->model("Session");
		echo json_encode($this->Session->getReadableKeys());
	}
	// tested
	function getEnumeratedKeys(){
		$this->load->model("Session");
		echo json_encode($this->Session->getEnumeratedKeys());
	}
}

?>