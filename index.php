<?php

/*
Copyright (c) 2008-2012 E.K. VAN DALEN

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

/**
 * HELLO, WELCOME TO THE FAT CLIENT FRAMEWORK
 * (A BUNCH OF LIBRARIES TUNED FOR SPEED)
 *
 * THANKS TO CODEIGNITER, JQUERY AND MANY OTHERS....
 *
 * COPYRIGHT 2009-2012 E.K. VAN DALEN, THE NETHERLANDS
 */

/*
#
# SUGGESTED .HTACCESS SETTINGS
# TO BE PLACED NEXT TO THIS FILE
#
Options +FollowSymLinks
Options +Indexes
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?/$1 [L]
*/

/**
 * URL USAGE
 ****
 *
 * WE USE THE URL LIKE SO:
 * HTTP://DOMAIN.COM/APPNAME/CONTROLLER/VAR/VAR/...
 *
 * WHERE IF NO APPNAME IS SPECIFIED
 * WE LOAD A DEFAULT APP
 *
 * AND PER APP, IF NO CONTROLLER IS SPECIFIED
 * WE MAY LOAD A DEFAULT CONTROLLER
 *
 */

/**
 * PRINT
 ****
 *
 * USE THE FCF_PRINT FUNCTION TO SEE
 * WHAT IS HAPPENING...
 *
 */
function fcf_print($str){
	// uncomment the $print = true setting
	//$print = '';
	if(isset($print)){
		$out = "<pre style='white-space: pre-wrap; white-space: -moz-pre-wrap; white-space: -pre-wrap; white-space: -o-pre-wrap; word-wrap: break-word;'>";
		$out .= $str;
		$out .= "</pre>";
		print($out);
	}
}
/**
 * APPS AVAILABLE 
 ****
 *
 * THE KEY CAN BE USED IN THE URI
 * AND THE VALUE POINTS TO THE APP DIR
 *
 */
$fcf_apps_available = array( 
	"md2" => "md2"
);
/**
 * DEFAULT APP
 ****
 *
 * USED WHEN NO APP IS SPECIFIED
 * IN THE FIRST URI SEGMENT
 *
 */
$fcf_default_app_key =  "md2";
/**
 * APPS DIR
 ****
 *
 * MULTIPLE APPLICATIONS IN THE SAME DIRECTORY
 *
 */
$fcf_apps_dir = "apps";
/**
 * DEFINE ENVIRONMENT
 ****
 *
 * YOU SHOULD USE THE SERVER NAME TO DECIDE
 * WHERE YOU ARE AND WHICH THE BASIC VARIABLES ARE.
 * FOR EXAMPLE THERE CAN BE DIFFERENT DIRECTORIES
 * IN A HOSTING SERVICE WHERE ONE MAY BE READ-ONLY
 * WHILE THE OTHER IS WRITABLE AND WORLD READABLE
 * AND ANOTHER ONE IS WRITABLE AND SCRIPT READABLE
 * AND ANOTHER ONE IS ROTATED AND ANOTHER ONE IS
 * BACK-UPPED AND SO FORT...
 *
 */
define('ENVIRONMENT', 'bertus');
fcf_print("environment: " . ENVIRONMENT);
/**
 * DEFINE BASE DIRECTORIES (relative to the FC path)
 ****
 * The FCF_LOG_DIR is typically writable, not readable to the world
 * and can be cleaned up (presumably automatically) after some time.
 * The FCF_CACHE_DIR is typically writable by the server and world readable.
 * It should not lead to any problem to delete cached data!!!
 * The FCF_DATA_DIR is typically writable by the server and world readable.
 * It contains unique data that cannot be regenerated!!!
 * Of both the CACHE and DATA directories there is a private version
 * where users can store private data and to which access is regulated
 * by the app's permission system.
 * The FCF_TMP_DATA_DIR is meant to be cleared on a very regular basis for example
 * each hour where everything older than for example one hour will be deleted.
 * WATCH OUT!!! it is meant for files NOT VALIDATED YET and thus SHOULD NOT BE
 * EXECUTABLE OR ANYWAY ACCESSIBLE ELSE THAN BY SCRIPT!!!
 *
 */
if(ENVIRONMENT=="bertus"){
	define('FCF_LOG_DIR', "log");
	define('FCF_CACHE_DIR', "cache");
	define('FCF_DATA_DIR', "data");
	define('FCF_PRIV_CACHE_DIR', "priv_cache");
	define('FCF_PRIV_DATA_DIR', "priv_data");
	define('FCF_TMP_DATA_DIR', "tmp_data");
}
else {
	exit('index.php will not set directories for unknown env: '.ENVIRONMENT);
}

