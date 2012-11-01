<?php
class FCF_Error{
    public static $errors = array(
        // common - files and directories
        "1349171541"        =>  "cannot open directory %s",
        "1349175992"        =>  "cannot delete %s",
        "1349176466"        =>  "cannot create symlink %s",
        // model - keyValueModel
        "1349114039"        =>  "%s is not an allowed value for %s",
        "1349114731"        =>  "not allowed to set a value for %s",
        "1349118935"        =>  "no enumeration provided for %s",
        "1349115620"        =>  "not allowed to get a value for %s"
    );
    public $message;
    public $code;
    public $status;
    public $messageTemplate;
    public $messageArgs;
    function __construct($code, $vars = null, $status = "error") {
        if(!(array_key_exists($code,self::$errors))) die("unknown error! code: " . $code . " and vars " . print_r($vars,true));
        $this->code = $code;
        $this->status = $status;
        $this->messageTemplate = self::$errors[$code];
        $this->messageArgs = $vars;
        $this->message = vsprintf(self::$errors[$code], $vars);
    }
    function keyvals(){
        $r = new stdClass();
        $r->status = $this->status;
        $r->code = $this->code;
        $r->message = $this->message;
        $r->messageTemplate = $this->messageTemplate;
        $r->messageArgs = $this->messageArgs;
        return $r;
    }
}
?>