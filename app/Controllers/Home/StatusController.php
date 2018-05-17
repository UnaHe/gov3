<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/5/11
 * Time: 11:39
 */

namespace app\Controllers\Home;

use app\Models\Comments;
use app\Models\Project;
use app\Models\Status;
use app\Models\Users;
use app\Models\UserStatus;

/**
 * 人员状态控制器
 * Class StatusController
 * @package app\Controller\Home
 */
class StatusController extends ControllerBase
{
    /**
     * 首页
     */
    public function indexAction()
    {
        // 获取参数.
        $input['project_id'] = self::$project_id;
        $input['department_id'] = self::$department_id;
        $title_info = (new Project())->getDetailsByProject($input)[$input['project_id']];

        $this->view->setVars([
            'title_info'=>$title_info
        ]);

        $this->view->pick('index/index');
    }

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
        foreach ($project_default_status as $v) {
            $project_default_status_arr[$v->project_id][$v->status_is_default]['status_id'] = $v->status_id;
            $project_default_status_arr[$v->project_id][$v->status_is_default]['status_name'] = $v->status_name;
            $project_default_status_arr[$v->project_id][$v->status_is_default]['status_color'] = $v->status_color;
        }
        unset($project_default_status);

        // 准备数据.
        $user_list = [];
        foreach ($data['user_list']->items as $v) {
            if($v->user_status_id === NULL) {
                $key = (date("H:i", $input['time']) > $v->a->work_start_time && date("H:i",  $input['time']) < $v->a->work_end_time) ? 1 : 2;
                $project = isset($project_default_status_arr[$v->a->project_id]) && array_key_exists($key, $project_default_status_arr[$v->a->project_id]) !== false ? $v->a->project_id : 0;
                $v->status_id = $project_default_status_arr[$project][$key]['status_id'];
                $v->status_name = $project_default_status_arr[$project][$key]['status_name'];
                $v->status_color = $project_default_status_arr[$project][$key]['status_color'];
                $v->user_status_desc = '默认状态';
            }
            $v->a->section_id = $v->a->section_id ? $v->a->section_id : 'o';
            if (!array_key_exists($v->a->section_id ,$user_list)) {
                $user_list[$v->a->section_id]['section_name'] = $v->a->section_name ? $v->a->section_name  : '其他';
            }
            $user_list[$v->a->section_id]['user_list'][] = $v;
        }
        $user_list = !empty($user_list) ? $user_list : false;
        $data = [
            'status' => 200,
            'msg' => '',
            'data' => $user_list,
        ];

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 人员(员工)事件列表.
     */
    public function workerStatusDetailAction()
    {
        // 获取参数.
        $input = $this->request->get();
        $time = time();
        $user_id = $input['user_id'];
        $data['user_info'] = (new Users())->getProjectDetailsById($user_id);
        $project_default_status = (new Status())->getDefaultStatusByProject(self::$project_id);
        $other_status = (new UserStatus())->getStatusByUser($user_id);
        $data['user_comments'] = (new Comments())->getCommentsByUserId($user_id);

        // 事件信息.
        $project_default_status_arr = [];
        foreach($project_default_status as $v){
            $project_default_status_arr[$v->project_id][$v->status_is_default]['status_id'] = $v->status_id;
            $project_default_status_arr[$v->project_id][$v->status_is_default]['status_name'] = $v->status_name;
            $project_default_status_arr[$v->project_id][$v->status_is_default]['status_color'] = $v->status_color;
        }
        unset($project_default_status);

        // 用户状态.
        $other_status_arr = [];
        if ($other_status !== false) {
            foreach($other_status as $v){
                $other_status_arr[$v['user_id']] = $v;
            }
        }

        // 当前时间用户是否存在事件.
        if (!empty($other_status_arr) && array_key_exists($user_id, $other_status_arr) !== false) {
            $data['user_info']['status_id'] = $other_status_arr[$user_id]['status_id'];
            $data['user_info']['status_name'] = $other_status_arr[$user_id]['status_name'];
            $data['user_info']['status_color'] = $other_status_arr[$user_id]['status_color'];
            $data['user_info']['user_status_desc'] = $other_status_arr[$user_id]['user_status_desc'];
            $data['user_info']['user_status_start_time'] = date('Y/m/d H:i', $time);
            $data['user_info']['user_status_end_time'] = date('Y/m/d H:i', $other_status_arr[$user_id]['end_time']);
        } else {
            $key = (date("H:i", $time) > $data['user_info']['work_start_time'] && date("H:i", $time) < $data['user_info']['work_end_time']) ? 1 : 2;
            $project =  isset($project_default_status_arr[$data['user_info']['project_id']]) && array_key_exists($key, $project_default_status_arr[$data['user_info']['project_id']]) !== false ? $data['user_info']['project_id'] : 0;
            $data['user_info']['status_id'] = $project_default_status_arr[$project][$key]['status_id'];
            $data['user_info']['status_name'] = $project_default_status_arr[$project][$key]['status_name'];
            $data['user_info']['status_color'] = $project_default_status_arr[$project][$key]['status_color'];
            $data['user_info']['user_status_desc'] = '默认状态';
            $data['user_info']['user_status_start_time'] = date('Y/m/d H:i', $time);
            $work_end_time = $key === 1 ? strtotime($data['user_info']['work_end_time']) : strtotime($data['user_info']['work_start_time']) + 86400;
            $data['user_info']['user_status_end_time'] = date('Y/m/d H:i', $work_end_time);
        }

        $data['user_info']['user_id'] = $user_id;
        
        // 页面数据.
        $this->view->setVars([
            'data' => $data,
        ]);
    }

    /**
     * 添加留言.
     */
    public function addCommentAction()
    {
        // 获取参数.
        $input = $this->request->get();

        if ($this->request->isPost()) {
            $params = $this->request->getPost();

            if (!$params['user_id'] || !$params['comment_phone'] || !$params['comment_name'] ||!$params['comment_content']) {
                return $this->ajaxError('参数错误');
            }

            $data = [
                'user_id'=>$params['user_id'],
                'comment_phone'=>$params['comment_phone'],
                'comment_name'=>$params['comment_name'],
                'comment_content'=>$params['comment_content'],
            ];

            // 创建留言.
            $res = (new Comments())->create($data);

            if ($res) {
                return $this->ajaxSuccess('留言成功', 201);
            } else {
                $this->logger->error($this->getCname() . '---' . $res);
                return $this->ajaxError('留言失败，请稍后重试');
            }
        }

        $this->view->setVars([
            'user_id' => $input['user_id'],
        ]);

        return true;
    }

}