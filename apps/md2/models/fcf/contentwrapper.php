<?php
class Contentwrapper extends CI_Model {
	
	private $_db_dir_config;
	private $_base_url_config;
	
	private $_meta_file;
	private $_init_data_file;
	private $_ftime;
	
	private $_meta;
	
	private $_most_recent_data_file;
	
	private $_xml;
	private $_links;
	
	private $_iter_current_link;
	private $_content_for_url;
	
	function __construct()
	{
		parent::__construct();
		
		$this->_db_dir_config = $this->config->item('db_dir');
		$this->_base_url_config = $this->config->item('base_url');
		
		$this->_meta_file_config = $this->_db_dir_config . 'frf';
		$this->_init_data_file = $this->_db_dir_config . 'content';
		
		$this->_ftime = time();
	}
	private function _init_meta(){
		if(!file_exists($this->_meta_file_config)){
			$this->_meta = array();
			$this->_meta[] = $this->_ftime;
			file_put_contents($this->_init_data_file . "_" . $this->_ftime, file_get_contents($this->_init_data_file));
			file_put_contents($this->_meta_file_config,serialize($this->_meta));
		}else{
			$this->_meta = unserialize(file_get_contents($this->_meta_file_config));
		}
	}
	private function _init_most_recent_data_file(){
		if(!(isset($this->_meta))){
			$this->_init_meta();
		}
		$this->_most_recent_data_file = $this->_db_dir_config . 'content_' . $this->_meta[sizeof($this->_meta)-1];
		log_message('debug', 'contentwrapper->_most_recent_data_file set to ' . $this->_most_recent_data_file);
	}
	private function _init_site_xml(){
		$this->_init_most_recent_data_file();
		if(false === ($xml = simplexml_load_file($this->_most_recent_data_file))){
			log_message('error','contentwrapper->_init_site_xml : xml load error for file ' . $this->_most_recent_data_file);
		}
		$this->_xml = $xml;
	}
	
	public function getContentForLinks(){
		$this->_init_site_xml();
		$this->_iter_current_link = "";
		$this->_content_for_url = array();
		$this->_retrieve_content($this->_xml->site);
		return $this->_content_for_url;
	}
	
	private function _retrieve_content($elem){
		foreach($elem->children() as $child) {
			if(!($child->getName() == 'children')){
				$this->_iter_current_link = $this->_iter_current_link . ((strlen($this->_iter_current_link)>0)?'-':'') . $child->getName();
				if($child->attributes()->view){
					$this->_content_for_url[$this->_iter_current_link] = "";
					if($child->fields){
						foreach($child->fields->children() as $fld){
							$this->_content_for_url[$this->_iter_current_link]  .= '<p>' . $fld . '</p>';
						}
					}
				}
				$this->_retrieve_content($child);
				$this->_iter_current_link = substr($this->_iter_current_link, 0, ((strlen($this->_iter_current_link)==strlen($child->getName()))?0:strlen($this->_iter_current_link) - strlen( '-' . $child->getName() )));
			}else{
				$this->_retrieve_content($child);
			}
		}
	}
}
?>