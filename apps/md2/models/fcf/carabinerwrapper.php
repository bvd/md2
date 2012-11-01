<?php
class Carabinerwrapper extends CI_Model {
	function __construct()
	{
		$this->load->library('carabiner');
		$this->carabiner->config($this->config->item("carabiner"));
		parent::__construct();
	}
	public function jsTagsForModule($module_name){
		$this->_load_module($module_name);
		$tagsString = $this->carabiner->display_string($module_name);
		return $tagsString;
	}
	private function _load_module($module_name){
		$jsModulesConfigured = $this->config->item("js_modules");
		if(!array_key_exists($module_name,$jsModulesConfigured)){
			return false;
		}
		$this->carabiner->group($module_name, array('js'=>$jsModulesConfigured[$module_name]) );
	}
	public function scriptForModule($module_name){
		$srcUrls = $this->srcUrlsForModule($module_name);
		if(false === $srcUrls){
			return "unknown: " . $module_name;
		}
		$out = "";
		foreach($srcUrls as $url){
			if(false !== ($path = $this->pathForUrl($url))){
				$out .= file_get_contents($path);
			}
		}
		return $out;
	}
	public function srcUrlsForModule($module_name){
		$out = array();
		if(false === ($this->_load_module($module_name))){
			return false;
		}
		$tagsString = $this->carabiner->display_string($module_name);
		$scriptsXml = simplexml_load_string("<scripts>" . $tagsString . "</scripts>");
		foreach( $scriptsXml->script as $script){
			if( $script->getName() != "script"){
				log_message("error","carabinerwrapper: strange output from scripts string element name: " . $script->getName());
				continue;
			}
			$out[] = $script->attributes()->src;
		}
		return $out;
	}
	public function pathForUrl($url, $type="js"){
		if($type = "js"){
			if($this->carabiner->dev){
				if(!(substr($url,0,strlen($this->carabiner->script_uri)) == $this->carabiner->script_uri)){
					log_message("error","carabinerwrapper->pathForUrl(".$url.") : does not contain the carabiner-configured script_uri!");
					return false;
				}else{
					return $this->carabiner->script_path . substr($url,strlen($this->carabiner->script_uri));
				}
			}else{
				if(!(substr($url,0,strlen($this->carabiner->cache_uri)) == $this->carabiner->cache_uri)){
					log_message("error","carabinerwrapper->pathForUrl(".$url.") : does not contain the carabiner-configured cache_uri!");
					return false;
				}else{
					return $this->carabiner->cache_path . substr($url,strlen($this->carabiner->cache_uri));
				}
			}
		}
	}
	
}
?>