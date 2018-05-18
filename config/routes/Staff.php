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

// 状态统计详情（下属员工列表）.
$staff->add('/countstatusdetail', [
    'controller' => 'count',
    'action'     => 'countStatusList',
]);

// 状态统计详情（按分类查询具体信息）.
$staff->addPost('/countstatusdetailbystatus', [
    'controller' => 'count',
    'action'     => 'countStatusDetailByStatus',
]);

// 获取单个用户的计划列表和个人信息.
$staff->add('/userstatuslist', [
    'controller' => 'count',
    'action'     => 'userStatusList',
]);

// 留言统计详情（按分类查询具体信息）.
$staff->add('/countcommentdetail', [
    'controller' => 'count',
    'action'     => 'countCommentDetail',
]);

// 获取下拉列表.
$staff->add('/getselectoptions', [
    'controller' => 'count',
    'action'     => 'getSelectOptions',
]);

/**
 * 留言.
 */

// 留言详情.
$staff->addGet('/commentone', [
    'controller' => 'comment',
    'action'     => 'commentOne',
]);

// 修改状态.
$staff->addPost('/changecommentstatus', [
    'controller' => 'comment',
    'action'     => 'changeCommentStatus',
]);

/**
 * 事件.
 */

// 新增(修改)状态.
$staff->add('/addstatus', [
    'controller' => 'status',
    'action'     => 'addStatus',
]);

// 删除状态.
$staff->add('/delstatus', [
    'controller' => 'status',
    'action'     => 'delStatus',
]);

// 状态列表.
$staff->add('/statuslist', [
    'controller' => 'status',
    'action'     => 'statusList',
]);

// 状态详情.
$staff->add('/statusinfo', [
    'controller' => 'status',
    'action'     => 'statusInfo',
]);