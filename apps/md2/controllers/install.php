<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Install extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		$this->dirs();
	}
	public function dirs(){
		$dirs = $this->config->item("fcf_install_dirs");
		foreach($dirs as $dir){
			if(!(is_dir($dir))){
				if(false === (mkdir($dir,0770,true))){
					die("could not create " . $dir);
				}
			}
		}
	}
}