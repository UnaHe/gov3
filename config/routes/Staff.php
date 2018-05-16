<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/5/15
 * Time: 13:39
 */

/**
 * 登录.
 */

// 登录.
$staff->add('/login', [
    'controller' => 'login',
    'action'     => 'login',
]);

// 刷新页面.
$staff->add('/', [
    'controller' => 'login',
    'action'     => 'refresh',
]);
$staff->add('/index', [
    'controller' => 'login',
    'action'     => 'refresh',
]);
$staff->add('/refresh', [
    'controller' => 'login',
    'action'     => 'refresh',
]);

// 设置.
$staff->addGet('/setting', [
    'controller' => 'login',
    'action'     => 'setting',
]);

// 修改我的留言.
$staff->add('/edit_comments', [
    'controller' => 'login',
    'action'     => 'editComments',
]);

// 修改密码.
$staff->add('/changepassword', [
    'controller' => 'login',
    'action'     => 'changePassword',
]);

// 登出.
$staff->add('/loginout', [
    'controller' => 'login',
    'action'     => 'logout',
]);

/**
 * 统计.
 */

// 统计.
$staff->addGet('/count', [
    'controller' => 'count',
    'action'     => 'index',
]);

// 我的统计.
$staff->addPost('/mycount', [
    'controller' => 'count',
    'action'     => 'myCount',
]);