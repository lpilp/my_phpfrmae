<?php
defined('HOMEBASE') OR exit('No direct script access allowed');
/**
 *  class
 */
class RT_Loader {
    protected $_models = array();
    protected $_libs = array();
    protected $_database = array();
    protected $_configs = array();
    protected $_helps = array();
    public function model( $model ) {
        if ($model == '') {
            return;
        }        
        list( $path, $model ) = $this->_get_real_path($model);
        $model = ucfirst($model);
        if(isset($this->_models[$model] )){
            // echo "exists\n";
            return $this->_models[$model];
        }
        $file = MODPATH.$path.$model.".php";
        if(!file_exists($file)){
            throw new FileNotExistException("$file not exists");
        }
        require_once($file);
        $modeName = $model."Model";
        $ret = new $modeName();
        $this->_models[$model] = $ret;
        return $ret;
    }
    /**
     * Undocumented function
     *
     * @param string $name
     * @return void
     */
    public function library($name){
        if ($name == '') {
            return;
        }

        list( $path, $name ) = $this->_get_real_path($name);
     
        $name = ucfirst($name);
        if(isset($this->_libs[$name] )){
            return $this->_libs[$name];
        }
        $file = LIBPATH.$path.$name;
        if(!file_exists($file)){
            throw new Exception("$file not exists");
        }
        require_once($file);
        $ret = new $name();
        $this->_libs[$name] = $ret;
        return $ret;
    }
    /**
     * Undocumented function
     *
     * @param string $name
     * @return void
     */
    public function help($name){
        if ($name == '') {
            return;
        }
        list( $path, $name ) = $this->_get_real_path($name);
        $name = ucfirst($name);
        if(isset($this->_helps[$name])){
            return ;
        }
        $file = HELPPATH.$path.$name;
        if(!file_exists($file)){
            throw new Exception("$file not exists");
        }
        require_once($file);
        return true;
    }
    /**
     * Undocumented function
     *
     * @param string $name
     * @return void
     */
    public function config($name){
        if ($name == '') {
            return array();
        }
          
        list( $path, $name ) = $this->_get_real_path($name);

        $name = strtolower($name); 
        if(isset($this->_configs[$name] )){
            return $this->_configs[$name];
        }
        
        $file = CONPATH.$path.$name;
        if(!file_exists($file)){
            throw new Exception("$file not exists");
        }
        require_once($file);
        $this->_configs[$name] = $$name;
        return $$name;
    }
    public function database($active = '') {
        $db = get_config_db();
        if(empty($active) ){
            $active = $db['active_group'];
        } 
        if(empty($active)){
            $active = 'default';
        }
        if(!isset($db[$active])){
            throw new DbConfigException("no db $active group config");
        }
        $dbconfig = $db[$active];
        include_once(SYSPATH.'database'.DIRECTORY_SEPARATOR.'Db.php');
        return  DB($dbconfig);        
    }

    public function view($tpl, $data, $flag = false) {
        $viewfile = VIEWPATH.$tpl.".php";
        if( !file_exists($viewfile)) {
            throw new ViewNotExistException($tpl);
            return true;
        }
        
        extract($data);
        ob_start();
        include_once $viewfile;
        if ($flag === true) {
			$buffer = ob_get_contents();
			@ob_end_clean();
			return $buffer;
		}
        ob_end_flush();
        return true;
    }

    protected function _get_real_path($model){
        $path = '';
        if (($last_slash = strrpos($model, '/')) !== false) {
            $path = substr($model, 0, $last_slash + 1);
            $model = substr($model, $last_slash + 1);
        }
        return array($path, $model);
    }

}