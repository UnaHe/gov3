<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/5/11
 * Time: 11:32
 */

/**
 * 群众扫码入口.
 */

// 前台 —— 跳转到科室.
$route = $home->add('/forward/{forward_id:[0-9]+}');
$route->convert(
    'forward_id',
    function ($forward_id) {
        $forward_info = app\Models\Forwards::findFirst([
            'forward_id = :forward_id:',
            'bind' => [
                'forward_id' => $forward_id
            ],
        ]);
        if($forward_info && !empty($forward_info->forward_string)){
            header('Location:' . $forward_info->forward_string);
//            header('Location:' . 'http://g.local.com/status/workerStatusList?p=/8eSQb/zSHBjEJhubST3rqcWJF4MLRvsfIqrkMQQDb4=&d=/8eSQb/zSHBjEJhubST3rqcWJF4MLRvsfIqrkMQQDb4=');
        }else{
            echo "二维码不匹配，请联系管理员！";
            exit;
        }
    }
);

/**
 * 事件.
 */

// 首页.
$home->add('/status', [
    'controller' => 'status',
    'action'     => 'index',
]);
$home->add('/status/index', [
    'controller' => 'status',
    'action'     => 'index',
]);

// 人员(员工)状态列表.
$home->add('/status/workerStatusList', [
    'controller' => 'status',
    'action'     => 'workerStatusList',
]);
$home->add('/status/ajaxWorkerStatusList', [
    'controller' => 'status',
    'action'     => 'ajaxWorkerStatusList',
]);

// 人员事件列表.
$home->add('/status/workerStatusDetail', [
    'controller' => 'status',
    'action'     => 'workerStatusDetail',
]);

// 添加留言.
$home->add('/status/addComment', [
    'controller' => 'status',
    'action'     => 'addComment',
]);

/**
 * 告示.
 */

// 告示列表.
$home->addGet('/notice', [
    'controller' => 'notice',
    'action'     => 'index',
]);
$home->add('/notice/index', [
    'controller' => 'notice',
    'action'     => 'index',
]);

// ajax获取告示.
$home->add('/notice/ajaxIndex', [
    'controller' => 'notice',
    'action'     => 'ajaxIndex',
]);

// 告示详情.
$home->addGet('/notice/detail', [
    'controller' => 'notice',
    'action'     => 'detail',
]);

/**
 * 单位.
 */

// 单位介绍.
$home->addGet('/department/projectDetail', [
    'controller' => 'department',
    'action'     => 'projectDetail',
]);

// 科室列表.
$home->add('/department/departmentList', [
    'controller' => 'department',
    'action'     => 'departmentList',
]);
$home->add('/department/ajaxDepartmentList', [
    'controller' => 'department',
    'action'     => 'ajaxDepartmentList',
]);

// 科室介绍.
$home->addGet('/department/detail', [
    'controller' => 'department',
    'action'     => 'detail',
]);

