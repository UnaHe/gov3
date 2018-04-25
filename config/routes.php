<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/3
 * Time: 14:56
 */

$router = new Phalcon\Mvc\Router(false);

/**
 * 首页相关.
 */

// 登录页面.
$router->addGet("/", [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'login',
    'action'     => 'index',
]);
$router->addGet("/admin/login", [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'login',
    'action'     => 'index',
]);
$router->addPost("/admin/login", [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'login',
    'action'     => 'login',
]);

// 主页.
$router->addGet("/admin/home", [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'home',
    'action'     => 'index',
]);

// 修改密码.
$router->addGet("/admin/changepwd", [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'home',
    'action'     => 'changePwd',
]);
$router->addPost("/admin/updatepwd", [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'home',
    'action'     => 'updatePwd',
]);
$router->addPost("/admin/authpwd", [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'home',
    'action'     => 'authPwd',
]);

// 退出登录.
$router->addGet("/admin/logout", [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'login',
    'action'     => 'logout',
]);

/**
 * 公共.
 */

// 图片上传.
$router->addPost('/admin/upload', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'common',
    'action'     => 'upload',
]);

// 得到单位列表通过项目id.
$router->addPost('/admin/ajaxGetOptionsByProject', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'common',
    'action'     => 'ajaxGetOptionsByProject',
]);

include 'routes/admin.php';
//include 'routes/User.php';

return $router;