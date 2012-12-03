<?php
class Fcf_proxytweet_db extends CI_Model {

	private $_config_cache_path;
	private $_config_proxytweet_url;
	private $_file;
	private $_filemtime;
	private $_is_file;
	private $_file_initialized;
	private $_timeNow;

	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
		$this->_config_cache_path = $this->config->item('cache_dir');
		$this->_config_proxytweet_url = $this->config->item('proxytweet_url');
		$this->_file = $this->_config_cache_path . 'recentTweet.xml';
		$this->_timeNow = time();
	}
    public function get_recent_data(){
    	$xmlStringFromApi = false;
    	$cachedXmlString = $this->_tryReadFile(false);
    	if(!$cachedXmlString){
    		$xmlStringFromApi = $this->_get_latest();
    		$valid = $this->_validate($xmlStringFromApi);
    		if($valid){
    			$doc = $this->_filter($xmlStringFromApi);
    			$xmlStringFromApi = $doc->asXML();
    			$this->_storeToCache($xmlStringFromApi);
    		}else{
    			$xmlStringFromApi = false;
    		}
    	}
    	if(!($xmlStringFromApi || $cachedXmlString)){
    		$cachedXmlString = $this->_tryReadFile(true);
    	}
		if($xmlStringFromApi){
			return $xmlStringFromApi;
		}
		if($cachedXmlString){
			return $cachedXmlString;
		}
		return "";
    }
    private function _tryReadFile($allowTooOld = false){
    	if(!$this->_file_initialized){
    		$this->_is_file = is_file($this->_file);
    		if($this->_is_file){
    			$this->_filemtime = filemtime($this->_file);
    		}
    		$this->_file_initialized = true;
    	}
    	if($this->_is_file){
    		if($allowTooOld){
    			return file_get_contents($this->_file);
    		}else if($this->_timeNow - $this->_filemtime < 60){
    			return file_get_contents($this->_file);
    		}
    	}
    	return false;
    }
    private function _storeToCache($string){
    	$out = file_put_contents($this->_file, $string);
    	if(!$out){
    		log_message('error', 'proxytweet could not write xml for twitfeed at location ' . $this->_file);
    	}
    	return $out;
    }
	private function _get_latest(){
		// Initialize session and set URL.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->_config_proxytweet_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		$response = curl_exec($ch);
		$curl_error = curl_error($ch);
		if(!($curl_error == "")){
			log_message("error", "proxytweet CURL error: " . $curl_error . " for " . $this->_config_proxytweet_url);
			$response = file_get_contents($this->_config_proxytweet_url);
			if(false === $response){
				log_message("error","proxytweet could not file_get_contents of " . $this->_config_proxytweet_url);
			}
		}
		return $response;
	}
	private function _validate($string){
		$this->load->helper('fcf_simplexml');
		$simplexml = fcf_parse_simplexml($string);
		if(is_array($simplexml)){
			return false;
		}
		$twitter_error = $this->_twitter_error($simplexml);
		if($twitter_error){
			log_message("error", "proxytweet twitter error: " . $twitter_error . " for " . $this->_config_proxytweet_url);
			return false;
		}
		return true;
	}
	private function _twitter_error($doc){
		if($doc->getName() == "errors"){
			return $doc->asXML();
		}
		return false;
	}
	
	private function _twitterify($ret) {
		$ret = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $ret);
		$ret = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $ret);
		$ret = preg_replace("/@(\w+)/", "<a href=\"http://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $ret);
		$ret = preg_replace("/#(\w+)/", "<a href=\"http://search.twitter.com/search?q=\\1\" target=\"_blank\">#\\1</a>", $ret);
		return $ret;
	}
	private function _filter($input){
		$twXML = simplexml_load_string($input);
		$output = simplexml_load_string("<twitfeed></twitfeed>");
		$count = 0;
		$maxNum = 3;
		foreach($twXML->status as $status){
			$child = $output->addChild("status");
			$child->text = $status->text;
			$count++;
			if($count == $maxNum) break;
		}
		foreach($output->status as $status){
			$status->text = $this->_twitterify($status->text);
		}
		return $output;
	}
}
?>