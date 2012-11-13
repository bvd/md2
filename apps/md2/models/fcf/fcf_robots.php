<?php

class Fcf_robots extends CI_Model {
    
	private $_config_links_file;
	private $_config_snapshots_dir;
	
	private $_ci;
	
	private $_links_file_write_handler;
	
	public function __construct()
	{
		parent::__construct();
		log_message("debug","fcf_robots constructor");
		$this->_ci =& get_instance();
		$this->_config_links_file = $this->config->item('robots_links_dir') . 'links';
		$this->_config_snapshots_dir = $this->config->item("robots_snapshots_dir");
	}
	function __destruct(){
		if(isset($this->_links_file_write_handler)){
			fclose($this->_links_file_write_handler);
		}
	}
	public function getLinks(){
		if(!is_file($this->config->item('robots_links_dir') . 'links')){
			$this->refreshHtmlContentCache();
		}
		return file_get_contents($this->config->item('robots_links_dir') . 'links');
	}
	public function getContent(){
		if(isset($_GET["_escaped_fragment_"])){
			$uriStr = $_GET["_escaped_fragment_"];
		}else{
			$urlParse = parse_url("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			$this->load->helper('url');
			$uriStr = substr($urlParse['path'], strlen(substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/') + 1)));
		}	
		if(strlen($uriStr) == 0){
			if(is_file($this->_config_snapshots_dir . $this->config->item("default_page"))){
				return file_get_contents($this->_config_snapshots_dir . $this->config->item("default_page"));
			}
			return "";
		}
		$strlen = strlen($uriStr);
		$uriStr = (strrpos($uriStr,"/") == $strlen -1) ? substr($uriStr,0, $strlen -1) : $uriStr;
		if(!file_exists($this->_config_snapshots_dir . $uriStr)){
			show_404($this->uri->segment($this->uri->total_segments()),FALSE);
			exit();
		}
		$strlen = strlen($this->_config_snapshots_dir);
		$snapshotsDirName = (strrpos($this->_config_snapshots_dir,"/") == $strlen -1) ? substr($this->_config_snapshots_dir,0, $strlen -1) : $this->_config_snapshots_dir;
		if(!(dirname($this->_config_snapshots_dir . $uriStr) == $snapshotsDirName)){
			show_404($this->uri->segment($this->uri->total_segments()),FALSE);
			exit();
		}
		return file_get_contents($this->_config_snapshots_dir . $uriStr);
	}
	private function _init_empty_links_file(){
		$this->_links_file_write_handler = fopen($this->_config_links_file,"w");
		if(false === $this->_links_file_write_handler){
			log_message("error", "Fcf_robots->refreshHtmlContentCache() : could not open file at " . $this->_config_links_file);
			die();
		}
	}
	private function _append_to_links_file($anchor){
		if(!(isset($this->_links_file_write_handler))){
			$this->_init_empty_links_file();
		}
		if(false === fwrite($this->_links_file_write_handler,$anchor . "\n")){
			log_message("error","Fcf_robots->refreshHtmlContentCache() : could not write to file at " . $this->_config_links_file);
			return;
		}
	}
	private function _write_content_to_file($file_name, $content){
		$contentFile = $this->_config_snapshots_dir . $file_name;
		if(false === file_put_contents($contentFile, $content)){
			log_message("error","Fcf_robots->refreshHtmlContentCache() : could not write to file at " . $contentFile);
			return;
		}
	}
	private function _create_anchor($link, $title = ""){
		$absLink = $this->config->item("base_url") . "#!/" . $link . "/";
		$anchor = "<a href='" . $absLink . "'>" . (($title != "") ? $title : $link) . "</a>";
		return $anchor;
	}
	public function refreshHtmlContentCache(){
		log_message("debug","function fcf_robots->refreshHtmlContentCache()");
		$this->_ci->load->model("fcf/Fcf_xml_db");
		log_message("debug","function fcf_robots->refreshHtmlContentCache() - loaded Fcf_xml_db");
		$contentForLinks = $this->_ci->Fcf_xml_db->getContentForLinks();
		log_message("debug","function fcf_robots->refreshHtmlContentCache() - received links");
		foreach($contentForLinks as $link => $content){
			$this->_append_to_links_file($this->_create_anchor($link));
			$this->_write_content_to_file($link,$content);
		}
	}
}
?>