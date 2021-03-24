<?php
defined('HOMEBASE') OR exit('No direct script access allowed');
function DB($params) {
    $driver = @$params['dbdriver'];
    if (empty($driver)) {
        $driver = 'pdo';
    }
    _load_driver($driver);
    $driver_class = "DB_Driver_".$driver;
    if(!class_exists($driver_class)){
        throw new DbDriverNotExistException("driver class $driver not exists");
    }
    return new $driver_class ($params);
}

function _load_driver($driver){
    $driver = ucfirst($driver);
    $driver_file = SYSPATH.'database'.DIRECTORY_SEPARATOR."driver".DIRECTORY_SEPARATOR.$driver.'.php';
    if(!file_exists($driver_file)){
        throw new DbDriverNotExistException("driver file $driver not exists");
    }
    include_once $driver_file;
}