<?php

define('ENVIRONMENT','development');
// 开启调试模式
define('APP_DEBUG', true);

define('HOMEBASE',dirname(__DIR__) .DIRECTORY_SEPARATOR);
define('BASEPATH',dirname(__DIR__) .DIRECTORY_SEPARATOR);
define('LOGPATH',HOMEBASE.'logs'.DIRECTORY_SEPARATOR);
//config
define('CONFIGPATH', HOMEBASE.'config'.DIRECTORY_SEPARATOR);
//system path
define('SYSPATH', HOMEBASE.'system'.DIRECTORY_SEPARATOR);

// Path to the front controller (this file) directory
define('FCPATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
// application path
define('APPPATH', HOMEBASE.'app'.DIRECTORY_SEPARATOR);
// view path
define('VIEWPATH', APPPATH.'views'.DIRECTORY_SEPARATOR);
// controller path
define('CTRLPATH', APPPATH.'controllers'.DIRECTORY_SEPARATOR);
// model path
define('MODPATH', APPPATH.'model'.DIRECTORY_SEPARATOR);

// help path
define('HELPPATH', APPPATH.'help'.DIRECTORY_SEPARATOR);
//lib
define('LIBPATH', APPPATH.'library'.DIRECTORY_SEPARATOR);

include SYSPATH.'core'.DIRECTORY_SEPARATOR."App.php";
// 加载配置文件

/* // 实例化框架类
// (new fastphp\Fastphp($config))->run();


$model = "a/b/c/d/e";
if (($last_slash = strrpos($model, '/')) !== FALSE)
{
    // The path is in front of the last slash
    $path = substr($model, 0, $last_slash + 1);

    // And the model name behind it
    $model = substr($model, $last_slash + 1);
}

var_dump($path, $model); */