<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/3
 * Time: 11:45
 */

use Phalcon\Cache\Backend\Redis as CacheRedis;
use Phalcon\Cache\Frontend\Data as FrontData;
use Phalcon\Crypt;
use Phalcon\Db\Adapter\Pdo\Factory;
use Phalcon\Db\Profiler as DbProfiler;
use Phalcon\Flash\Direct as FlashDirect;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Logger\Adapter\File as FileLogger;
use Phalcon\Logger\Formatter\Line as FormatterLine;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Mvc\Model\MetaData\Redis as MetaDataRedis;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Session\Adapter\Redis as SessionRedis;
use app\Library\CryptModel;
use app\Library\SecurityDeep;

/**
 * Config.
 */
$di->setShared('config', function () {
    $config = include BASE_PATH . '/config/config.php';

    if (is_readable(BASE_PATH . '/config/config.dev.php')) {
        $override = include BASE_PATH . '/config/config.dev.php';
        $config->merge($override);
    }

    return $config;
});

/**
 * ACL.
 */
$di->setShared('aclResource', function () {
    return include BASE_PATH . '/config/frontBackAcl.php';
});

/**
 * Router.
 */
$di->setShared('router', function () {
    return require BASE_PATH . '/config/routes.php';
});

/**
 * Url.
 */
$di->setShared('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});

/**
 * View.
 */
$di->setShared('view', function () use ($config) {

    $view = new View();

    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines(
        [
            '.volt'  => function ($view, $di) use ($config) {
                $volt = new VoltEngine($view, $di);

                $volt->setOptions(
                    [
                        'compiledPath'      => $config->application->cacheDir . 'volt/',
                        'compiledSeparator' => '_'
                    ]
                );

                return $volt;
            },
        ]
    );

    return $view;
});

/**
 * Crypt.
 */
$di->set(
    "crypt",
    function () {
        $crypt = new Crypt();

        $crypt->setKey(CryptModel::POINT_KEY); // 使用你自己的key！

        return $crypt;
    }
);

/**
 * DB.
 */
$di->set('db', function () use ($config, $di) {
    // 事件管理器.
    $eventsManager = new EventsManager();

    // 分析底层sql性能，并记录日志.
    $profiler = new DbProfiler();
    $eventsManager->attach('db', function ($event, $connection) use ($profiler, $di) {
        if($event->getType() == 'beforeQuery'){
            // 在sql发送到数据库前启动分析.
            $profiler -> startProfile($connection -> getSQLStatement());
        }
        if($event->getType() == 'afterQuery'){
            // 在sql执行完毕后停止分析.
            $profiler->stopProfile();
            // 获取分析结果.
            $profile = $profiler->getLastProfile();
            $sql = $profile->getSQLStatement();
            $sql = str_replace(PHP_EOL, NULL, $sql);
            $params = $connection->getSqlVariables();
            (is_array($params) && count($params)) && $params = json_encode($params);
            $executeTime = $profile->getTotalElapsedSeconds();

            // 日志记录.
            $logger = $di->get('logger', ['filename' => date('Y-m-d') . '_SQL.log']);
            @$log = "{$sql} {$params} {$executeTime}";
            $logger->log($log);
        }
    });

    $connection = Factory::load(
        [
            'adapter'  => $config->database->adapter,
            'host'     => $config->database->host,
            'username' => $config->database->username,
            'password' => $config->database->password,
            'dbname'   => $config->database->dbname
        ]
    );

    // 注册监听事件.
    $connection->setEventsManager($eventsManager);
    return $connection;
});

/**
 * Manager.
 */
$di->set("modelsManager", function() use ($config) {
        $modelsManager = new ModelsManager();
        $modelsManager->setModelPrefix($config->database->prefix);
//        $modelsManager->registerNamespaceAlias('M', 'app\Models');
        return $modelsManager;
    }
);

/**
 * Cache.
 */
$di->set('cache', function () {
    $frontCache = new FrontData(
        [
            "lifetime" => 3600,
        ]
    );

    $cache = new CacheRedis(
        $frontCache,
        [
            'host'       => '127.0.0.1',
            'port'       => 6379,
            'auth'       => '',
            'persistent' => false,
            'prefix'     => '_ca_',
            'index'      => 1,
        ]
    );

    return $cache;
});

/**
 * Session.
 */
$di->set('session', function () {
    $session = new SessionRedis(
        [
            'uniqueId'   => 'govV3',
            'host'       => '127.0.0.1',
            'port'       => 6379,
            'auth'       => '',
            'persistent' => false,
            'lifetime'   => 3600,
            'prefix'     => '_se_',
            'index'      => 2,
        ]
    );

    $session->start();

    return $session;
});

/**
 * MetaData.
 */
$di->set('modelsMetadata', function () {
    return new MetaDataRedis(
        [
            'host'       => '127.0.0.1',
            'port'       => 6379,
            'auth'       => '',
            'persistent' => false,
            'statsKey'   => '_govV3',
            'lifetime'   => 3600,
            'prefix'     => '_mt_',
            'index'      => 3,
        ]
    );
});

/**
 * Dispatcher.
 */
$di->set('dispatcher', function () {
    // 事件管理器.
    $eventsManager = new EventsManager();

    $securityDeep = new SecurityDeep();

    $eventsManager->attach("dispatch", $securityDeep);

    $dispatcher = new Dispatcher();

    $dispatcher->setEventsManager($eventsManager);

    return $dispatcher;
});

/**
 * Flash.
 */
$di->set('flash', function () {
    return new FlashDirect([
        'error' => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ]);
});

/**
 * flashSession.
 */
$di->set("flashSession", function () {
    return new FlashSession([
        'error' => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ]);
});

/**
 * Logger.
 */
$di->set('logger', function ($filename = null, $format = null) use($config) {
    $format   =  $format ? : $config->get('logger')->format;
    $filename = trim($filename ? : $config->get('logger')->filename, '\\/');
    $path     = rtrim($config->get('logger')->path, '\\/') . DIRECTORY_SEPARATOR;

    $formatter = new FormatterLine($format, $config->get('logger')->date);
    $logger    = new FileLogger($path . $filename);

    $logger->setFormatter($formatter);
    $logger->setLogLevel($config->get('logger')->logLevel);

    return $logger;
});
