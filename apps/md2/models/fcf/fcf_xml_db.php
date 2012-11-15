<?php
class Fcf_xml_db extends CI_Model {

    private $_config_db_dir;
	
	private $_init_data_file;
	private $_xml;
	private $_iter_current_link;
	private $_content_for_url;
	
	private $ftime;
	private $metafile;
	private $metadata;
	private $datafile;
	private $urlSegment;
	private $links;
	private $ci;

	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
		$this->ftime = time();
		$this->_config_db_dir = $this->config->item("db_dir");
		$this->initContent();
		$this->ci = get_instance();
		$this->_init_data_file = $this->_config_db_dir . 'content';
	}
    private function initContent(){
		$this->metafile = $this->_config_db_dir . 'frf';
		if(!file_exists($this->metafile)){
			$this->metadata = array();
			$this->datafile = $this->_config_db_dir . 'content.xml';
			$fname = $this->datafile . '_' . $this->ftime;
			$this->metadata[] = $this->ftime;
			copy($this->datafile, $fname);
			file_put_contents($this->metafile,serialize($this->metadata));
		}else{
			$this->metadata = unserialize(file_get_contents($this->metafile));
		}
	}
	public function get_dimensions_for_file_field_type($type){
		if(!($this->_xml)) $this->_load_xml();
		return $this->_xml->db->fieldTypes->visuals->{$type};
	}
    public function get_recent_data_file_name()
    {
        $fileName = 'content_';
        $fileNum = $this->metadata[sizeof($this->metadata)-1];
        return $this->_config_db_dir . $fileName . $fileNum . ".xml";
    }
    public function get_recent_data(){
		$str = file_get_contents($this->get_recent_data_file_name());
		if(substr($str,0,2) == "<?"){
			$str = substr($str,strpos($str,"?>")+2);
		}
		return $str;
    }
	public function save($allContents){
		$this->datafile = $this->_config_db_dir . 'content_' . $this->ftime . ".xml";
		log_message("debug","fcf_xml_db->save - to file: " . $this->datafile);
		$this->metadata[] = $this->ftime;
		if(false === file_put_contents($this->datafile,$allContents)){
			log_message("error","could not save to " . $this->datafile);
		}
		if(false === file_put_contents($this->metafile,serialize($this->metadata))){
			log_message("error","could not save to " . $this->metafile);
		}
		log_message("debug","fcf_xml_db->save() - stored data");
		if(!(property_exists($this->ci, 'Fcf_robots'))){
			$this->ci->load->model("fcf/Fcf_robots");
		}
		log_message("debug","fcf_xml_db->save() - model Fcf_robots loaded");
		$this->ci->Fcf_robots->refreshHtmlContentCache();
	}
	public function db_insert($table,$fields){
		log_message("debug","fcf_xml_db->db_insert");
		if(!($this->_xml)) $this->_load_xml();
		if(!property_exists($this->_xml->db, $table)){
			log_message("error","fcf_xml_db->db_insert no table " . $table);
			return -1;
		}
		$dbItemId = count($this->_xml->db->$table->children());
		$newNode = $this->_xml->db->$table->addChild($table);
		$newNode->addAttribute("id",$dbItemId);
		foreach($fields as $i => $field){
			$fieldNode = $newNode->addChild($field["field"]);
			$fieldNode->{0} = $field["fieldContent"];
			$fieldNode->addAttribute("fieldType",$field["fieldType"]);
		}
		return $dbItemId;
	}
	public function page_list_insert($pagePath,$listType,$position,$id){
		log_message("debug","fcf_xml_db->page_list_insert");
		if(!($this->_xml)) $this->_load_xml();
		log_message("debug","fcf_xml_db->page_list_insert will now retrieve " . $pagePath);
		$pageRef = $this->_xml->site;
		$pathSplit = explode("-",$pagePath);
		foreach($pathSplit as $segment){
			if(!(property_exists($pageRef,"children"))){
				log_message("error","fcf_xml_db->page_list_insert - xml element " . $pageRef->getName() . 
					" has no element children");
				return false;
			}
			if(!(property_exists($pageRef->children, $segment))){
				log_message("error","fcf_xml_db->page_list_insert - xml element " . 
					$pageRef->children->getName() . " has no element " . $segment);
				return false;
			}
			$pageRef = $pageRef->children->$segment;
		}
		log_message("debug","fcf_xml_db->page_list_insert successfully retrieved page element");
		$list = null;
		$prChildren = $pageRef->children();
		log_message("debug","found " . count($prChildren) . " children");
		foreach($prChildren as $pageChild){
			$pcName = $pageChild->getName();
			log_message("debug","pageChild " . $pcName);
			if($pcName == "order"){
				$attr = $pageChild->attributes();
				if($attr["type"] == $listType){
					$list = $pageChild;
					break;
				}
			}
		}
		if(!$list){
			log_message("error","page " . $pagePath . " does not seem to contain order element typed " . $listType);
			return false;
		}
		if($position == "page_list_insert_position_first"){
			$newElem = $list->prependChild("item");
			$newElem->addAttribute("id",$id);
			return true;
		}
		log_message("error","your position designation is not supported: " . $position);
		return false;
	}
	private function _load_xml(){
		$file = $this->get_recent_data_file_name();
		require_once(APPPATH . "libraries/lesssimplexml.php");
		$xml = simplexml_load_file($file,"LessSimpleXml");
		if(false === $xml){
			log_message('error','fcf_xml_db->_load_xml : xml load error for file ' . $file);
		}else{
			$this->_xml = $xml;
		}
	}
	public function db_store(){
		if(!($this->_xml)){
			log_message("debug","fcf_xml_db->store_xml - nothing to store");
			return true;
		}
		$file = $this->get_recent_data_file_name();
		$status = $this->_xml->asXML($file);
		if(false === $status){
			log_message('error','fcf_xml_db->db_store : xml store error for file ' . $file);
		}else{
			log_message('debug','fcf_xml_db->db_store : success for file ' . $file);
			unset($this->_xml);
		}
		return $status;
	}
	public function getContentForLinks(){
		if(!($this->_xml)) $this->_load_xml();
		$this->_iter_current_link = "";
		$this->_content_for_url = array();
		
		$this->_retrieve_content($this->_xml->site);
		return $this->_content_for_url;
	}
	
	private function _retrieve_content($elem){
		foreach($elem->children() as $child) {
			if(!($child->getName() == 'children')){
				$this->_iter_current_link = $this->_iter_current_link . ((strlen($this->_iter_current_link)>0)?'-':'') . $child->getName();
				if($child->attributes()->view){
					$this->_content_for_url[$this->_iter_current_link] = "";
					if($child->fields){
						foreach($child->fields->children() as $fld){
							$this->_content_for_url[$this->_iter_current_link]  .= '<p>' . $fld . '</p>';
						}
					}
				}
				$this->_retrieve_content($child);
				$this->_iter_current_link = substr($this->_iter_current_link, 0, ((strlen($this->_iter_current_link)==strlen($child->getName()))?0:strlen($this->_iter_current_link) - strlen( '-' . $child->getName() )));
			}else{
				$this->_retrieve_content($child);
			}
		}
	}
}
?>