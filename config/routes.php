<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/3
 * Time: 14:56
 */
use Phalcon\Mvc\Router\Group as RouterGroup;
use Phalcon\Mvc\Router;

// 默认事件.
$router = new Router(false);

/**
 * Admin路由组.
 */
$admin = new RouterGroup([
    'namespace'  => 'app\Controllers\Admin',
]);

// URL前缀.
$admin->setPrefix('/admin');

/**
 * 后台首页相关.
 */

// 登录页面.
$admin->addGet("/login", [
    'controller' => 'login',
    'action'     => 'index',
]);
$admin->addPost("/login", [
    'controller' => 'login',
    'action'     => 'login',
]);

// 主页.
$admin->addGet("/home", [
    'controller' => 'home',
    'action'     => 'index',
]);

// 修改密码.
$admin->addGet("/changepwd", [
    'controller' => 'home',
    'action'     => 'changePwd',
]);
$admin->addPost("/updatepwd", [
    'controller' => 'home',
    'action'     => 'updatePwd',
]);
$admin->addPost("/authpwd", [
    'controller' => 'home',
    'action'     => 'authPwd',
]);

// 退出登录.
$admin->addGet("/logout", [
    'controller' => 'login',
    'action'     => 'logout',
]);

/**
 * 公共.
 */

// 图片上传.
$admin->addPost('/upload', [
    'controller' => 'common',
    'action'     => 'upload',
]);

// 得到单位列表通过项目id.
$admin->addPost('/ajaxGetOptionsByProject', [
    'controller' => 'common',
    'action'     => 'ajaxGetOptionsByProject',
]);

/**
 * Errors.
 */

// 401.
$admin->addGet("/errors/show401", [
    'controller' => 'errors',
    'action'     => 'show401',
]);

// 404.
$admin->addGet("/errors/show404", [
    'controller' => 'errors',
    'action'     => 'show404',
]);

// 引入路由文件.
include 'routes/Admin.php';

// 注册路由组.
$router->mount($admin);unset($admin);

/**
 * Home路由组.
 */
$home = new RouterGroup([
    'namespace'  => 'app\Controllers\Home',
]);

// 引入路由文件.
include 'routes/Home.php';

// 注册路由组.
$router->mount($home);unset($home);

/**
 * Staff路由组.
 */
$staff = new RouterGroup([
    'namespace'  => 'app\Controllers\Api',
]);

// URL前缀.
$staff->setPrefix('/staff');

// 引入路由文件.
include 'routes/Staff.php';

// 注册路由组.
$router->mount($staff);unset($staff);

/**
 * Api路由组.
 */
$api = new RouterGroup([
    'namespace'  => 'app\Controllers\Api',
]);

// URL前缀.
$api->setPrefix('/apis');

// 引入路由文件.
include 'routes/Api.php';

// 注册路由组.
$router->mount($api);unset($api);

/**
 * 页面未找到.
 */
$router->notFound([
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'errors',
    'action'     => 'show404',
]);

// 后台登录页面.
$router->addGet("/", [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'login',
    'action'     => 'index',
]);

return $router;