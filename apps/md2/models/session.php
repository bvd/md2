<?php

include_once(APPPATH . "models/fcf/fcf_session.php");

class Session extends Fcf_session {
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
		"language" 		=> "nl"
    );
    // override
    protected $PRIVATE = array(
		"uid" 			=> -1,
		"token"			=> -1
    );
}
?>