<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/20
 * Time: 12:40
 */

namespace app\Controllers\Admin;

use app\Models\Departments;
use app\Models\Project;
use app\Models\Sections;
use app\Models\Status;
use app\Models\Users;
use app\Models\UserStatus;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

/**
 * 人员状态控制器
 * Class StatusController
 * @package app\Controller\Admin
 */
class StatusController extends ControllerBase
{
    /**
     * 事件列表.
     */
    public function indexAction()
    {
        // 分页参数.
        $page = $this->request->get('page', 'int', 1);
        $limit = $this->request->get('limit', 'int', 10);

        // 获取数据.
        $input = $this->request->getQuery();

        $user = $this->session->get('user');

        $data = [];
        $projects = [];
        if ($user['user_is_super'] && empty($user['project_id'])) {
            $projects = Project::getProjectList();
            if(!empty(current($projects))){
                $input['project_id'] = isset($input['project_id']) ? $input['project_id'] : $projects[0]->project_id;
            }else{
                $input['project_id'] = false;
            }
        } else {
            $input['project_id'] = $user['project_id'];
        }

        $data['project_list'] = $projects;
        $status_list = (new Status())->getList($input['project_id'], $page, $limit);

        // 页面参数.
        $this->view->setVars([
            'status_list' => $status_list,
            'input' => $input,
            'data' => $data,
        ]);
    }

    /**
     * 添加事件.
     */
    public function createAction()
    {
        $user = $this->session->get('user');

        if ($user['user_is_super'] && empty($user['project_id'])) {
            $data =Project::getProjectList();
        } else {
            $data = $user['project_name'];
        }

        $this->view->project = $data;
    }

    /**
     * 保存事件.
     */
    public function saveAction()
    {
        $projectId = $this->request->getPost('project_id');
        $statusName = $this->request->getPost('status_name');
        $statusColor = $this->request->getPost('status_color');
        $statusIsDefault = $this->request->getPost('status_is_default');
        $statusOrder = $this->request->getPost('status_order') ? : NULL;

        // 验证.
        if (!$projectId) {
            $this->flash->warning('请选择单位');
            return $this->dispatcher->forward(
                [
                    'namespace'  => 'app\Controllers\Admin',
                    'controller' => 'status',
                    'action'     => 'create',
                ]
            );
        }

        if (empty($statusName) || mb_strlen($statusName, 'UTF-8') > 100) {
            $this->flash->warning('名称长度 1 - 100 个字符');
            return $this->dispatcher->forward(
                [
                    'namespace'  => 'app\Controllers\Admin',
                    'controller' => 'status',
                    'action'     => 'create',
                ]
            );
        }

        $data = [
            'project_id' => $projectId,
            'status_name' => $statusName,
            'status_color' => $statusColor,
            'status_is_default' => $statusIsDefault,
            'status_order' => $statusOrder,
            'created_id' => $this->session->get('user')['user_id'],
        ];

        $Status = new Status();
        $res = false;

        if ($statusIsDefault > 0) {
            $is_default = $statusIsDefault;
            $data['status_is_default'] = 0;

            if ($Status->create($data) === true) {
                $res = Status::setDefault($projectId, $Status->status_id, $is_default);
            }
        } else {
            $res = $Status->create($data);
        }

        if ($res === true) {
            $this->flashSession->success('创建成功');

            return $this->response->redirect('admin/status?project_id=' . $projectId);
        } else {
            $this->logger->error($this->getCname() . '---' . $res);
            return $this->flash->error('事件创建失败，请稍后重试');
        }
    }

