<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/3
 * Time: 11:51
 */

use Phalcon\Config;
use Phalcon\Logger;

return new Config([
    'database' => [
        'adapter'   => 'Postgresql',
        'host'      => '127.0.0.1',
        'username'  => 'postgres',
        'password'  => '123456',
        'dbname'    => 'postgres',
        'prefix'    => 'n_z_'
    ],
    'application' => [
        'controllersDir' => APP_PATH . '/Controllers/',
        'libraryDir'     => APP_PATH . '/Library/',
        'modelsDir'      => APP_PATH . '/Models/',
        'viewsDir'       => APP_PATH . '/Views/',
        'cacheDir'       => BASE_PATH . '/cache/',
        'baseUri'        => '/',
    ],
    'logger' => [
        'path'     => BASE_PATH . '/logs/',
        'format'   => '%date% [%type%] %message%',
        'date'     => 'Y-m-d H:i:s',
        'logLevel' => Logger::DEBUG,
        'filename' => date('Y-m-d') . '.log',
    ],
    'constants' => [
        'upload'                => BASE_PATH . '/public/upload/',
        'upload_url'            => 'http://' . $_SERVER['SERVER_NAME'] . '/upload/',
        'upload_path'           => BASE_PATH . '/public/upload/',
        'default_staff_img'     => 'http://' . $_SERVER['SERVER_NAME'] . '/admin/style/img/user_default.jpg',
        'staff_remember_token'  => 'staff_remember_token',
    ],
    'APP_URL' => 'https://gov2.signp.cn',
]);