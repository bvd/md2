<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . "controllers/fcf/sessioncontroller.php");

class Test extends Sessioncontroller {

	public function __construct()
	{
		parent::__construct();
	}
	
	function index(){
		$this->load->library('unit_test');
		$this->unit->use_strict(TRUE);
		$this->load->model("fcf/Session");
		$this->sessionGetWritables();
		$this->sessionGetReadables();
		$this->sessionGetEnumeratedKeys();
		$this->sessionGetAvailable();
		$this->sessionGetAvailableError();
		$this->sessionSet();
		$this->sessionSetResult();
		$this->sessionSetUnavailable();
		$this->sessionSetReadOnly();
		$this->sessionSetNonexistent();
		$this->sessionGet();
		$this->sessionGetUnreadable();
		$this->sessionGetNonexistent();
		echo $this->unit->report();
	}
	
	private function sessionGetWritables(){
		$test = json_encode($this->Session->getWritableKeys());
		$test_name = 's/getWritableKeys: ' . $test . '<br />';
		$expected_result = '["language"]';
		$test_name .= 'expected result: ' . $expected_result;
		$this->unit->run($test, $expected_result, $test_name);
	}
	
	private function sessionGetReadables(){
		$test = json_encode($this->Session->getReadableKeys());
		$expected_result = '["language","ssid","username"]';
		$test_name = 's/getReadableKeys: ' . $test . '<br />';
		$test_name .= 'expected result: ' . $expected_result;
		$this->unit->run($test, $expected_result, $test_name);
	}
	
	private function sessionGetEnumeratedKeys(){
		$test = json_encode($this->Session->getEnumeratedKeys());
		$test_name = 's/getEnumeratedKeys: ' . $test . '<br />';
		$expected_result = '["language"]';
		$test_name .= 'expected result: ' . $expected_result;
		$this->unit->run($test, $expected_result, $test_name);
	}
	