    /**
     * 修改事件信息.
     * @param $statusId
     * @return bool|\Phalcon\Http\Response|\Phalcon\Http\ResponseInterface|\Phalcon\Mvc\View
     */
    public function editAction($statusId)
    {
        // 查找.
        $status = Status::findFirst($statusId);

        if ($status === false) {
            $this->flashSession->error('事件不存在');

            return $this->response->redirect('admin/status');
        }

        $user = $this->session->get('user');

        if ($user['user_is_super'] && empty($user['project_id'])) {
            $project = Project::getProjectList();
        } else {
            $project = $user['project_name'];
        }

        // 页面参数.
        $this->view->setVars([
            'status' => $status,
            'project' => $project,
        ]);

        return true;
    }

    /**
     * 更新事件.
     */
    public function updateAction()
    {
        // 获取参数.
        $input = $this->request->getPost();
        $input['status_order'] = $input['status_order'] ? : NULL;

        $status = Status::findFirst($input['status_id']);

        if ($status === false) {
            $this->flashSession->error('事件不存在');

            return $this->response->redirect('admin/status');
        }

        if ($input['status_is_default'] > 0) {
            $is_default = $input['status_is_default'];
            $input['status_is_default'] = 0;
            $res = Status::findFirst($input['status_id'])->update($input);
            Status::setDefault($input['project_id'], $input['status_id'], $is_default);
        } else {
            $res = Status::findFirst($input['status_id'])->update($input);
        }

        if ($res === true) {
            $this->flashSession->success('更新成功');

            return $this->response->redirect('admin/status?project_id=' . $input['project_id']);
        } else {
            $this->logger->error($this->getCname() . '---' . $res);
            return $this->flash->error('事件更新失败，请稍后重试');
        }
    }

    /**
     * 验证事件名称.
     */
    public function validNameAction()
    {
        // 获取数据.
        $input = $this->request->getPost();

        $status = Status::findFirst([
            'project_id = :project_id: AND status_name = :status_name:',
            'bind' => [
                'project_id' => $input['project_id'],
                'status_name' => $input['status_name'],
            ]
        ]);

        if ($status === false) {
            return $this->ajaxSuccess();
        } else {
            return $this->ajaxError('该事件已经存在');
        }
    }

    /**
     * 删除事件.
     */
    public function deleteAction()
    {
        $statusId = $this->request->getPost('status_id');

        $status = Status::findFirst($statusId);

        if ($status === false) {
            $this->flashSession->error('事件不存在');

            return $this->response->redirect('admin/status');
        }

        // 执行删除.
        if ($status->delete() !== false) {
            return $this->ajaxSuccess('删除成功', 201);
        } else {
            return $this->ajaxError('事件删除失败，请稍后重试');
        }
    }

    /**
     * 修改排序.
     */
    public function changeOrderAction()
    {
        // 获取数据.
        $input = $this->request->getPost();
        $input['status_order'] = $input['status_order'] ?  : NULL;

        $status = Status::findFirst($input['status_id']);

        if ($status->update($input) === true) {
            return $this->ajaxSuccess('事件排序更新成功', 201);
        } else {
            return $this->ajaxError('该事件已经存在');
        }
    }

    /**
     * 设置默认事件.
     */
    public function setDefaultAction()
    {
        // 获取数据.
        $input = $this->request->getPost();

        $res = Status::setDefault($input['project_id'], $input['status_id'], $input['status_is_default']);

        if ($res === true) {
            return $this->ajaxSuccess('操作成功', 201);
        } else {
            $this->logger->error($this->getCname() . '---' . $res);
            return $this->ajaxError('操作，请稍后重试');
        }
    }

    /**
     * 设置工作时间表.
     */
    public function settingWorkTimeListAction()
    {
        // 分页参数.
        $page = $this->request->get('page', 'int', 1);
        $limit = $this->request->get('limit', 'int', 10);

        $user = $this->session->get('user');

        $Project = [];
        if ($user['user_is_super'] && empty($user['project_id'])) {
            $Project = Project::find([
                'order' => 'project_id ASC'
            ]);
        } else if (!empty($user['project_id'])) {
            $Project = Project::find([
                'project_id = :project_id:',
                'bind' => [
                    'project_id' => $user['project_id']
                ],
                'order' => 'project_id ASC'
            ]);
        }

        $paginator = new PaginatorModel(
            [
                "data"  => $Project,
                "limit" => $limit,
                "page"  => $page,
            ]
        );

        $project_list = $paginator->getPaginate();

        // 页面参数.
        $this->view->setVars([
            'project_list' => $project_list,
        ]);
    }

