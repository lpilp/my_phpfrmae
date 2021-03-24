<?php
defined('HOMEBASE') OR exit('No direct script access allowed');
/**
 *  EXCEPTION类
 */
class RT_Exception extends Exception {
    protected $errfile = 'error.log';
    function __construct($message, $code){
        parent::__construct($message,$code);
        $this->code = $code;
        $this->message = $message;
    }
    
    public function __toString() {
        // write errorlog
        $file = $this->file;
        $trace = $this->getTraceAsString();        
        $writeMsg = $this->message." in ".$this->file."(".$this->getLine().")\n".$trace."\n-----------------\n";
        $file = LOGPATH.$this->errfile;
        file_put_contents($file,$writeMsg,FILE_APPEND);

        if (is_ajax_request()) {
            return $this->_format_ajax_return($this->code, $this->$message);
        }
        return $this->_format_web_return($this->message);
    }
    protected function _format_ajax_return($status,$msg){
        $array = array(
            'status'=>$status,
            'msg'=>$msg,
            'data'=>array()
        );
        return json_encode($array);
    }

    protected function _format_web_return($message){
        return "<h3>$message</h3>";
    }
}
/**
 * Undocumented class
 */
class RouterNotExistException extends RT_Exception{
    protected $code = 14041;
    function __construct($message){
        $this->message = "router class $message not exists";
        parent::__construct($this->message, $this->code);
    }
}
/**
 * Undocumented class
 */
class FileNotExistException extends RT_Exception {
    protected $code = 14043;
    function __construct($file){
        $files = explode(DIRECTORY_SEPARATOR, $file);
        $filename = array_pop($files);
        $this->message = "$filename not exists";
        parent::__construct($this->message, $this->code);
    } 
}


/**
 * Undocumented class
 */
class DbDriverNotExistException extends RT_Exception {
    protected $code = 14044;
    function __construct($message) {
        $this->message = "$message";
        parent::__construct($this->message, $this->code);
    }
}
//NoDbDsnException
class DbDsnNotExistException extends RT_Exception {
    protected $code = 14045;
    function __construct($message) {
        $this->message = "$message";
        parent::__construct($this->message, $this->code);
    }
}
/**
 * Undocumented class
 */
class ViewNotExistException extends RT_Exception {
    protected $code = 14046;
    function __construct($file){
        $files = explode(DIRECTORY_SEPARATOR, $file);
        $filename = array_pop($files);
        $this->message = "$filename not exists";
        parent::__construct($this->message, $this->code);
    } 
}
//PdoConnectionException
class PdoConnectionException extends RT_Exception {
    protected $code = 14051;
    function __construct($message) {
        $this->message = "$message";
        parent::__construct($this->message, $this->code);
    }
}

//SqlFormatException
class SqlFormatException extends RT_Exception {
    protected $code = 14052;
    function __construct($message) {
        $this->message = "$message";
        parent::__construct($this->message, $this->code);
    }
}

//DbConfigException
class DbConfigException extends RT_Exception {
    protected $code = 14054;
    function __construct($message) {
        $this->message = "$message";
        parent::__construct($this->message, $this->code);
    }
}

//SqliConnectionException
class SqliConnectionException extends RT_Exception {
    protected $code = 14061;
    function __construct() {
        $this->message = "mysqli connected error";
        parent::__construct($this->message, $this->code);
    }
}

//SqliQueryException
class SqliQueryException extends RT_Exception {
    protected $code = 14062;
    function __construct() {
        $this->message = "mysqli query";
        parent::__construct($this->message, $this->code);
    }
}

//SqliAddException
class SqliAddException extends RT_Exception {
    protected $code = 14063;
    function __construct() {
        $this->message = "mysqli add error";
        parent::__construct($this->message, $this->code);
    }
}

//SqliUpdateException
class SqliUpdateException extends RT_Exception {
    protected $code = 14064;
    function __construct() {
        $this->message = "mysqli update error";
        parent::__construct($this->message, $this->code);
    }
}
//SqliUpdateException
class SqliDelException extends RT_Exception {
    protected $code = 14064;
    function __construct() {
        $this->message = "mysqli delete error";
        parent::__construct($this->message, $this->code);
    }
}
