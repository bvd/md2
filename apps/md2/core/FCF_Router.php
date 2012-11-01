<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Router Class
 *
 * Parses URIs and determines routing
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @author		ExpressionEngine Dev Team
 * @category	Libraries
 * @link		http://codeigniter.com/user_guide/general/routing.html
 */
class FCF_Router extends CI_Router {
	function _set_request($segments = array())
	{
		log_message("debug","FCF_Router _set_request(" . json_encode($segments) . ")");
		log_message("debug","FCF_APP_SEGMENT: " . FCF_APP_SEGMENT);
		if(FCF_APP_SEGMENT != "" && $this->uri->segment(0) == $segments[0] && $segments[0] == FCF_APP_SEGMENT){
			array_shift($segments);
		}
		log_message("debug","FCF_Router _set_request modified segments array: " . json_encode($segments));
		parent::_set_request($segments);
	}
}
// END Router Class

/* End of file Router.php */
/* Location: ./system/core/Router.php */