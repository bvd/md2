<?php
class Errorreturningmodel extends CI_Model {

    public function __construct()
	{
		parent::__construct();
	}
    protected function error($code,$vars=null,$status="error"){
	require_once(APPPATH . "libraries/fcf/fcf_error.php");
	$error = new FCF_Error($code,$vars,$status);
	return $error->keyvals();
    }
}
?>