    /**
     * 设置工作时间.
     */
    public function settingWorkTimeAction()
    {
        // 获取数据.
        $input = $this->request->getPost();

        $user = $this->session->get('user');
        $project_id = !empty($user['project_id']) ? $user['project_id'] : $input['project_id'];

        $result = Project::findFirst([
            'project_id = :project_id:',
            'bind' => [
                'project_id' => $project_id
            ]
        ])->update($input);

        if ($result === true) {
            return $this->ajaxSuccess('工作时间修改成功', 201);
        } else {
            return $this->ajaxError('工作时间修改失败, 请稍后在试');
        }
    }

    /**
     * 已设事件.
     */
    public function userStatusAction()
    {
        // 参数.
        $page = $this->request->get('page', 'int', 1);
        $limit = $this->request->get('limit', 'int', 10);
        $input = $this->request->getQuery();
        $input['start_time'] = isset($input['start_time']) ? $input['start_time'] : date("Y-m-d H:i:s");

        // 规范参数, 避免查询出错.
        foreach ($input as $k => $v){
            if ($v === '') {
                $input[$k] = NULL;
            }
        }

        $user = $this->session->get('user');

        if ($user['user_is_super'] && empty($user['project_id'])) {
            $data['project_list'] = Project::getProjectList();
            if(!empty($input['project_id'])){
                $data['department_list'] = (new Departments())->getTree(0, 0, $input['project_id']);
                $data['status_list'] = Status::getListByProjectId($input['project_id']);
                $data['section_list'] = (new Sections())->getTree(0, 0, $input['project_id']);
            }
        } else {
            $input['project_id'] = $user['project_id'];
            $data['department_list'] = (new Departments())->getTree(0, 0, $user['project_id']);
            $data['status_list'] = Status::getListByProjectId($user['project_id']);
            $data['section_list'] = (new Sections())->getTree(0, 0, $input['project_id']);
        }

        $data['list'] = (new UserStatus())->getUserStatusList($input, false, $page, $limit);

        // 页面参数.
        $this->view->setVars([
            'data' => $data,
            'input' => $input,
        ]);
    }

    /**
     * 修改状态.
     */
    public function changeStatusAction()
    {
        // 获取参数
        $input = $this->request->getPost();
        $input['start_time'] = strtotime($input['start_time']);
        $input['end_time'] = strtotime($input['end_time']);

        // 是否同时间段存在事务.
        $userConflict = UserStatus::getConflict($input['user_id'],$input['start_time'],$input['end_time'], $input['user_status_id']);

        if ($userConflict === true) {
            $UserStatus = UserStatus::findFirst($input['user_status_id']);

            // 更新.
            if ($UserStatus->update($input) === true) {
                return $this->ajaxSuccess('事件添加成功', 201);
            } else {
                return $this->ajaxError('事件添加失败, 请稍后重试');
            }
        } else {
            return $this->ajaxError('时间段内存在其它事件, 请合理安排');
        }
    }