/**
 * ERROR REPORTING LEVEL BASED ON ENVIRONMENT
 ****
 *
 * OF COURSE IT MAY BE CHANGED AGAIN
 * ON THE APPLICATION LEVEL
 *
 */
if(ENVIRONMENT=="bertus") error_reporting(E_ALL);
else exit('index.php will not set error_reporting for unknown env: '.ENVIRONMENT);
$fcf_error_reporting = ini_get('error_reporting');
$fcf_human_error_reporting =""; 
if($fcf_error_reporting & E_ERROR) // 1 // 
	$fcf_human_error_reporting.='& E_ERROR '; 
if($fcf_error_reporting & E_WARNING) // 2 // 
	$fcf_human_error_reporting.='& E_WARNING '; 
if($fcf_error_reporting & E_PARSE) // 4 // 
	$fcf_human_error_reporting.='& E_PARSE '; 
if($fcf_error_reporting & E_NOTICE) // 8 // 
	$fcf_human_error_reporting.='& E_NOTICE '; 
if($fcf_error_reporting & E_CORE_ERROR) // 16 // 
	$fcf_human_error_reporting.='& E_CORE_ERROR '; 
if($fcf_error_reporting & E_CORE_WARNING) // 32 // 
	$fcf_human_error_reporting.='& E_CORE_WARNING '; 
if($fcf_error_reporting & E_CORE_ERROR) // 64 // 
	$fcf_human_error_reporting.='& E_COMPILE_ERROR '; 
if($fcf_error_reporting & E_CORE_WARNING) // 128 // 
	$fcf_human_error_reporting.='& E_COMPILE_WARNING '; 
if($fcf_error_reporting & E_USER_ERROR) // 256 // 
	$fcf_human_error_reporting.='& E_USER_ERROR '; 
if($fcf_error_reporting & E_USER_WARNING) // 512 // 
	$fcf_human_error_reporting.='& E_USER_WARNING '; 
if($fcf_error_reporting & E_USER_NOTICE) // 1024 // 
	$fcf_human_error_reporting.='& E_USER_NOTICE '; 
if($fcf_error_reporting & E_STRICT) // 2048 // 
	$fcf_human_error_reporting.='& E_STRICT '; 
if($fcf_error_reporting & E_RECOVERABLE_ERROR) // 4096 // 
	$fcf_human_error_reporting.='& E_RECOVERABLE_ERROR '; 
if(defined("E_DEPRECATED"))
	if($fcf_error_reporting & E_DEPRECATED) // 8192 // 
		$fcf_human_error_reporting.='& E_DEPRECATED ';
if(defined("E_USER_DEPRECATED"))
	if($fcf_error_reporting & E_USER_DEPRECATED) // 16384 // 
		$fcf_human_error_reporting.='& E_USER_DEPRECATED '; 
$fcf_human_error_reporting = substr($fcf_human_error_reporting,2);
fcf_print( "fcf_human_error_reporting = " . $fcf_human_error_reporting );
/**
 * WEBROOT_URL
 ****
 *
 * THE URL WHERE THIS SCRIPT RESIDES
 *
 */
