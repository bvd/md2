<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . "controllers/fcf/uebercontroller.php");

class Sessioncontroller extends Uebercontroller {

	/**
	 * The Sessioncontroller initializes the database connection(s) as configured
	 * (even if you configured NO database).
	 *
	 * It also initializes the PHP-SESSION and the Fcf_session model.
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model("Session");
	}
}