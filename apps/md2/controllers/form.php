<?php

class Form extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		log_message('debug', '-------------------');
		log_message('debug', '- - - - FORM  - - -');
		log_message('debug', '-------------------');
		log_message('debug', 'uri: ' . $this->uri->uri_string());
		log_message('debug', date("D M j G:i:s"));
		session_start();
	}
	
	function index()
	{
		$ret= new stdClass();
		$ret->form = "ok";
		return json_encode($ret);
	}
	function submit($formID)
	{
		$this->formID = $formID;
		$data = $_POST;
		$errors = array();
		
		// load processing instructions
		require(APPPATH . "views/form/" . $formID . ".php");
		
		$errors = array_merge($errors,$this->propertyExists($data, $mustExist));
		$errors = array_merge($errors,$this->existingPropertyIsString($data, $mustContainString));
		$errors = array_merge($errors,$this->existingPropertyIsEmail($data, $mustContainEmail));
		
		if(count($errors)) {
			$errors["formID"] = $this->formID;
			return json_encode($errors);
		}
		
		if(isset($requireRecaptcha)){
			if(is_array($requireRecaptcha)){
				if(array_key_exists('challenge',$requireRecaptcha) && array_key_exists('response',$requireRecaptcha)){
					$rcFields = array($requireRecaptcha["challenge"],$requireRecaptcha["challenge"]);
					$errors = array_merge($errors,$this->propertyExists($data, $rcFields));
					$errors = array_merge($errors,$this->existingPropertyIsString($data, $rcFields));
					if(count($errors)) {
						$errors["formID"] = $this->formID;
						return json_encode($errors);
					}
					$chall = $data[$requireRecaptcha["challenge"]];
					$resp = $data[$requireRecaptcha["challenge"]];
					if(!($this->verifyRecaptcha($chall,$resp))) {
						$errors["formID"] = $this->formID;
						$errors[] = $this->error("5","rcResponse");
						return json_encode($errors);
					}else{
						log_message("debug","recaptcha verification success");
					}
				}else{
					log_message("error","incorrect recaptcha fields array in view/form/".$this->formID.".php");
				}
			}else{
				log_message("error","incorrect recaptcha fields array in view/form/".$this->formID.".php");
			}
		}else{
			log_message("debug","no recaptcha verification required for " . $this->formID);
		}
		
		// collect the data to send
		
		
	}
	private $eMsg = array(
		"1" => array(
			"en" => "missing property",
			"nl" => "ontbrekende eigenschap"
		),
		"2" => array(
			"en" => "is not a string",
			"nl" => "is geen string"
		),
		"3" => array(
			"en" => "cannot be empty",
			"nl" => "mag niet leeg zijn"
		),
		"4" => array(
			"en" => "is not a valid email address",
			"nl" => "is geen geldig email adres"
		),
		"5" => array(
			"en" => "please try again",
			"nl" => "probeer het a.u.b. opnieuw"
		)
	);
	private $formID = "null";
	private function error($code, $fieldID, $value = null){
		$e = new stdClass();
		$e->message = $this->eMsg[$code];
		$e->error = $code;
		$e->field = $fieldID;
		$e->value = $value;
		$e->form = $this->formID;
		return $e;	
	}
	private function propertyExists(&$array, $keys){
		$ret = array();
		foreach($keys as $k){
			if(!array_key_exists($k,$array)){
				$ret[] =  $this->error("1",$k);
			}
		}
		return $ret;
	}
	private function existingPropertyIsString(&$array, $keys){
		$ret = array();
		foreach($keys as $k){
			if(!array_key_exists($k,$array)){
				continue;	
			}
			if(!is_string($array[$k])){
				$ret[] =  $this->error("2",$k);
				unset($array[$k]);
			}
			if($array[$k] == ""){
				$ret[] =  $this->error("3",$k);
				unset($array[$k]);
			}
		}
		return $ret;
	}
	private function existingPropertyIsEmail(&$array, $keys){
		require_once(APPPATH . "libraries/is_email.php");
		$ret = array();
		foreach($keys as $k){
			if(!array_key_exists($k,$array)){
				continue;	
			}
			if(!is_string($array[$k])){
				$ret[] = $this->error("2",$k);
				unset($array[$k]);
			}
			if(!(is_email($array[$k]))){
				$ret[] = $this->error("4",$k);
				unset($array[$k]);
			}
		}
		return $ret;
	}
	private function getRealIP(){
		if (!empty($_SERVER['HTTP_CLIENT_IP']))
		//check ip from share internet
		{
		  $ip=$_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		//to check ip is pass from proxy
		{
		  $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
		  $ip=$_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	private function verifyRecaptcha($challenge, $response){
		$url = 'http://www.google.com/recaptcha/api/verify';
		$ch = curl_init($url);
		$postFields = array(
			"privatekey" => $this->config->item("recaptcha_private_key"),
			"remoteip" => $this->getRealIP(),
			"challenge" => $challenge,
			"response" => $response
		);
		log_message("debug","sending to recaptcha: " . print_r($postFields,true));
		curl_setopt($ch, CURLOPT_COOKIESESSION, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$rcVerifResult = curl_exec($ch);
		curl_close($ch);
		$rcVerifResult = explode("\n",$rcVerifResult);
		log_message("debug","recaptcha API says: " . json_encode($rcVerifResult));
		return $rcVerifResult[0] == "true";
	}
	private function sendmail($args){
		
		$body = $this->load->view("email/" . $args["mailview"], $args["viewdata"], true);
		
		$attachment = array_key_exists( $args, "attachment") ? $args["attachment"] : false;
		$attachmentName = array_key_exists( $args, "attachmentName") ? $args["attachmentName"] : false;
		if(is_dir($attachment)) unset($attachment);
		
		require_once(APPPATH . "libraries/class.phpmailer.php");
		
		$mail = new PHPMailer();
		$mail->SetFrom($args["from"],$args["fromName"]);
		$mail->AddReplyTo($args["replyTo"],$args["replyToName"]);
		$mail->Subject    = $args["subj"];
		$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!";
		$mail->MsgHTML($body);
		
		foreach($args["to"] as $addr){
			$mail->AddAddress($addr);
		}
		
		if(isset($attachment)) $mail->AddAttachment($attachment, $attachmentName);
		
		if(!$mail->Send()) {
		  return array("result" => "error", "formID" => $this->formID, "mailerError" => $mail->ErrorInfo);
		} else {
		  return array("result" => "success", "formID" => $this->formID);
		}
	}
}
?>