	private function sessionGetAvailable(){
		$available = $this->Session->getEnumeratedKeys();
		$r = array();
		$args = array();
		foreach($available as $key){
			$r[] = array($key => $this->Session->getAvailable($key));
			$args[] = $key;
		}
		$test = json_encode($r);
		$test_name = 's/getAvailable/arg tested for args:<br /> ' . json_encode($args) . ' :<br />';
		$i = 0;
		while($i < count($args)){
			$test_name .= 's/getAvailable/' . $args[$i] . ' output: ' . json_encode($r[$i]) . '<br />';
			$i++;
		}
		$expected_result =  '[{"language":{"ENGLISH":"en","DUTCH":"nl"}}]' ;
		$test_name .= 'expected result: ' . $expected_result;
		$this->unit->run($test, $expected_result, $test_name);
	}
	private function sessionGetAvailableError(){
		$error = $this->Session->getAvailable("user");
		$test = json_encode($error);
		$test_name = 's/getAvailable/user output: ' . $test .'<br />';
		$expected_result = '{"status":"error","code":"1349118935","message":"no enumeration provided for user","messageTemplate":"no enumeration provided for %s","messageArgs":"user"}';
		$test_name .= 'expected result: ' . $expected_result;
		$this->unit->run($test, $expected_result, $test_name);
	}
	private function sessionSet(){
		$writables = $this->Session->getWritableKeys();
		$enumerated = $this->Session->getEnumeratedKeys();
		$test_name = 's/set/key/value tested...<br />';
		$results = array();
		foreach($writables as $writable){
			$availables = null;
			if(in_array($writable,$enumerated)){
				$availables = $this->Session->getAvailable($writable);
			}
			if($availables){
				foreach($availables as $available){
					$output = $this->Session->set($writable,$available);
					$test_name .= "s/set/" . $writable . '/' . $available .' output: ' . json_encode($output) . "<br />";
					$results[] = $output;
				}
			}else{
				$output = $this->Session->set($writable,"something");
				$test_name .= "s/set/" . $writable . '/something output: ' . $output . "<br />";
				$results[] = $output;
			}
		}
		$expected_result = '[{"language":"en"},{"language":"nl"}]';
		$test_name .= 'expected result: ' . $expected_result;
		$this->unit->run(json_encode($results), $expected_result, $test_name);
	}
	private function sessionSetResult(){
		$writables = $this->Session->getWritableKeys();
		$enumerated = $this->Session->getEnumeratedKeys();
		$test_name = 's/set/key/value tested...<br />';
		$results = array();
		foreach($writables as $writable){
			$availables = null;
			if(in_array($writable,$enumerated)){
				$availables = $this->Session->getAvailable($writable);
			}
			if($availables){
				foreach($availables as $available){
					$this->Session->set($writable,$available);
					$result = array($writable => $_SESSION[$writable]);
					$test_name .= "s/set/" . $writable . '/' . $available .' result in session: ' . json_encode($result) . "<br />";
					$results[] = $result;
				}
			}else{
				$this->Session->set($writable,"something");
				$result = array($writable => $_SESSION[$writable]);
				$test_name .= "s/set/" . $writable . '/something result in session: ' . $result . "<br />";
				$results[] = $result;
			}
		}
		$expected_result = '[{"language":"en"},{"language":"nl"}]';
		$test_name .= 'expected result: ' . $expected_result;
		$this->unit->run(json_encode($results), $expected_result, $test_name);
	}
	private function sessionSetUnavailable(){
		$writables = $this->Session->getWritableKeys();
		$enumerated = $this->Session->getEnumeratedKeys();
		$test_name = 's/set/key/value tested...<br />';
		$results = array();
		foreach($writables as $writable){
			$availables = null;
			if(in_array($writable,$enumerated)){
				$availables = $this->Session->getAvailable($writable);
			}
			if($availables){
				$output = $this->Session->set($writable,"qw");
				$test_name .= "s/set/" . $writable . '/qw output: ' . json_encode($output) . "<br />";
				$results[] = $output;
			}else{
				$output = $this->Session->set($writable,"something");
				$test_name .= "s/set/" . $writable . '/something output: ' . $output . "<br />";
				$results[] = $output;
			}
		}
		$expected_result = '[{"status":"error","code":"1349114039","message":"qw is not an allowed value for language","messageTemplate":"%s is not an allowed value for %s","messageArgs":["qw","language"]}]';
		$test_name .= 'expected result: ' . $expected_result;
		$this->unit->run(json_encode($results), $expected_result, $test_name);
	}
	private function sessionSetReadOnly(){
		$readables = $this->Session->getReadableKeys();
		$writables = $this->Session->getWritableKeys();
		$test_name = 's/set/key/value tested...<br />';
		$results = array();
		foreach($readables as $readable){
			if(!(in_array($readable,$writables))){
				$output = $this->Session->set($readable,"something");
				$test_name .= "s/set/" . $readable . '/something output: ' . json_encode($output) . "<br />";
				$results[] = $output;
			}
		}
		$expected_result = '[{"status":"error","code":"1349114731","message":"not allowed to set a value for ssid","messageTemplate":"not allowed to set a value for %s","messageArgs":["ssid"]},{"status":"error","code":"1349114731","message":"not allowed to set a value for username","messageTemplate":"not allowed to set a value for %s","messageArgs":["username"]}]';
		$test_name .= 'expected result: ' . $expected_result;
		$this->unit->run(json_encode($results), $expected_result, $test_name);
	}
	private function sessionSetNonexistent(){
		$error = $this->Session->set("lalali","lalala");
		$test = json_encode($error);
		$test_name = 's/set/lalali/lalala output: ' . $test .'<br />';
		$expected_result = '{"status":"error","code":"1349114731","message":"not allowed to set a value for lalali","messageTemplate":"not allowed to set a value for %s","messageArgs":["lalali"]}';
		$test_name .= 'expected result: ' . $expected_result;
		$this->unit->run($test, $expected_result, $test_name);
	}
	private function sessionGet(){
		$readables = $this->Session->getReadableKeys();
		$test_name = 's/get/key tested...<br />';
		$results = array();
		foreach($readables as $readable){
			$output = $this->Session->get($readable);
			$test_name .= "s/get/" . $readable . ' output: ' . json_encode($output) . "<br />";
			$results[$readable] = $output;
		}
		// the session will be different all the time but can be regex'd
		if(array_key_exists("ssid",$results)){
			$results["ssid"] = preg_match('/^[a-zA-Z0-9]{26,40}$/', $results["ssid"]) ? "ok" : "fail";
			$test_name .= "testing the session id with a regexp....<br />";
		}
		$expected_result = '{"language":"nl","ssid":"ok","username":"nobody"}';
		$test_name .= 'expected result: ' . $expected_result;
		$this->unit->run(json_encode($results), $expected_result, $test_name);
	}
	private function sessionGetUnreadable(){
		$error = $this->Session->get("uid");
		$test = json_encode($error);
		$test_name = 's/get/uid output: ' . $test .'<br />';
		$expected_result = '{"status":"error","code":"1349115620","message":"not allowed to get a value for uid","messageTemplate":"not allowed to get a value for %s","messageArgs":["uid"]}';
		$test_name .= 'expected result: ' . $expected_result;
		$this->unit->run($test, $expected_result, $test_name);
	}
	private function sessionGetNonexistent(){
		$error = $this->Session->get("boo");
		$test = json_encode($error);
		$test_name = 's/get/boo output: ' . $test .'<br />';
		$expected_result = '{"status":"error","code":"1349115620","message":"not allowed to get a value for boo","messageTemplate":"not allowed to get a value for %s","messageArgs":["boo"]}';
		$test_name .= 'expected result: ' . $expected_result;
		$this->unit->run($test, $expected_result, $test_name);
	}
}