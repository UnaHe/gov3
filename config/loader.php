<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/3
 * Time: 11:45
 */

use Phalcon\Loader;

$loader = new Loader();

/**
 * 注册命名空间.
 */
$loader->registerNamespaces([
    'app\Models'      => $config->application->modelsDir,
    'app\Controllers' => $config->application->controllersDir,
    'app\Library'     => $config->application->libraryDir,
])->register();