    /**
     * 人员(员工)状态列表.
     */
    public function workerStatusListAction()
    {
        // 参数.
        $page = $this->request->get('page', 'int', 1);
        $limit = $this->request->get('limit', 'int', 10);
        $input = $this->request->getQuery();

        // 规范参数, 避免查询出错.
        foreach ($input as $k => $v){
            if ($v === '') {
                $input[$k] = NULL;
            }
        }

        $user = $this->session->get('user');

        $data = [];
        $projects = [];

        if ($user['user_is_super'] || ($user['user_is_admin'] && $user['project_id'] == '')) {
            $data['project_list'] = Project::getProjectList();
            if(!empty($input['project_id'])){
                $data['department_list'] = (new Departments())->getTree(0, 0, $input['project_id']);
                $data['section_list'] = (new Sections())->getTree(0, 0, $input['project_id']);
            }
        } else {
            $input['project_id'] = $user['project_id'];
            $data['department_list'] = (new Departments())->getTree(0, 0, $user['project_id']);
            $data['section_list'] = (new Sections())->getTree(0, 0, $input['project_id']);
        }

        $input['time'] = time();
        $data['list'] = (new Users())->getProjectUsersByProject($input,true, $page, $limit, true);

        foreach ($data['list']->items as $v) {
            $projects[] = $v->a->project_id;
        }

        $projects = array_unique($projects);

        $project_default_status = (new Status())->getDefaultStatusByProject($projects);

        $project_default_status_arr = [];
        foreach ($project_default_status as $v) {
            $project_default_status_arr[$v->project_id][$v->status_is_default]['status_id'] = $v->status_id;
            $project_default_status_arr[$v->project_id][$v->status_is_default]['status_name'] = $v->status_name;
            $project_default_status_arr[$v->project_id][$v->status_is_default]['status_color'] = $v->status_color;
        }
        unset($project_default_status);

        // 对象赋值循环内可以, 出了循环失效, 曲线救国.
        $params = [];
        foreach ($data['list']->items as $v) {
            if ($v->user_status_id) {
                // 其他事件.
                $params[$v->a->user_id]['status_id'] = $v->status_id;
                $params[$v->a->user_id]['status_name'] = $v->status_name;
                $params[$v->a->user_id]['status_color'] = $v->status_color;
                $params[$v->a->user_id]['user_status_desc'] = $v->user_status_desc;
            } else {
                // 默认事件.
                $key = (date("H:i", $input['time']) > $v->a->work_start_time && date("H:i",  $input['time']) < $v->a->work_end_time) ? 1 : 2;
                $project = isset($project_default_status_arr[$v->a->project_id]) && array_key_exists($key, $project_default_status_arr[$v->a->project_id]) !== false ? $v->a->project_id : 0;
                $params[$v->a->user_id]['status_id'] = $project_default_status_arr[$project][$key]['status_id'];
                $params[$v->a->user_id]['status_name'] = $project_default_status_arr[$project][$key]['status_name'];
                $params[$v->a->user_id]['status_color'] = $project_default_status_arr[$project][$key]['status_color'];
                $params[$v->a->user_id]['user_status_desc'] = '默认状态';
            }
        }

        // 页面参数.
        $this->view->setVars([
            'data' => $data,
            'input' => $input,
            'params' => $params,
        ]);
    }

    /**
     * 得到用户可选的事件选项.
     */
    public function ajaxGetStatusOptionByUserAction()
    {
        // 获取参数.
        $input = $this->request->getPost();

        $status_list = Status::getListByProjectId($input['project_id']);

        if ($status_list) {
            return $this->ajaxSuccess($status_list);
        } else {
            return $this->ajaxError('暂无法获取，请稍后重试');
        }
    }

    /**
     * 保存用户事件.
     */
    public function saveUserStatusAction()
    {
        // 获取参数.
        $input = $this->request->getPost();
        $input['start_time'] = strtotime($input['start_time']);
        $input['end_time'] = strtotime($input['end_time']);

        // 是否同时间段存在事务.
        $userConflict = UserStatus::getConflict($input['user_id'],$input['start_time'],$input['end_time']);

        if ($userConflict === true) {
            $UserStatus = new UserStatus();

            // 创建.
            if ($UserStatus->create($input) === true) {
                return $this->ajaxSuccess('事件添加成功', 201);
            } else {
                return $this->ajaxError('事件添加失败, 请稍后重试');
            }
        } else {
            return $this->ajaxError('时间段内存在其它事件, 请合理安排');
        }
    }

}