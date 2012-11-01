<?php

class Sendmail extends CI_Controller {
	
	
	public function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		echo "sendmail->index";
	}
	/*
	URL EXAMPLE
	
	http://www.voorbeeld.nl/sendmail/nieuwsbrief/sep2012/test/3/Voorbeeld__SPACE__Subject/12345678/exampleEnvelopeDomain__DOT__nl
	
	*/
	function nieuwsbrief($bestand, $verzendingID, $maxAdressen, $subject, $wachtwoord = null, $domein = null){
		
		if(!(isset($bestand))) die("exit");
		if(!(isset($verzendingID))) die("exit");
		if(!(isset($maxAdressen))) die("exit");
		
		if(!(is_numeric($maxAdressen))) die("invalid params");
		
		function repl($str){
			$str = preg_replace("/__DOT__/",".",$str);
			$str = preg_replace("/__SPACE__/"," ",$str);
			$str = preg_replace("/__AT__/","@",$str);
			return $str;
		}
		
		$domein = repl($domein);
		$subject = repl($subject);
		$replyToName = repl($replyToName);
		$replyToAddress = repl($replyToAddress);
		$replyToOrganization = repl($replyToOrganization);
		
		log_message('debug', 'sendmail->nieuwsbrief');
		log_message('debug', 'uri: ' . $this->uri->uri_string());
		log_message('debug', date("D M j G:i:s"));
		
		session_start();
		
		if(!isset($_SESSION['user'])){
			if(!(isset($wachtwoord))){
				die("no permission");
			}else{
				if(!($wachtwoord == $this->config->item("sendmail_script_password"))){
					die("no permission");
				}
			}
		}
		
		// is er al eerder een dergelijke verzending geweest?
		$shipmentFile = $this->config->item("hidden_dir") . "sendmail_nieuwsbrief_" . $verzendingID;
		if(!(file_exists($shipmentFile))){
			if(!(true === touch($shipmentFile))){
				log_message("error","sendmail->nieuwsbrief cannot touch " . $shipmentFile);
				die("1");
			}else{
				$meta = new stdClass();
				// per shipment always the same domain needs be used
				$meta->domain = $domein == null ? "" : $domein;
				$meta->line = 0;
				file_put_contents($shipmentFile,json_encode($meta));
			}
		}
		if(!(true === touch($shipmentFile))){
			log_message("error","sendmail->nieuwsbrief cannot touch existing " . $shipmentFile);
			die("3");
		}
		
		// open metadata
		$meta = json_decode(file_get_contents($shipmentFile));
		
		// what was the last processed line?
		$lastLine = $meta->line;
		
		// was it limited to a domain and if so, which
		$domain = $meta->domain;
		
		if($domein == null && $domain != ""){
			die("is something going wrong? Domain in file: " . $domain);	
		}
		if($domein != null && $domain == ""){
			die("file has no domain");
		}
		if($domein != null && $domain != ""){
			if($domein != $domain){
				die("domain in file: " . $domain . "!, is something going wrong?");	
			}
		}
		
		// move the file handler to the first unprocessed line
		$recepients_file = $this->config->item("hidden_dir")."newsletter_recepients";
		$rf = fopen($recepients_file,'r');
		if(false === $rf){
			log_message("error","sendmail newsletter cannot open " . $recepients_file);
			die("15");	
		}
		$lineCount = 0;
		while($lineCount < $lastLine){
			if(fgets($rf, 4096) === false){
				log_message("debug","reached end of file, line number " . $lineCount);
				break;
			}
			$lineCount++;
		}
		
		
		// from here we work with line count and will update the file after we're done
		
		$sent = 0;
		$bcc = array();
		
		
		while(false !== ($address = fgets($rf, 4096))){
			
			$lineCount++;
			
			$address = strtolower($address);
			
			$pos = strpos($address,"@");
			if($pos === false){
				log_message("info","sendmail->nieuwsbrief reached end of file");
				break;	
			}else{
				if($domain){
					if(strpos($address,$domain)){
						if(strpos($address,$domain) == strlen($address) - 1 - strlen($domain)){
							$bcc[] = $address;
							$sent++;
							if($sent == $maxAdressen){
								break;
							}
						}
					}
				}else{
					$bcc[] = $address;
					$sent++;
					if($sent == $maxAdressen){
						break;
					}	
				}	
			}
		}
		
		$meta->line = $lineCount;
		file_put_contents($shipmentFile,json_encode($meta));
		
		
		$body             = file_get_contents(FCPATH . "nieuwsbrieven/" . $bestand . ".html");
		
		require_once(APPPATH . "libraries/phpmailer/class.phpmailer.php");
		require_once(APPPATH . "libraries/phpmailer/class.smtp.php");
		
		
		$mail             = new PHPMailer();
		
		$mail->IsSMTP();
		$mail->SMTPDebug  = $this->config->item("sendmail_smtp_debug");
		$mail->SMTPAuth   = $this->config->item("sendmail_smtp_auth");
		$mail->SMTPSecure = $this->config->item("sendmail_smtp_secure");
		$mail->Host       = $this->config->item("sendmail_smtp_host");
		$mail->Port       = $this->config->item("sendmail_smtp_port");
		$mail->Username   = $this->config->item("sendmail_smtp_user_name");
		$mail->Password   = $this->config->item("sendmail_smtp_user_pass");
		$mail->SetFrom($this->config->item("sendmail_smtp_from_address"), $this->config->item("sendmail_smtp_from_name"));
		$mail->AddReplyTo($this->config->item("sendmail_smtp_reply_to_address"), $this->config->item("sendmail_smtp_reply_to_name"));
		
		$mail->Subject    = $subject;
		
		$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!";
		
		$mail->MsgHTML($body);
		
		foreach($bcc as $bccAddress){
			$mail->AddBCC($bccAddress);	
		}
		if(!$mail->Send()) {
		  echo "Mailer Error: " . $mail->ErrorInfo;
		} else {
		  echo "Message sent!";
		  echo "<br />" . json_encode($meta);
		}
	}
	private function read_file($file, $lines) {
	    //global $fsize;
	    $handle = @fopen($file, "r");
	    $linecounter = $lines;
	    $pos = -2;
	    $beginning = false;
	    $text = array();
	    while ($linecounter > 0) {
	        $t = " ";
	        while ($t != "\n") {
	            if(fseek($handle, $pos, SEEK_END) == -1) {
	                $beginning = true; 
	                break; 
	            }
	            $t = fgetc($handle);
	            $pos --;
	        }
	        $linecounter --;
	        if ($beginning) {
	            rewind($handle);
	        }
	        $text[$lines-$linecounter-1] = fgets($handle);
	        if ($beginning) break;
	    }
	    fclose ($handle);
	    return array_reverse($text);
	} 
}
?>
