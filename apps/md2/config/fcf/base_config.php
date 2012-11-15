<?php
/**
 *
 * THE BASE DIRECTORIES ARE DEFINED IN THE INDEX.PHP FILE!!!
 * 
 * DO NOT EDIT THIS FILE FOR APPLICATION SPECIFIC SETTINGS.
 * USE YOUR APPLICATION CONFIG FILE TO OVERRIDE THESE.
 */
// prefix for overriding CI classes
$config['subclass_prefix'] = 'FCF_';
// the site's base URL, automatically detected. Use the print setting in the index.php file if it is not OK.
$config['base_url']					= FCF_BASE_URL;

// this is a list of directories that will be created by the install/directories function if they do not exist
$config['fcf_install_dirs']			= array();
$config['fcf_install_dirs'][]		= FCF_LOG_DIR;
$config['fcf_install_dirs'][]		= FCF_CACHE_DIR;
$config['fcf_install_dirs'][]		= FCF_PRIV_CACHE_DIR;
$config['fcf_install_dirs'][]		= FCF_DATA_DIR;
$config['fcf_install_dirs'][]		= FCF_PRIV_DATA_DIR;
$config['fcf_install_dirs'][]		= FCF_TMP_DATA_DIR;

// system log files (duh):
$config['log_path'] 				= FCPATH . FCF_LOG_DIR 			. "/" . FCF_APP . "/log/";
// system cache files (world readable) -- cache files may be deleted without problems, they can be regenerated if missing:
$config['cache_dir'] 				= FCPATH 				. FCF_CACHE_DIR 		. "/" . FCF_APP . "/cache/";
$config['cache_url'] 				= $config['base_url'] 	. FCF_CACHE_DIR 		. "/" . FCF_APP . "/cache/";
// system data files, world readable -- unique data / assets:
$config['data_dir'] 				= FCPATH 				. FCF_DATA_DIR 		. "/" . FCF_APP . "/data/";
$config['data_url']					= $config['base_url'] 	. FCF_DATA_DIR 		. "/" . FCF_APP . "/data/";
// private cache files (script readable) -- cache files may be deleted without problems, they can be regenerated if missing:
$config['priv_cache_path'] 			= FCPATH . FCF_PRIV_CACHE_DIR 	. "/" . FCF_APP . "/priv_cache/";
// private data files (script readable) -- unique and private data
$config['priv_data_dir'] 			= FCPATH . FCF_PRIV_DATA_DIR 	. "/" . FCF_APP . "/priv_data/";
// tmp data dir (for example temp storage for uploaded files) to be cleared regularly
$config['tmp_data_dir']				= FCPATH 				. FCF_TMP_DATA_DIR 	. "/" . FCF_APP . "/tmp_data/";
$config['tmp_data_url']				= $config['base_url']	. FCF_TMP_DATA_DIR 	. "/" . FCF_APP . "/tmp_data/";


$config['fcf_install_dirs'][]		= $config['log_path'];
$config['fcf_install_dirs'][]		= $config['cache_dir'];
$config['fcf_install_dirs'][]		= $config['data_dir'];
$config['fcf_install_dirs'][]		= $config['priv_cache_path'];
$config['fcf_install_dirs'][]		= $config['priv_data_dir'];
$config['fcf_install_dirs'][]		= $config['tmp_data_dir'];

// uploaded files
$config['tmp_up_dir']				= $config['tmp_data_dir'] . "up/";
$config['tmp_up_url']				= $config['tmp_data_url'] . "up/";

// to store serialized but unique data in files (e.g. XML, json, serialize):
$config['db_dir']					= $config['priv_data_dir'] . "db/";
// to store cached html snapshots of ajax-generated pages for robots
$config['robots_snapshots_dir']		= $config['cache_dir'] . 'robots/html/';
// to store crawlable URLS that lead to regenerated pages the same as the snapshots
$config['robots_links_dir']			= $config['cache_dir'] . 'robots/links/';

$config['fcf_install_dirs'][]		= $config['tmp_up_dir'];
$config['fcf_install_dirs'][]		= $config['db_dir'];
$config['fcf_install_dirs'][]		= $config['robots_snapshots_dir'];
$config['fcf_install_dirs'][]		= $config['robots_links_dir'];

