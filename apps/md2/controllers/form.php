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
	
	public function index()
	{
		$ret= new stdClass();
		$ret->form = "ok";
		return json_encode($ret);
	}
	public function submit($formID)
	{
		log_message("debug","form->submit(" . $formID . ")");
		
		$this->formID = $formID;
		$data = $_POST;
		log_message("debug","$_POST-ed data: " . print_r($data,true));
		$errors = array();
		
		require(APPPATH . "views/form/" . $formID . ".php");
		log_message("debug","form processing instructions loaded: " . APPPATH . "views/form/" . $formID . ".php");
		
		$errors = array_merge($errors,$this->propertyExists($data, $mustExist));
		$errors = array_merge($errors,$this->existingPropertyIsString($data, $mustContainString));
		$errors = array_merge($errors,$this->existingPropertyIsEmail($data, $mustContainEmail));
		
		if(count($errors)) {
			$errors["formID"] = $this->formID;
			exit(json_encode($errors));
		}else{
			log_message("debug","validators success");
		}
		
		if(isset($requireRecaptcha)){
			if(is_array($requireRecaptcha)){
				if(array_key_exists('challenge',$requireRecaptcha) && array_key_exists('response',$requireRecaptcha)){
					$rcFields = array($requireRecaptcha["challenge"],$requireRecaptcha["response"]);
					$errors = array_merge($errors,$this->propertyExists($data, $rcFields));
					$errors = array_merge($errors,$this->existingPropertyIsString($data, $rcFields));
					if(count($errors)) {
						$errors["formID"] = $this->formID;
						exit(json_encode($errors));
					}
					$chall = $data[$requireRecaptcha["challenge"]];
					$resp = $data[$requireRecaptcha["response"]];
					if(!($this->verifyRecaptcha($chall,$resp))) {
						$errors["formID"] = $this->formID;
						$errors[] = $this->error("5","rcResponse");
						exit(json_encode($errors));
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
		
		log_message("debug","validation success, applying process as configured per-form...");
		$results = array();
		foreach($onSuccess as $functionName => $args){
			$results = array_merge($results,call_user_func_array(array($this,$functionName),$args));
		}
		exit(json_encode($results));
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
	/**
	 * $args
	 */
	private function sendmail($mailViewName,$mailViewData,$toAddresses,$subject,$attachment,$attachmentName,$fromAddress,$fromName,$replyToAddress,$replyToName){
		
		log_message("debug","function sendmail...");
		
		require_once(APPPATH . "libraries/class.phpmailer.php");
		log_message("debug","included " . APPPATH . "libraries/class.phpmailer.php");
		
		$mail = new PHPMailer();
		log_message("debug","created PHPMailer object");
		$mail->SetFrom($fromAddress,$fromName);
		log_message("debug","added from address: " . $fromAddress);
		$mail->AddReplyTo($replyToAddress,$replyToName);
		log_message("debug","added reply to address: " . $replyToAddress);
		$mail->Subject    = $subject;
		log_message("debug","subject: " . $subject);
		$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!";
		$mail->MsgHTML($this->load->view("email/" . $mailViewName, $mailViewData, true));
		log_message("debug","added mail body from view " . "email/" . $mailViewName);
		foreach($toAddresses as $addr){
			$mail->AddAddress($addr);
		}
		log_message("debug","added to-addresses: " . print_r($toAddresses,true));
		if($attachment){
			$mail->AddAttachment($attachment, $attachmentName);
			log_message("debug","attachment added: " . $attachment);
		}else{
			log_message("debug","no attachment");
		}
		
		if(!$mail->Send()) {
		  log_message("debug","error sending mail: " . $mail->ErrorInfo);
		  return array("result" => "error", "formID" => $this->formID, "mailerError" => $mail->ErrorInfo);
		} else {
		  log_message("debug","successfully sent mail, returning form id and result status");
		  return array("result" => "success", "formID" => $this->formID);
		}
	}
}
?>