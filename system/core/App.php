<?php
defined('HOMEBASE') OR exit('No direct script access allowed');
include 'Common.php';
include 'Loader.php';
include 'Exceptions.php';

$config = include CONFIGPATH."config.php";

try {
    list($className, $actionName, $param) = get_router($config);
    $classFile = CTRLPATH.$className.'.php';
    if (file_exists($classFile)) {
        include $classFile;
    } else {
        throw new FileNotExistException($className);
    }
    if (!class_exists($className)) {
        throw new RouterNotExistException($className.".");
    }
    if (!method_exists($className, $actionName)) {
        throw new RouterNotExistException($actionName);
    }
    $dispatch = new $className($className, $actionName);
    call_user_func_array(array($dispatch,$actionName), $param);
} catch (Exception $e) {
    die($e);
}
