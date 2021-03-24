<?php
defined('HOMEBASE') or exit('No direct script access allowed');
$db['active_group'] = 'default';
// $db['default'] = array(
//     'dsn'	=> '',
//     'hostname' => 'localhost',
//     'port'     =>3306,
//     'username' => 'root',
//     'password' => '',
//     'database' => 'myframe',
//     'dbdriver' => 'pdo', //连接驱动
//     'dbtype'   => 'mysql', // 数据库类型
//     'pconnect' => false, // 是否长链接， 如果前端较少可以使用长链，效率更高，如果前端太多，用短链，因为链接很快就会被消费光，使得后续无法链接到数据库
//     'db_debug' => (ENVIRONMENT !== 'production'),
//     'charset' => 'utf8',
//     'dbcollat' => 'utf8_general_ci',
//     'swap_pre' => '',
//     'encrypt' => false,
//     'compress' => false,
//     'stricton' => false,
//     'failover' => array(),
//     'save_queries' => true
// );
$db['default'] = array(
    'dsn'	=> '',
    'hostname' => 'localhost',
    'port'     =>3306,
    'username' => 'root',
    'password' => '',
    'database' => 'myframe',
    'dbdriver' => 'pdo',
    'dbtype'   => 'mysql',
    'pconnect' => false,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'charset' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => false,
    'compress' => false,
    'stricton' => false,
    'failover' => array(),
    'save_queries' => true
);
$db['mysqli'] = array(
    'dsn'	=> '',
    'hostname' => 'localhost',
    'port'     =>3306,
    'username' => 'root',
    'password' => '',
    'database' => 'myframe',
    'dbdriver' => 'mysqli',
    'dbtype'   => 'mysql',
    'pconnect' => false,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'charset' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => false,
    'compress' => false,
    'stricton' => false,
    'failover' => array(),
    'save_queries' => true
);
