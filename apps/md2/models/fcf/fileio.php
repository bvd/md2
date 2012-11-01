<?php
class FileIO extends CI_Model {
	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}
	public function dirExistsOrCreateForWriteAccess($dir){
		if(!(is_dir($dir))){
			if (!mkdir($dir, 0, true)) {
				return false;
			}
		}
		if(!(is_writable($dir))){
			return false;
		}
		return true;
	}
}
?>