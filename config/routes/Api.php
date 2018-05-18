<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/5/18
 * Time: 14:51
 */

/**
 * 登录.
 */

// 登录.
$api->addPost('/login', [
    'controller' => 'login',
    'action'     => 'login',
]);

// 刷新页面.
$api->addPost('/refresh', [
    'controller' => 'login',
    'action'     => 'refresh',
]);

// 修改密码.
$api->addPost('/changepassword', [
    'controller' => 'login',
    'action'     => 'changePassword',
]);

// 获取我的留言.
$api->addPost('/getComments', [
    'controller' => 'login',
    'action'     => 'getComments',
]);

// 修改我的留言.
$api->addPost('/editcomments', [
    'controller' => 'login',
    'action'     => 'editComments',
]);

/**
 * 留言.
 */

// 留言详情.
$api->addPost('/commentone', [
    'controller' => 'comment',
    'action'     => 'commentOne',
]);

// 修改状态.
$api->addPost('/changecommentstatus', [
    'controller' => 'comment',
    'action'     => 'changeCommentStatus',
]);

/**
 * 事件.
 */

// 新增(修改)状态.
$api->addPost('/addstatus', [
    'controller' => 'status',
    'action'     => 'addStatus',
]);

// 删除状态.
$api->addPost('/delstatus', [
    'controller' => 'status',
    'action'     => 'delStatus',
]);

// 状态列表.
$api->addPost('/statuslist', [
    'controller' => 'status',
    'action'     => 'statusList',
]);

// 状态详情.
$api->addPost('/statusinfo', [
    'controller' => 'status',
    'action'     => 'statusInfo',
]);

/**
 * 统计.
 */

// 我的统计.
$api->add('/mycount', [
    'controller' => 'count',
    'action'     => 'myCount',
]);

// 留言统计详情（按分类查询具体信息）.
$api->addPost('/countcommentdetail', [
    'controller' => 'count',
    'action'     => 'countCommentDetail',
]);

// 状态统计详情（下属员工列表）.
$api->add('/countstatusdetail', [
    'controller' => 'count',
    'action'     => 'countStatusList',
]);

// 状态统计详情（按分类查询具体信息）.
$api->add('/countstatusdetailbystatus', [
    'controller' => 'count',
    'action'     => 'countStatusDetailByStatus',
]);

// 获取单个用户的计划列表和个人信息.
$api->addPost('/userstatuslist', [
    'controller' => 'count',
    'action'     => 'userStatusList',
]);

// 获取下拉列表.
$api->addPost('/getselectoptions', [
    'controller' => 'count',
    'action'     => 'getSelectOptions',
]);