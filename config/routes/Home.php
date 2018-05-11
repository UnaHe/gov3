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
        $forward_info = app\Models\Forwards::findFirst($forward_id);
        if($forward_info && !empty($forward_info->forward_string)){
//            header('Location:' . $forward_info->forward_string);
            header('Location:' . 'http://g.local.com/status/workerStatusList?p=/8eSQb/zSHBjEJhubST3rqcWJF4MLRvsfIqrkMQQDb4=&d=/8eSQb/zSHBjEJhubST3rqcWJF4MLRvsfIqrkMQQDb4=');
        }else{
            echo "二维码不匹配，请联系管理员！";
            exit;
        }
    }
);

// 人员(员工)状态列表.
$home->add('/status/workerStatusList', [
    'controller' => 'status',
    'action'     => 'workerStatusList',
]);

$home->add('/status/ajaxWorkerStatusList', [
    'controller' => 'status',
    'action'     => 'ajaxWorkerStatusList',
]);