// javascript directory
$config['js_dir']					= $config['data_dir'] . "js/";
$config['js_url']					= $config['data_url'] . "js/";
// css directory
$config['css_dir']					= $config['data_dir'] 		. "css/";
$config['css_url']					= $config['data_url'] 		. "css/";
// swf directory
$config['swf_dir']					= $config['data_dir'] 		. "swf/";
$config['swf_url']					= $config['data_url'] 		. "swf/";
// user data
$config['user_dir']					= $config['data_dir'] 		. "user/";
$config['user_url']					= $config['data_url'] 		. "user/";
$config['uploaded_dir']				= $config['user_dir']		. "uploaded/";
$config['uploaded_url']				= $config['user_url'] 		. "uploaded/";
// js cache dir
$config['js_cache_dir']				= $config['cache_dir'] . "js/";

$config['fcf_install_dirs'][]		= $config['js_dir'];
$config['fcf_install_dirs'][]		= $config['css_dir'];
$config['fcf_install_dirs'][]		= $config['swf_dir'];
$config['fcf_install_dirs'][]		= $config['js_cache_dir'];
$config['fcf_install_dirs'][]		= $config['user_dir'];
$config['fcf_install_dirs'][]		= $config['uploaded_dir'];

// javascript cacher and compressor
$config['carabiner'] 				= array(
										'script_dir' => substr($config['js_dir'], strlen(FCPATH)), 
										'style_dir'  => substr($config['css_dir'], strlen(FCPATH)), 
										'cache_dir'  => substr($config['js_cache_dir'], strlen(FCPATH)), 
										'base_uri'   => $config['base_url'],
										'combine'    => TRUE,
										'dev'        => FALSE
									);
// modules
$config['js_modules'] = array();
$config['js_modules']['startup'] = array();
$config['js_modules']['startup'][] = array("jquery-1.8.2.js");
$config['js_modules']['startup'][] = array("jquery.address.js");
$config['js_modules']['startup'][] = array("flash_detect.js");
$config['js_modules']['startup'][] = array("swfobject.js");
$config['js_modules']['startup'][] = array("fcf_startup.js");
$config['js_modules']['startup'][] = array("json2.js");
$config['js_modules']['startup'][] = array("jsrender.js");
$config['js_modules']['startup'][] = array("recaptchaAPI.js");
$config['js_modules']['startup'][] = array("jquery.ui.widget.js");
$config['js_modules']['startup'][] = array("jquery.iframe-transport.js");
$config['js_modules']['startup'][] = array("jquery.fileupload.js");
$config['js_modules']['startup'][] = array("fcf_form.js");
$config['js_modules']['startup'][] = array("sha256.js");
$config['js_modules']['startup'][] = array("fcf_cms.js");
$config['js_modules']['startup'][] = array("jquery-ui-1.9.1.custom.min.js");
$config['js_modules']['startup'][] = array("soundmanager2-jsmin.js");									
									
// important for availability of $_GET within CI
$config['uri_protocol']	= 'PATH_INFO';

//
//
//
//
//
// here's the (for our purposes) less usefull settings:
///////////////////////////////////////////////////////
$config['log_threshold'] = 0;
$config['index_page'] = '';
$config['uri_protocol']	= 'AUTO';
$config['url_suffix'] = '';
$config['language']	= 'english';
$config['charset'] = 'UTF-8';
$config['enable_hooks'] = FALSE;
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';
$config['allow_get_array']		= TRUE;
$config['enable_query_strings'] = FALSE;
$config['controller_trigger']	= 'c';
$config['function_trigger']		= 'm';
$config['directory_trigger']	= 'd'; // experimental not currently in use
$config['log_date_format'] = 'Y-m-d H:i:s';
$config['encryption_key'] = '';
$config['sess_cookie_name']		= 'ci_session';
$config['sess_expiration']		= 7200;
$config['sess_expire_on_close']	= FALSE;
$config['sess_encrypt_cookie']	= FALSE;
$config['sess_use_database']	= FALSE;
$config['sess_table_name']		= 'ci_sessions';
$config['sess_match_ip']		= FALSE;
$config['sess_match_useragent']	= TRUE;
$config['sess_time_to_update']	= 300;
$config['cookie_prefix']	= "";
$config['cookie_domain']	= "";
$config['cookie_path']		= "/";
$config['cookie_secure']	= FALSE;
$config['global_xss_filtering'] = FALSE;
$config['csrf_protection'] = FALSE;
$config['csrf_token_name'] = 'csrf_test_name';
$config['csrf_cookie_name'] = 'csrf_cookie_name';
$config['csrf_expire'] = 7200;
$config['compress_output'] = FALSE;
$config['time_reference'] = 'local';
$config['rewrite_short_tags'] = FALSE;
$config['proxy_ips'] = '';
?>