<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/3
 * Time: 11:45
 */

use Phalcon\Cache\Backend\Redis as CacheRedis;
use Phalcon\Cache\Frontend\Data as FrontData;
use Phalcon\Db\Adapter\Pdo\Factory;
use Phalcon\Flash\Direct as FlashDirect;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Logger\Adapter\File as FileLogger;
use Phalcon\Logger\Formatter\Line as FormatterLine;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Mvc\Model\MetaData\Redis as MetaDataRedis;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Session\Adapter\Redis as SessionRedis;

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
            // Generate Template files uses PHP itself as the template engine
            '.phtml' => 'Phalcon\Mvc\View\Engine\Php',
        ]
    );

    return $view;
});

/**
 * DB.
 */
$di->set('db', function () use ($config) {
    return Factory::load(
        [
            'adapter'  => $config->database->adapter,
            'host'     => $config->database->host,
            'username' => $config->database->username,
            'password' => $config->database->password,
            'dbname'   => $config->database->dbname
        ]
    );
});

/**
 * Manager.
 */
$di->set("modelsManager", function() use ($config) {
        $modelsManager = new ModelsManager();
        $modelsManager->setModelPrefix($config->database->prefix);
        $modelsManager->registerNamespaceAlias('m', 'app\Models');
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
            'prefix'     => 'ca',
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
            'lifetime'   => 7200,
            'prefix'     => 'se',
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
            'statsKey'   => '_PHCM_MM',
            'lifetime'   => 3600,
            'prefix'     => 'mt',
            'index'      => 3,
        ]
    );
});

/**
 * Dispatcher.
 */
$di->set('dispatcher', function () {
    $dispatcher = new Dispatcher();
    $dispatcher->setDefaultNamespace('app\Controllers');

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

    $format   = $format ?: $config->get('logger')->format;
    $filename = trim($filename ?: $config->get('logger')->filename, '\\/');
    $path     = rtrim($config->get('logger')->path, '\\/') . DIRECTORY_SEPARATOR;

    $formatter = new FormatterLine($format, $config->get('logger')->date);
    $logger    = new FileLogger($path . $filename);

    $logger->setFormatter($formatter);
    $logger->setLogLevel($config->get('logger')->logLevel);

    return $logger;
});