$fcf_url_parse = parse_url("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
$fcf_base_url = $fcf_url_parse['scheme'] . "://";
$fcf_base_url .= $fcf_url_parse['host'];
$fcf_base_url .= substr($fcf_url_parse['path'], 0, strlen(substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/') + 1)));
define("FCF_BASE_URL",$fcf_base_url);
fcf_print("FCF_BASE_URL: " . FCF_BASE_URL);
/**
 * SYSTEM PATH
 ****
 * 
 * relative to the directory of this script,
 * where is the code igniter library?
 *
 */
if(ENVIRONMENT=="bertus") $system_path = 'system';
else exit('index.php will not set system_path for unknown env: '.ENVIRONMENT);
/**
 * FIND THE URI SEGMENT REFERRING TO THE APP KEY
 ****
 *
 * YOU CAN SET NO RELEVANT VARIABLES HERE,
 * YOUR WORK IS DONE....
 *
 */
$fcf_uri_str = $_SERVER['REQUEST_URI'];
fcf_print("fcf_uri_str : " . $fcf_uri_str);
$fcf_uri_segments = explode('/',$fcf_uri_str);
fcf_print("fcf_uri_segments : " . print_r($fcf_uri_segments,true));

$fcf_script_loc = $_SERVER['SCRIPT_FILENAME'];
fcf_print("fcf_script_loc : " . $_SERVER['SCRIPT_FILENAME']);
$fcf_script_dir = substr($fcf_script_loc,0,strrpos($fcf_script_loc,'/'));
fcf_print("fcf_script_dir : " . $fcf_script_dir);
$fcf_script_dir_name = substr($fcf_script_dir,strrpos($fcf_script_dir,'/')+1);
fcf_print("fcf_script_dir_name : " . $fcf_script_dir_name);
$fcf_result = false;
$fcf_max = count($fcf_uri_segments);
$fcf_i = 0;

while(!$fcf_result && $fcf_i < $fcf_max){
	fcf_print("compare segment: " . $fcf_uri_segments[0]);
	if($fcf_uri_segments[0] == $fcf_script_dir_name || $fcf_uri_segments[0] == "index.php"){
		$fcf_result = true;
	}
	array_shift($fcf_uri_segments);
	$fcf_i++;
}
/*
 * WE NOW SWITCH THE APPLICATION FOLDER BASED ON THE FIRST RELEVANT SEGMENT
 */
if($fcf_i == $fcf_max){
	fcf_print("switching to default application, no relevant uri segment was found");
	$fcf_switch_segment = $fcf_default_app_key;
}else{
	$fcf_switch_segment = $fcf_uri_segments[0];
}
fcf_print("try to switch on segment : " . $fcf_switch_segment);
if(!array_key_exists($fcf_switch_segment, $fcf_apps_available)){
	fcf_print("invalid application: " . $fcf_switch_segment);
	$fcf_switch_segment = $fcf_default_app_key;
	fcf_print("will use default: " . $fcf_switch_segment);
	if(!array_key_exists($fcf_switch_segment, $fcf_apps_available)){
		exit("your configuration is incorrect, your default application does not seem to be correct!");
	}
	define("FCF_APP_SEGMENT","");
}else{
	fcf_print("valid application: " . $fcf_switch_segment);
	define("FCF_APP_SEGMENT",$fcf_switch_segment);
}
$fcf_application_folder = $fcf_apps_available[$fcf_switch_segment];
$ci_application_folder = $fcf_apps_dir . "/" . $fcf_application_folder;
fcf_print("ci application_folder = " . $ci_application_folder);
define("FCF_APP", $fcf_application_folder);
/*

some remarks:

It is important to have the 404 override AND the default controller set 
in the config/routes file (in your app). Set them both your default controller.


*/


// --------------------------------------------------------------------
// END OF USER CONFIGURABLE SETTINGS.  DO NOT EDIT BELOW THIS LINE
// --------------------------------------------------------------------

/*
 * ---------------------------------------------------------------
 *  Resolve the system path for increased reliability
 * ---------------------------------------------------------------
 */

	// Set the current directory correctly for CLI requests
	if (defined('STDIN'))
	{
		chdir(dirname(__FILE__));
	}

	if (realpath($system_path) !== FALSE)
	{
		$system_path = realpath($system_path).'/';
	}

	// ensure there's a trailing slash
	$system_path = rtrim($system_path, '/').'/';

	// Is the system path correct?
	if ( ! is_dir($system_path))
	{
		exit("Your system folder path does not appear to be set correctly. Please open the following file and correct this: ".pathinfo(__FILE__, PATHINFO_BASENAME));
	}

/*
 * -------------------------------------------------------------------
 *  Now that we know the path, set the main path constants
 * -------------------------------------------------------------------
 */
	// The name of THIS file
	define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

	// The PHP file extension
	// this global constant is deprecated.
	define('EXT', '.php');

	// Path to the system folder
	define('BASEPATH', str_replace("\\", "/", $system_path));

	// Path to the front controller (this file)
	define('FCPATH', str_replace(SELF, '', __FILE__));

	// Name of the "system folder"
	define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));


	// The path to the "application" folder
	if (is_dir($ci_application_folder))
	{
		define('APPPATH', $ci_application_folder.'/');
	}
	else
	{
		if ( ! is_dir(BASEPATH.$ci_application_folder.'/'))
		{
			exit("Your application folder path does not appear to be set correctly. Please open the following file and correct this: ".SELF);
		}

		define('APPPATH', BASEPATH.$ci_application_folder.'/');
	}

/*
 * --------------------------------------------------------------------
 * LOAD THE BOOTSTRAP FILE
 * --------------------------------------------------------------------
 *
 * And away we go...
 *
 */
require_once BASEPATH.'core/CodeIgniter.php';

/* End of file index.php */
/* Location: ./index.php */