<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/5/16
 * Time: 18:18
 */

namespace app\Controllers\Api;

use app\Models\Comments;
use app\Models\Departments;
use app\Models\Sections;
use app\Models\Status;
use app\Models\UserBelongs;
use app\Models\Users;
use app\Models\UserStatus;

/**
 * 统计控制器
 * Class CountController
 * @package app\Controller\Api
 */
class CountController  extends ControllerBase
{
    /**
     * 统计列表.
     */
    public function indexAction()
    {
        $this->view->pick($this->session->get('tpl') . '/count');
    }

    /**
     * 我的统计.
     */
    public function myCountAction()
    {
        $routes = explode('/', $this->request->getURI());

        if (!in_array('apis', $routes)) {
            $user_id = $this->session->get('staff')['user_id'];
        } else {
            $user_phone = $this->request->get('user_phone');

            if (!preg_match('/^1[3456789]{1}\d{9}$/', $user_phone)) {
                return $this->ajaxError('参数错误', 406);
            }

            $user = Users::findFirst([
                'user_phone = :user_phone:',
                'bind' => [
                    'user_phone' => $user_phone,
                ],
            ]);

            if ($user === false) {
                return $this->ajaxError('用户不存在');
            } else {
                $user_id = $user->user_id;
            }
        }

        // 查询计划表中今天的工作列表.
        $time = time();
        $input['user_id'] = $user_id;
        $input['start_time'] = $time;

        // 我的留言.
        $my_comments = (new Comments())->getCommentsCount(['time' => $time, 'user_id' => $user_id]);
        $my_comments = $this->getCommentCountList($my_comments);

        $data = [
            'my_comments' => !empty($my_comments) ? $my_comments : false,
            'belongs' => false,
            'time' => $time,
        ];

        // 判断是否有下属统计信息.
        $belongs = (new UserBelongs())->getUsersByUserId($user_id, true);

        if (!empty($belongs)) {
            // 有下属列表.
            $data['belongs'] = true;

            // 项目信息.
            $project_info = (new Users())->getProjectDetailsById($user_id);

            // 项目默认事件
            $project_default_status = (new Status())->getDefaultStatusByProject($project_info['project_id']);

            $project_default_status_arr = [];
            foreach ($project_default_status as $v) {
                $project_default_status_arr[$v->project_id][$v->status_is_default]['status_id'] = $v->status_id;
                $project_default_status_arr[$v->project_id][$v->status_is_default]['status_name'] = $v->status_name;
                $project_default_status_arr[$v->project_id][$v->status_is_default]['status_color'] = $v->status_color;
            }
            unset($project_default_status);

            // 默认事件.
            $key = (date("H:i", $time) >= $project_info['work_start_time'] && date("H:i",  $time) <= $project_info['work_end_time']) ? 1 : 2;
            $project = isset($project_default_status_arr[$project_info['project_id']]) && array_key_exists($key, $project_default_status_arr[$project_info['project_id']]) !== false ? $project_info['project_id'] : 0;
            $project_default_work_status = $project_default_status_arr[$project][$key];

            // 获取状态列表.
            $today_status = (new UserStatus())->getStatusByUser($belongs);
            $today_belong_status_list = $this->getUsersStatusCountList($today_status, $project_default_work_status, count($belongs));

            $my_belong_comments = (new Comments())->getCommentsCount(['time' => $time, 'user_id' => $user_id], true);
            $my_belong_comments = $this->getCommentCountList($my_belong_comments);

            $data['today_belong_status_list'] = !empty($today_belong_status_list) ? $today_belong_status_list : false;
            $data['my_belong_comments'] = !empty($my_belong_comments) ? $my_belong_comments : false;
        }

        $data = [
            'status' => 200,
            'msg' => '',
            'data' => $data,
        ];

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 留言统计的列表.
     * @param $comments
     * @return array
     */
    private function getCommentCountList($comments)
    {
        $comment_list = [];
        $total_count = $comments['read'] + $comments['unread'];
        if ($total_count > 0) {
            foreach ($comments as $k => $v) {
                $comment_list[$k] = [
                    'name' => $k == 'read' ? '已处理' : '未处理',
                    'value' => $v,
                    'status_id' => $k == 'read' ? 1 : 0,
                    'percent' => sprintf("%.2f", $v / $total_count) * 100,
                ];
            }
        } else {
            $comment_list [] = [
                'name' => '暂无',
                'value' => 0,
                'status_id' => -1,
                'percent' => 100,
            ];
        }

        return array_values($comment_list);
    }

    /**
     * 得到统计状态的列表.
     * @param $today_status
     * @param $project_default_work_status
     * @param int $user_num
     * @return array
     */
    private function getUsersStatusCountList($today_status, $project_default_work_status, $user_num = 1)
    {
        $today_status_list = [];
        $today_status = $today_status ? $today_status : NULL;
        if ($today_status) {
            foreach ($today_status as $v) {
                if (array_key_exists($v['status_id'], $today_status_list)) {
                    $today_status_list[$v['status_id']]['value']++;
                    $today_status_list[$v['status_id']]['percent'] = sprintf("%2.f", $today_status_list[$v['status_id']]['value'] / $user_num) * 100;
                } else {
                    $today_status_list[$v['status_id']] = [
                        'status_id' => $v['status_id'],
                        "name" => $v['status_name'],
                        "status_color" => $v['status_color'],
                        "value" => 1,
                        "percent" => sprintf("%.2f", 1 / $user_num) * 100
                    ];
                }
            }
        }
        if (count($today_status) < $user_num) { //有默认事件的情况
            $today_status_list [] = [
                'status_id' => $project_default_work_status['status_id'],
                "name" => $project_default_work_status['status_name'],
                "status_color" => $project_default_work_status['status_color'],
                "user_status_desc" => '默认事件',
                "value" => $user_num - count($today_status),
                "percent" => sprintf("%.2f", (($user_num - count($today_status)) / $user_num)) * 100
            ];
        }
        return array_values($today_status_list);
    }

    /**
     * 状态统计详情（下属员工列表）.
     */
    public function countStatusListAction()
    {
        if ($this->request->isPost()) {
            $routes = explode('/', $this->request->getURI());

            if (!in_array('apis', $routes)) {
                $user_info = $this->session->get('staff');
            } else {
                $user_phone = $this->request->get('user_phone');

                if (!preg_match('/^1[3456789]{1}\d{9}$/', $user_phone)) {
                    return $this->ajaxError('参数错误', 406);
                }

                $user_info = (new Users())->getDetailsByTel($user_phone);

                if ($user_info === false) {
                    return $this->ajaxError('用户不存在');
                }
            }

            $time = time();
            $input = $this->request->get();
            $input['user_id'] = $user_info['user_id'];
            $input['time'] = $time;

            // 分页参数.
            $limit = $this->request->get('limit', 'int', 10);
            $page = $this->request->get('page', 'int', 1);
            $user_list = (new Users())->getUsersListUseCountByUser($input, true, $page, $limit);

            // 项目默认事件
            $project_default_status = (new Status())->getDefaultStatusByProject($user_info['project_id']);

            $project_default_status_arr = [];
            foreach ($project_default_status as $v) {
                $project_default_status_arr[$v->project_id][$v->status_is_default]['status_id'] = $v->status_id;
                $project_default_status_arr[$v->project_id][$v->status_is_default]['status_name'] = $v->status_name;
                $project_default_status_arr[$v->project_id][$v->status_is_default]['status_color'] = $v->status_color;
            }
            unset($project_default_status);

            foreach ($user_list as $k => $v) {
                if ($v['user_status_id'] === NULL) {//默认事件
                    $key = (date("H:i", $time) > $v['work_start_time'] && date("H:i", $time) < $v['work_end_time']) ? 1 : 2;
                    $project = isset($project_default_status_arr[$v['project_id']]) && array_key_exists($key,$project_default_status_arr[$v['project_id']]) !== false ? $v['project_id'] : 0;
                    $user_list[$k]['status_id'] = $project_default_status_arr[$project][$key]['status_id'];
                    $user_list[$k]['status_name'] = $project_default_status_arr[$project][$key]['status_name'];
                    $user_list[$k]['status_color'] = $project_default_status_arr[$project][$key]['status_color'];
                    $user_list[$k]['user_status_desc'] = '默认状态';
                }
            }

            $data['user_id'] = $input;
            $data['data'] = !empty($user_list) ? $user_list : false;
            $data['department_id'] = isset($data['department_id']) ? $data['department_id'] : 0;
            $data['section_id'] = isset($data['section_id']) ? $data['section_id'] : 0;

            $res = [
                'status' => 200,
                'msg' => '操作成功',
                'data' => $data,
            ];

            return json_encode($res, JSON_UNESCAPED_UNICODE);
        }

        $type = $this->request->get('type');
        $status_id = $this->request->get('status_id');
        $projectId = $this->session->get('staff')['project_id'];

        $department_list = Departments::find([
            'project_id = :project_id:',
            'bind' => [
                'project_id' => $projectId,
            ],
            'columns' => 'department_id, department_name',
        ]);
        $section_list = Sections::find([
            'project_id = :project_id:',
            'bind' => [
                'project_id' => $projectId,
            ],
            'columns' => 'section_id, section_name',
        ]);
        $status_list = (new Status())->getListByProjectMore($projectId);

        // 页面参数.
        $this->view->setVars([
            'type' => $type,
            'status_id' => $status_id,
            'department_list'=>$department_list,
            'section_list'=>$section_list,
            'status_list'=>$status_list
        ]);

        $this->view->pick($this->session->get('tpl') . '/countstatusdetail');

        return true;
    }

    /**
     * 状态统计详情（按分类查询具体信息）.
     */
    public function countStatusDetailByStatusAction()
    {
        $routes = explode('/', $this->request->getURI());

        if (!in_array('apis', $routes)) {
            $user_info = $this->session->get('staff');
        } else {
            $user_phone = $this->request->get('user_phone');

            if (!preg_match('/^1[3456789]{1}\d{9}$/', $user_phone)) {
                return $this->ajaxError('参数错误', 406);
            }

            $user_info = (new Users())->getDetailsByTel($user_phone);

            if ($user_info === false) {
                return $this->ajaxError('用户不存在');
            }
        }

        // 分页参数.
        $limit = $this->request->get('limit', 'int', 10);
        $page = $this->request->get('page', 'int', 1);

        $time = time();
        $type = $this->request->getPost('type');
        $status_id = $this->request->getPost('status_id');
        $need_relation = $type == 2 ? true : false;
        $input = $this->request->getPost();
        $input['user_id'] = $user_info['user_id'];
        $input['time'] = $time;

        $status_info = Status::findFirst($status_id);
        if ($status_info->status_is_default) {
            // 默认事件.
            $status_list = [];
            $user_info['work_start_time'] = strtotime($user_info['work_start_time']);
            $user_info['work_end_time'] = strtotime($user_info['work_end_time']);
            $working_flag = ($status_info->status_is_default == 1 && $user_info['work_start_time'] <= $time && $user_info['work_end_time'] >= $time) ? true : false;
            $workout_flag = ($status_info->status_is_default == 2 && ($user_info['work_start_time'] > $time || $user_info['work_end_time'] < $time)) ? true : false;

            if($working_flag || $workout_flag){
                $user_list = (new Users())->getUserListUseCountOnDefault($input, $need_relation, $page, $limit);
                if ($user_list) {
                    foreach ($user_list as $v) {
                        $status_list[] = [
                            'status_id' => $status_info->status_id,
                            'status_name' => $status_info->status_name,
                            'status_color' => $status_info->status_color,
                            'user_id' => $v['user_id'],
                            'user_name' => $v['user_name'],
                            'department_name' => $v['department_name'],
                            'section_name' => $v['section_name'],
                        ];
                    }
                }
            }
        } else {
            $status_list = (new Users())->getUserListUseCount($input, $need_relation, $page, $limit);
        }
        $data = $input;
        $data['data'] = !empty($status_list) ? $status_list : false;
        $data['department_id'] = isset($data['department_id']) ? $data['department_id'] : 0;
        $data['section_id'] = isset($data['section_id']) ? $data['section_id'] : 0;

        $res = [
            'status' => 200,
            'msg' => '操作成功',
            'data' => $data,
        ];

        return json_encode($res, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 获取单个用户的计划列表和个人信息.
     */
    public function userStatusListAction()
    {
        // 获取参数.
        $user_id = $this->request->get('user_id');
        if (!$user_id) {
            return $this->ajaxError('参数错误', 406);
        }

        $userInfo = (new Users())->getProjectDetailsById($user_id);

        if ($userInfo === false) {
            return $this->ajaxError('用户不存在');
        }

        if ($userInfo['user_image']) {
            $userInfo['user_image'] = self::$upload_url . $userInfo['user_image'];
        } else {
            $userInfo['user_image'] = $this->config->constants['default_staff_img'];
        }

        // 计划列表.
        $userInfo['statuslist'] = (new UserStatus())->getPlanListByUserId($userInfo['user_id']);

        // 当前状态.
        $currentStatus = (new UserStatus())->getStatusByUser($userInfo['user_id']);
        if ($currentStatus !== false) {
            $userInfo['nowstatus'] = $currentStatus[0];
        } else {
            // 默认状态.
            $project_default_status = (new Status())->getDefaultStatusByProject($userInfo['project_id']);

            $project_default_status_arr = [];
            foreach ($project_default_status as $v) {
                $project_default_status_arr[$v->project_id][$v->status_is_default] = $v->toArray();
            }
            unset($project_default_status);

            $time = time();
            $key = (date("H:i", $time) > $userInfo['work_start_time'] && date("H:i",  $time) < $userInfo['work_end_time']) ? 1 : 2;
            $project = isset($project_defalut_status_arr[$userInfo['project_id']]) && array_key_exists($key, $project_default_status_arr[$userInfo['project_id']]) !== false ? $userInfo['project_id'] : 0;
            $userInfo['nowstatus'] = $project_default_status_arr[$project][$key];
            $userInfo['nowstatus']['start_time'] = $time;
            $userInfo['nowstatus']['end_time'] = strtotime($userInfo['work_end_time']);
        }

        if ($this->request->isPost()) {
            $data = [
                'status' => 200,
                'msg' => '操作成功',
                'data' => $userInfo,
            ];

            return json_encode($data, JSON_UNESCAPED_UNICODE);
        } else {
            // 页面参数.
            $this->view->setVars([
                'data' => $userInfo,
            ]);

            $this->view->pick($this->session->get('tpl') . '/statuslist');

            return true;
        }
    }

    /**
     * 留言统计详情（按分类查询具体信息）.
     */
    public function countCommentDetailAction()
    {
        if ($this->request->isPost()) {
            $routes = explode('/', $this->request->getURI());

            if (!in_array('apis', $routes)) {
                $user_id = $this->session->get('staff')['user_id'];
            } else {
                $user_phone = $this->request->get('user_phone');

                if (!preg_match('/^1[3456789]{1}\d{9}$/', $user_phone)) {
                    return $this->ajaxError('参数错误', 406);
                }

                $user = Users::findFirst([
                    'user_phone = :user_phone:',
                    'bind' => [
                        'user_phone' => $user_phone,
                    ],
                ]);

                if ($user === false) {
                    return $this->ajaxError('用户不存在');
                } else {
                    $user_id = $user->user_id;
                }
            }

            $time = time();
            $input = [];
            $type = $this->request->get('type');
            $status_id = $this->request->get('status_id');
            $need_relation = $type == 3 ? true : false;
            $input['user_id'] = $user_id;
            $input['status'] = $status_id;
            $input['start_time'] = strtotime(date("Y-m-d"), $time);

            // 留言.
            if ($type) {
                // 分页参数.
                $limit = $this->request->get('limit', 'int', 10);
                $page = $this->request->get('page', 'int', 1);

                if ($need_relation) {
                    // 多人.
                    $input['user_id'] = (new UserBelongs())->getUsersByUserId($user_id, true);
                    $comment_list = (new Comments())->index($input, true, $page, $limit);
                } else {
                    // 自己的.
                    $comment_list = (new Comments())->index($input, true, $page, $limit);
                }
                $data['data'] = !empty($comment_list) ? $comment_list : false;
            }
            $data['type'] = $type;
            $data['user_id'] = $user_id;

            $res = [
                'status' => 200,
                'msg' => '操作成功',
                'data' => $data,
            ];

            return json_encode($res, JSON_UNESCAPED_UNICODE);
        }
        $type = $this->request->get('type');
        $status_id = $this->request->get('status_id');

        // 页面参数
        $this->view->setVars([
            'type' => $type,
            'status_id' => $status_id,
        ]);

        $this->view->pick($this->session->get('tpl') . '/countcommentdetail');

        return true;
    }

    /**
     * 获取下拉列表.
     */
    public function getSelectOptionsAction()
    {
        if ($this->request->isPost()) {
            $routes = explode('/', $this->request->getURI());

            if (!in_array('apis', $routes)) {
                $user_project_id = $this->session->get('staff')['project_id'];
            } else {
                $user_phone = $this->request->get('user_phone');

                if (!preg_match('/^1[3456789]{1}\d{9}$/', $user_phone)) {
                    return $this->ajaxError('参数错误', 406);
                }

                $user = Users::findFirst([
                    'user_phone = :user_phone:',
                    'bind' => [
                        'user_phone' => $user_phone,
                    ],
                ]);

                if ($user === false) {
                    return $this->ajaxError('用户不存在');
                } else {
                    $user_project_id = $user->project_id;
                }
            }

            $department_list = Departments::findFirst([
                'project_id = :project_id:',
                'columns' => 'department_id, department_name',
                'bind' => [
                    'project_id' => $user_project_id,
                ],
            ]);
            $section_list = Sections::findFirst([
                'project_id = :project_id:',
                'columns' => 'section_id, section_name',
                'bind' => [
                    'project_id' => $user_project_id,
                ],
            ]);
            $status_list = (new Status())->getListByProjectMore($user_project_id);

            $res = [
                'status' => 200,
                'msg' => '操作成功！',
                'department_list' => $department_list,
                'section_list' => $section_list,
                'status_list' => $status_list
            ];

            return json_encode($res, JSON_UNESCAPED_UNICODE);
        }

        return false;
    }

}