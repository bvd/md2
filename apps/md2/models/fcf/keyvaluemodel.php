<?php

include_once(APPPATH . "models/fcf/errorreturningmodel.php");

class Keyvaluemodel extends errorreturningmodel {

    public function __construct()
	{
		parent::__construct();
	}
    // tested
    public function getAvailable($key){
	if(!(array_key_exists($key,$this->AVAILABLE_VALUES))){
	    return $this->error("1349118935",$key);
	}else{
	    return $this->AVAILABLE_VALUES[$key];
	}
    }
    // virtual
    protected function read($key){
	return false;
    }
    // tested
    public function get($key = false){
	if(is_array($key)){
	    $r = array();
	    foreach($key as $k){
		$v = $this->get($k);
		if(!(is_string($v))){
		    return $v;
		}
		$r[] = $v;
	    }
	}
	else if(is_string($key)){
	    if(array_key_exists($key,$this->PUBLIC)){
		return $this->PUBLIC[$key];
	    }
	    if(array_key_exists($key,$this->PUBLIC_READ_ONLY)){
		return $this->PUBLIC_READ_ONLY[$key];
	    }
	    return $this->error("1349115620",array($key));
	}else if($key !== false){
	    
	}
	$r = new stdClass();
	foreach($this->PUBLIC as $k => $v){
	    $read = $this->read($k);
	    $r->$k = $read ? $read : $v;
	}
	foreach($this->PUBLIC_READ_ONLY as $k => $v){
	    $read = $this->read($k);
	    $r->$k = $read ? $read : $v;
	}
	return $r;
    }
    // tested
    public function set($key,$value){
	if(!array_key_exists($key, $this->PUBLIC)){
	    return $this->error("1349114731",array($key));
	}
	if(array_key_exists($key,$this->AVAILABLE_VALUES)){
	    if(false === array_search($value,$this->AVAILABLE_VALUES[$key])){
		return $this->error("1349114039",array($value,$key));
	    }
	}
	return $this->write($key, $value);
    }
    // tested
    public function getWritableKeys(){
	$r = array();
	foreach($this->PUBLIC as $k => $v){
	    $r[] = $k;
	}
	return $r;
    }
    // tested
    public function getReadableKeys(){
	$r = array();
	foreach($this->PUBLIC as $k => $v){
	    $r[] = $k;
	}
	foreach($this->PUBLIC_READ_ONLY as $k => $v){
	    $r[] = $k;
	}
	return $r;
    }
    // tested
    public function getEnumeratedKeys(){
	return $this->ENUMERATED_KEYS;
    }
    // virtual
    protected $ENUMERATED_KEYS = array( );
    // virtual
    protected $AVAILABLE_VALUES = array( );
    // virtual
    protected $PUBLIC_READ_ONLY = array( );
    // virtual
    protected $PUBLIC = array( );
    // virtual
    protected $PRIVATE = array( );
}
?>