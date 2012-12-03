<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('fcf_parse_simplexml'))
{
	/**
	 * Parses a string to a simplexml doc.
	 * On error, logs to CI error log.
	 * On warning, logs to CI info log.
	 * 
	 * @param string $string
	 * @param boolean $ignoreWarningsOnSuccess
	 * @return mixed On error an associative array is returned for example 
	 * array(array("string"=>completeMessageString,"object"=>libxml_error),...)
	 * On success a simple xml document is returned.
	 */
	function fcf_parse_simplexml($string, $ignoreWarningsOnSuccess = true)
	{
		libxml_use_internal_errors(true);
		$doc = simplexml_load_string($string);
		if((!$doc)||(!$ignoreWarningsOnSuccess)){
			$xml = explode("\n", $string);
			$errors = libxml_get_errors();
			$returnErrors = array();
			foreach ($errors as $error) {
				$str = "";
				$str .= $xml[$error->line - 1] . "\n";
				$str .= str_repeat('-', $error->column) . "^\n";
				switch ($error->level) {
					case LIBXML_ERR_WARNING:
						$str .= "Warning $error->code: ";
						break;
					case LIBXML_ERR_ERROR:
						$str .= "Error $error->code: ";
						break;
					case LIBXML_ERR_FATAL:
						$str .= "Fatal Error $error->code: ";
						break;
				}
				$str .= trim($error->message);
				$str .= "\n  Line: $error->line";
				$str .= "\n  Column: $error->column";
			
				if ($error->file) {
					$str .= "\n  File: $error->file";
				}
				
				switch ($error->level) {
					case LIBXML_ERR_WARNING:
						log_message("info","proxytweet xml warning: " . $str);
						break;
					case LIBXML_ERR_ERROR:
						log_message("error","proxytweet xml error: " . $str);
						break;
					case LIBXML_ERR_FATAL:
						log_message("error","proxytweet xml fatal error: " . $str);
						break;
				}
				
				$returnErrors[] = array("string" => $str, "object" => $error);
			}
			if(!$doc){
				libxml_clear_errors();
				return false;
			}
		}
		libxml_clear_errors();
		return $doc;
	}
}