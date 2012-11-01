<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Up extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		// todo: wat gebeurt er met foutmeldingen bij geen goed formaat
		// bij te groot bestand
		// etc
		
		// todo: wat doen we met voorgebakken schaling, cropping e.d.
		$this->load->model("fcf/Fcf_upload");
		$this->Fcf_upload->upload();
	}
}