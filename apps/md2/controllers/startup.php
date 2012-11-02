<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . "controllers/fcf/sessioncontroller.php");

class Startup extends Sessioncontroller {
	public function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		/**
		 *
		 * IF DATA ARE SAVED, SAVE
		 *
		 * AND RETURN
		 *
		 */
		$this->load->model("fcf/Fcf_xml_db");
		if(isset($_POST['save'])){
			if(!(isset($_SESSION['username']))){
				log_message('debug','cannot save without login');
				echo "fail";
				return;
			}
			$this->Fcf_xml_db->save($_POST['save']);
			echo "success";
			return;
		}
		
		/**
		 *
		 * LOAD THE OTHER MODELS
		 *
		 */
		$this->load->model("fcf/Fcf_robots");
		$this->load->model("fcf/Fcf_proxytweet_db");
		
		/**
		 *
		 * COLLECT ALL THE VIEW DATA
		 *
		 */
		$viewData = array();
		$viewData["title"] = $this->config->item("title");
		$viewData["base_url"] = $this->config->item("base_url");
		$viewData["default_page"] = $this->config->item("default_page");
		$viewData['cb_version'] = $this->config->item("cb_version");
		$viewData['analytics_account'] = $this->config->item("analytics_account");
		$viewData['analytics_enabled'] = $this->config->item("analytics_enabled");
		$viewData['site_data'] = $this->Fcf_xml_db->get_recent_data();
		$viewData['proxytweet_data'] = $this->Fcf_proxytweet_db->get_recent_data();
		$viewData['session'] = json_encode($this->Session->get());
		$viewData["links"] = $this->Fcf_robots->getLinks();
		$viewData["content"] = $this->Fcf_robots->getContent();
		$viewData["css_url"] = $this->config->item("css_url");
		$viewData["swf_url"] = $this->config->item("swf_url");
		$viewData["js_url"] = $this->config->item("js_url");
		$viewData["js_tags"] = $this->Carabinerwrapper->jsTagsForModule("startup");
		$viewData["escaped_fragment"] = isset($_GET['_escaped_fragment_']) ? $_GET['_escaped_fragment_'] : false;
		/**
		 *
		 * IF THIS IS A SEARCH ENGINE, LOAD SEO VIEW
		 *
		 * AND RETURN
		 *
		 */
		if(isset($_GET['_escaped_fragment_'])){
			$this->load->view("google", $viewData);
			return;
		}
		/**
		 *
		 * ADD THE BODY STRING TO THE VIEW
		 *
		 */
		$bodyContent = $this->load->view("body/body", $viewData, true);
		$viewData["bodyContent"] = $bodyContent;
		$this->load->view("startup", $viewData);
	}
}