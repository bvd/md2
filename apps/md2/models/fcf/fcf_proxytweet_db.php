<?php
class Fcf_proxytweet_db extends CI_Model {

	private $_config_cache_path;
	private $_config_proxytweet_url;
	private $_file;
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
		if(is_file($this->_file)){
			$fileMTime = filemtime($this->_file);
			if($this->_timeNow - $fileMTime < 60){
				$tweetXML = $this->_file;
			}
		}
		
		if(!(isset($tweetXML))){
			log_message('debug', 'no file, or file too old, so: query API');
			$tweetXML = $this->_get_latest()->asXML();
			$new = true;
		}else{
			log_message('debug', 'getting twitter data cached in local file');
			$tweetXML = file_get_contents($tweetXML);
			$new = false;
		}
		
		if(strlen($tweetXML) < 300){
			log_message("error","an error message from the twitter API: " . $tweetXML);
			$tweetXML = file_get_contents($this->_file);
		}else{
			if($new){
				if(!(file_put_contents($this->_file, $tweetXML))){
					log_message('error', 'proxytweet could not write xml for twitfeed at location ' . $this->_file);
				}
			}
		}
		
		return $tweetXML;	
    }
	private function _get_latest(){
		// Initialize session and set URL.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->_config_proxytweet_url);
		// Set so curl_exec returns the result instead of outputting it.
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Get the response and close the channel.
		$response = curl_exec($ch);
		if($response) {
			return $this->_filter($response);
		}
		else{
			log_message("error", "CURL response false for " . $this->_config_proxytweet_url);
			return false;
		}
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