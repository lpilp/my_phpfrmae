<?php
defined('HOMEBASE') OR exit('No direct script access allowed');
//注册autoload class

spl_autoload_register(function ($class) {
    if (class_exists($class)) {
        return true;
    }
    if (substr($class, 0, 3)=='RT_') { // base class
        $filename = substr($class, 3);
        $file = SYSPATH.'core'.DIRECTORY_SEPARATOR.$filename.'.php';
        if (file_exists($file)) {
            include $file;
        } else {
            throw new Exception("no class $class");
        }
    }
    return true;
});

function is_ajax_request() {
    return (@$_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest');
}

function is_cli_request() {
    return (php_sapi_name() === 'cli' or defined('STDIN'));
}




/**
 * 严格路由结构，防止一些不必要的注入
 *
 * @param string $name
 * @return boolean 
 */
function filter_url_input($name){

    return str_replace(array('<','>',"'",'"'," ","\t",'.'), '', $name);

}

function get_router($config){

    $defaultController = isset($config['defaultController']) ? $config['defaultController']: 'welcome';
    $defaultAction = isset($config['defaultAction']) ? $config['defaultAction']: 'index';

    
    $param = array();    
    $url = $_SERVER['REQUEST_URI'];
    $position = strpos($url, '?');
    $url = $position === false ? $url : substr($url, 0, $position);
    
    // 支持下index.php/{controller}/{action}
    $position = strpos($url, 'index.php');
    if ($position !== false) {
        $url = substr($url, $position + strlen('index.php'));
    }

    $url = trim($url, '/');
    
    if ($url) {
        $urlArray = explode('/', $url);
        $urlArray = array_filter($urlArray);
        
        $controllerName = filter_url_input(ucfirst($urlArray[0]));
        
        array_shift($urlArray);
        $actionName = $urlArray ? $urlArray[0] : $defaultAction;
        $actionName = filter_url_input($actionName);
        // 获取URL参数
        array_shift($urlArray);
        $param = $urlArray ? array_map('filter_url_input', $urlArray) : array();
    } else {
        $controllerName = $defaultController;
        $actionName = $defaultAction;
    }

    return array(
        $controllerName,
        $actionName,
        $param
    );    
}

function  get_config_db() {
    static $_config = array();
    // $_config = array();
    if(!empty($_config)) {
        return $_config;
    }
    $config_file = CONFIGPATH.'database.php';
    // echo $config_file."\n";
    if(!file_exists($config_file)){
        throw new FileNotExistException('database');
    }
    include_once($config_file);
    $_config = $db;
    return $_config;
}

function get_loader() {
    static $_load = null;
    if(empty($_load)){
        $_load = new RT_Loader();
        return $_load;
    }
    return $_load;
}


