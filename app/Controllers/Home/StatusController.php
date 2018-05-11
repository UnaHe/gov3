<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/5/11
 * Time: 11:39
 */

namespace app\Controllers\Home;

use app\Models\Project;
use app\Models\Status;
use app\Models\Users;

/**
 * 人员状态控制器
 * Class StatusController
 * @package app\Controller\Home
 */
class StatusController extends ControllerBase
{
    /**
     * 人员(员工)状态列表.
     */
    public function workerStatusListAction()
    {
        // 获取参数.$input = [];
        $input['project_id'] = self::$project_id;
        $input['department_id'] = self::$department_id;

        // 判断请求方式.
        $title_info = (new Project())->getDetailsByProject($input)[$input['project_id']];

        $this->view->setVars([
            'title_info'=>$title_info
        ]);
    }

    public function ajaxWorkerStatusListAction()
    {
        // 获取参数.
        $page = $this->request->get('page', 'int', 1);
        $limit = $this->request->get('limit', 'int', 10);

        $input = [];
        $input['project_id'] = self::$project_id;
        $input['department_id'] = self::$department_id;
        $input['time'] =  time();
        $data = [];

        $data['user_list'] = (new Users())->getProjectUsersByProject($input,true, $page, $limit, true);

        $project_default_status = (new Status())->getDefaultStatusByProject($input['project_id']);
        $project_default_status_arr = [];

        foreach($project_default_status as $v){
            $project_default_status_arr[$v->project_id][$v->status_is_default ] = $v;
        }
        unset($project_default_status);

        // 对象赋值循环内可以, 出了循环失效, 曲线救国.
        $params = [];
        $user_list = [];
        foreach ($data['user_list']->items as $k => $v){
            if($v->user_status_id){
                // 其他事件.
                $params[$v->a->user_id]['status_id'] = $v->status_id;
                $params[$v->a->user_id]['status_name'] = $v->status_name;
                $params[$v->a->user_id]['status_color'] = $v->status_color;
                $params[$v->a->user_id]['user_status_desc'] = $v->user_status_desc;
            }else{//默认事件
                $key = (date("H:i",  $input['time']) > $v->a->work_start_time && date("H:i",  $input['time']) < $v->a->work_end_time) ? 1 : 2;
                $project = isset($project_default_status_arr[$v->a->project_id]) && array_key_exists($key, $project_default_status_arr[$v->a->project_id]) !== false ? $v->a->project_id : 0;
                $v->status_id = $project_default_status_arr[$project][$key]->status_id;
                $v->status_name = $project_default_status_arr[$project][$key]->status_name;
                $v->status_color = $project_default_status_arr[$project][$key]->status_color;
                $v->user_status_desc = '默认状态';
            }
            $v->a->section_id = $v->a->section_id ? $v->a->section_id : 'o';
            if(!array_key_exists($v->a->section_id ,$user_list)){
                $user_list[$v->a->section_id]['section_name'] = $v->a->section_id ? $v->a->section_id  : '其他';
            }
            $user_list[$v->a->section_id]['user_list'][] = $v;
        }
        $user_list = !empty($user_list) ? $user_list : false;
        $data = [
            'status' => 200,
            'msg' => '',
            'data' => $user_list,
            'params' => $params,
        ];

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

}