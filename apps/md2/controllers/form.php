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
		switch($formID){
			case "sollicitatieFormulier":
				$ret = $this->sollicitatieFormulier();
				break;	
		}
		echo json_encode($ret);
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
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$rcVerifResult = curl_exec($ch);
		curl_close($ch);
		$rcVerifResult = explode("\n",$rcVerifResult);
		return $rcVerifResult[0] == "true";
	}
	private function sollicitatieFormulier(){
		$this->formID = "sollicitatieFormulier";
		$data = $_POST;
		/*
			$data options:
			
			contactPersonName
			mailFormSubmissionTo
			achternaam
			cvuploadveld
			email
			functionName
			motivatie
			rcChallenge
			rcResponse
			telnr1
			telnr2
			tussenvoegsels
			voornaam
			thankYouMessage
		*/
		$errors = array();
		
		$mustExist = array("achternaam","email","functionName","rcChallenge","rcResponse","voornaam","contactPersonName","mailFormSubmissionTo");
		$mustContainString = array("achternaam","functionName","rcChallenge","rcResponse","voornaam");
		$mustContainEmail = array("email","mailFormSubmissionTo");
		
		$errors = array_merge($errors,$this->propertyExists($data, $mustExist));
		$errors = array_merge($errors,$this->existingPropertyIsString($data, $mustContainString));
		$errors = array_merge($errors,$this->existingPropertyIsEmail($data, $mustContainEmail));
		
		if(count($errors)) {
			$errors["formID"] = $this->formID;
			return $errors;
		}
		if(!($this->verifyRecaptcha($data["rcChallenge"],$data["rcResponse"]))) {
			$errors["formID"] = $this->formID;
			$errors[] = $this->error("5","rcResponse");
			return $errors;
		}
		
		// collect the data to send
		
		$to = $data["mailFormSubmissionTo"];
		$subj = $data["functionName"];
		$attachment = $this->config->item("tmp_up_dir") . $data["cvuploadveld"];
		if(is_dir($attachment)) unset($attachment);
		
		$data["cvFile"] = isset($attachment) ? "zie bijlage / see attachment" : "geen bestand toegevoegd";
		
		log_message("debug","succes1");
		
		// generate the view / body
		
		$body = $this->load->view("sollicitatieform",$data,true);
		
		// send
		
		log_message("debug","succes2");
		
		require_once(APPPATH . "libraries/class.phpmailer.php");
		
		$mail = new PHPMailer();
		$mail->SetFrom($this->config->item("form_submit_mail_from_address"), $this->config->item("form_submit_mail_from_name"));
		$mail->AddReplyTo($this->config->item("form_submit_mail_reply_to_address"), $this->config->item("form_submit_mail_reply_to_name"));
		$mail->Subject    = $subj;
		$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
		$mail->MsgHTML($body);
		
		$mail->AddAddress($data["mailFormSubmissionTo"]);
		if(isset($attachment)) $mail->AddAttachment($attachment, $data["cvuploadveld"]);
		
		
		if(!$mail->Send()) {
		  return array("result" => "error", "formID" => $this->formID, "mailerError" => $mail->ErrorInfo);
		} else {
		  return array("result" => "success", "formID" => $this->formID);
		}
	}
}
?>