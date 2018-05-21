<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/3
 * Time: 11:35
 */

use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\Application;

//ini_set("display_errors", "On");
//error_reporting(E_ALL);

/**
 * 定义系统常量.
 */
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

try {
    /**
     * 获取配置文件.
     */
    $config = include BASE_PATH . '/config/config.php';

    /**
     * 初始化依赖注入.
     */
    $di = new FactoryDefault();

    /**
     * 引入注册服务.
     */
    include BASE_PATH . "/config/services.php";

    /**
     * 自动加载配置.
     */
    include BASE_PATH . '/config/loader.php';

    /**
     * 处理请求.
     */
    $application = new Application($di);

    echo $application->handle()->getContent();

} catch (Exception $e) {
    echo $e->getMessage(), '<br>';
    echo nl2br(htmlentities($e->getTraceAsString()));
}
