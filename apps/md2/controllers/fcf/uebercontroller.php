<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Uebercontroller extends CI_Controller {

	/**
	 * The Uebercontroller safeguards some simple conditions
	 *
	 * - can we write to a log file?
	 * - initialize javascript compressor.
	 * - can we (if configured) connect to a database? (TODO)
	 * 
	 * If you want PHP-Session related conditions, consider subclassing the Sessioncontroller
	 * which itself is a subclass of the Uebercontroller
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model("fcf/FileIO");
		if(!($this->FileIO->dirExistsOrCreateForWriteAccess($this->config->item("log_path")))){
			exit("problem writing to the log file");
		}
		$this->load->model("fcf/Carabinerwrapper");
	}
}