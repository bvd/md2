<?php

include_once(APPPATH . "models/fcf/keyvaluemodel.php");

class Fcf_session extends KeyValueModel {
    
	public function __construct()
	{
		session_start();
		$this->PUBLIC_READ_ONLY["ssid"] = session_id();
		parent::__construct();
	}
	
    // override
    protected $ENUMERATED_KEYS = array(
		"language"
    );
    // override
    protected $AVAILABLE_VALUES = array(
		"language" => array(
			"ENGLISH" 			=> "en",
			"DUTCH"				=> "nl"
		)
    );
    // override
    protected $PUBLIC_READ_ONLY = array(
		"ssid" 			=> "",
		"username" 		=> "nobody"
    );
    // override
    protected $PUBLIC = array(
		"language" 		=> "en"
    );
    // override
    protected $PRIVATE = array(
		"uid" 			=> -1,
		"token"			=> -1
    );
    // for superclass, called if allowed only
    protected function read($key){
		if(isset($_SESSION[$key])) return $_SESSION[$key];
		if(array_key_exists($key, $this->PUBLIC)) return $this->PUBLIC[$key];
		if(array_key_exists($key, $this->PUBLIC_READ_ONLY)) return $this->PUBLIC_READ_ONLY[$key];
    }
    protected function write($key,$value){
		$_SESSION[$key] = $value;
		return array($key => $value);
    }
}
?>