<?php
class Fcf_xml_db extends CI_Model {

    private $_config_db_dir;
	
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
	}
    private function initContent(){
		$this->metafile = $this->_config_db_dir . 'frf';
		if(!file_exists($this->metafile)){
			$this->metadata = array();
			$this->datafile = $this->_config_db_dir . 'content';
			$fname = $this->datafile . '_' . $this->ftime;
			$this->metadata[] = $this->ftime;
			copy($this->datafile, $fname);
			file_put_contents($this->metafile,serialize($this->metadata));
		}else{
			$this->metadata = unserialize(file_get_contents($this->metafile));
		}
	}
    public function get_recent_data_file_name()
    {
        $fileName = 'content_';
        $fileNum = $this->metadata[sizeof($this->metadata)-1];
        return $this->_config_db_dir . $fileName . $fileNum;
    }
    public function get_recent_data(){
		return file_get_contents($this->get_recent_data_file_name());	
    }
	public function save($allContents){
		$this->datafile = $this->_config_db_dir . 'content_' . $this->ftime;
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
}
?>