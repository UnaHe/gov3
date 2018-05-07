<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/19
 * Time: 9:37
 */

namespace app\Controllers\Admin;

use app\Models\DepartmentNotices;
use app\Models\Departments;
use app\Models\Notices;
use app\Models\Project;

/**
 * 告示控制器
 * Class NoticeController
 * @package app\Controller
 */
class NoticeController extends ControllerBase
{
    /**
     * 留言列表.
     */
    public function indexAction()
    {
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

        $data['input'] = $input;
        if ($user['user_is_super'] || ($user['user_is_admin'] && $user['project_id'] == '')) {
            $data['project_list'] = Project::getProjectList();
            if (!empty($input['project_id'])) {
                $data['department_list'] = (new Departments())->getTree(0, 0, $input['project_id']);
            }
        } else {
            $input['project_id'] =  $user['project_id'];
            if ($user['user_is_admin']) {
                $data['department_list'] = (new Departments())->getTree(0, 0, $user['project_id']);
            } else {
                $input['department_id'] = $user['department_id'];
            }
        }
        $data['list'] = (new Notices())->getList($input, $page, $limit);

        // 页面参数.
        $this->view->setVars([
            'data' => $data,
            'input' => $input,
        ]);
    }

    /**
     * 编辑公告部门.
     */
    public function ajaxGetDepartmentsAction()
    {
        $input = $this->request->getPost();

        // 规范参数, 避免查询出错.
        foreach ($input as $k => $v){
            if ($v === '') {
                $input[$k] = NULL;
            }
        }

        $user = $this->session->get('user');

        $department_list =  $used_department_list = [];
        if($user['user_is_super'] || ($user['user_is_admin'] && $user['project_id'] == '')) {
            $departments = Departments::find([
                'project_id = :project_id:',
                'columns' => 'department_id, department_name',
                'bind' => [
                    'project_id' => $input['project_id']
                ],
            ])->toArray();

            foreach ($departments as $department) {
                $department_list[$department['department_id']] = $department;
            }
        }else{
            $department_list [$user['department_id']] = [
                'department_id'=>$user['department_id'],
                'department_name'=>$user['department_name'],
            ];
        }

        $used_departments = DepartmentNotices::find([
            'notice_id = :notice_id:',
            'bind' => [
                'notice_id' => $input['notice_id']
            ]
        ])->toArray();

        foreach ($used_departments as $department) {
            $used_department_list[$department['department_id']] = $department['department_id'];
        }

        $data = [
            'department_list' => $department_list,
            'used_department_list' => $used_department_list
        ];

        if ($department_list) {
            return $this->ajaxSuccess($data, 200);
        } else {
            return $this->ajaxError('系统错误，请稍后重试');
        }
    }

    /**
     * 保存公告部门列表.
     */
    public function updateNoticeDepartmentAction()
    {
        // 获取参数.
        $input = $this->request->getPost();
        $input['departments'] = isset($input['departments']) ? $input['departments'] : [];

        $res = (new Notices())->updateDepartmentNotice($input['notice_id'],$input['departments']);

        if ($res) {
            return $this->ajaxSuccess('保存公告部门列表成功', 201);
        } else {
            $this->logger->error($this->getCname() . '---' . $res);
            return $this->ajaxError('保存公告部门列表失败，请稍后重试');
        }

    }

    /**
     * 删除公告.
     * @throws \Exception
     */
    public function deleteAction()
    {
        // 获取参数.
        $noticeId = $this->request->getPost('notice_id');

        // 开启事务.
        $this->db->begin();

        try{

            // 关联表.
            $Dn = DepartmentNotices::find([
               'notice_id = :notice_id:',
               'bind' => [
                   'notice_id' => $noticeId,
               ],
            ]);

            foreach ($Dn as $v) {
                if ($v->delete() === false) {
                    throw new \LogicException('删除关联表失败');
                }
            }

            // 公告表.
            $Notice = Notices::findFirst([
                'notice_id = :notice_id:',
                'bind' => [
                    'notice_id' => $noticeId,
                ],
            ]);

            if ($Notice->delete() === false) {
                throw new \LogicException('删除公告失败');
            }

            // 保存.
            $this->db->commit();
            return $this->ajaxSuccess('删除成功', 201);
        }catch (\Exception $e){
            $this->db->rollback();
            $error = $e instanceof \LogicException ? $e->getMessage() : '删除失败，请稍后重试';
            return $this->ajaxError($error);
        }
    }

    /**
     * 显示详情.
     * @throws \Exception
     */
    public function showAction()
    {
        $noticeId = $this->request->getPost('notice_id');

        $Notice = Notices::findFirst([
            'notice_id = :notice_id:',
            'bind' => [
                'notice_id' => $noticeId,
            ],
        ]);

        if ($Notice !== false) {
            return $this->ajaxSuccess($Notice->notice_content, 201);
        } else {
            return $this->ajaxError('获取详情失败，请稍后重试');
        }
    }

    /**
     * 告示状态变更.
     */
    public function changeStatusAction()
    {
        // 获取参数.
        $noticeId = $this->request->getPost('notice_id');
        $noticeStatus = $this->request->getPost('notice_status');

        // 查询.
        $notice = Notices::findFirst([
            'notice_id = :notice_id:',
            'bind' => [
                'notice_id' => $noticeId
            ],
        ]);

        if ($notice === false) {
            return $this->ajaxError('告示不存在');
        } else {
            // 执行变更.
            if ($notice->update(['notice_status' => $noticeStatus]) === false) {
                return $this->ajaxError('状态变更失败，请稍后重试');
            } else {
                return $this->ajaxSuccess('状态已变更', 201);
            }
        }
    }

    /**
     * 添加告示.
     */
    public function createAction()
    {
        $user = $this->session->get('user');

        $department_list = [];
        if ($user['user_is_super'] || ($user['user_is_admin'] && $user['project_id'] == '')) {
            $project = Project::getProjectList();
        } else {
            $project = $user['project_name'];
            if($user['user_is_admin']) {
                $department_list = Departments::find([
                    'project_id = :project_id:',
                    'columns' => 'department_id, department_name',
                    'bind' => [
                        'project_id' => $user['project_id'],
                    ]
                ])->toArray();
            }else{
                $department_list [] = [
                    'department_id' => $user['department_id'],
                    'department_name' => $user['department_name'],
                ];
            }
        }

        // 页面参数.
        $this->view->setVars([
            'project' => $project,
            'department_list' => $department_list,
        ]);
    }

    /**
     * 保存告示.
     */
    public function saveAction()
    {
        $projectId = $this->request->getPost('project_id');
        $departments = !empty($this->request->getPost('departments')) ? $this->request->getPost('departments') : [];
        $noticeTitle = $this->request->getPost('notice_title');
        $noticeStatus = $this->request->getPost('notice_status');
        $noticeContent = $this->request->getPost('notice_content');

        // 验证.
        if (!$projectId || !$noticeTitle) {
            $this->flash->warning('参数错误');
            return $this->dispatcher->forward(
                [
                    'namespace'  => 'app\Controllers\Admin',
                    'controller' => 'notice',
                    'action'     => 'create',
                ]
            );
        }

        if (mb_strlen($noticeTitle, 'UTF-8') > 100) {
            $this->flash->warning('名称长度 1 - 100 个字符');
            return $this->dispatcher->forward(
                [
                    'namespace'  => 'app\Controllers\Admin',
                    'controller' => 'notice',
                    'action'     => 'create',
                ]
            );
        }

        // 准备参数.
        $user = $this->session->get('user');

        $param = [
            'project_id' => $user['project_id'] ? $user['project_id'] : $projectId,
            'notice_title' => $noticeTitle,
            'notice_content' => $noticeContent ? $noticeContent : '',
            'notice_status' => $noticeStatus,
            'created_user' => $user['user_id'],
        ];

        // 执行.
        $res = Notices::addNotice($param, $departments);

        if ($res) {
            $this->flashSession->success('创建成功');

            return $this->response->redirect('admin/notice');
        } else {
            return $this->flash->error('创建失败，请稍后重试');
        }
    }

    /**
     * 编辑告示详情.
     */
    public function editAction($noticeId)
    {
        $user = $this->session->get('user');

        if ($user['user_is_super'] || ($user['user_is_admin'] && $user['project_id'] == '')) {
            $project = Project::getProjectList();
        } else {
            $project = $user['project_name'];
        }

        $department_list = $used_department_list = [];

        $notice = Notices::findFirst($noticeId);

        if($user['user_is_super'] || ($user['user_is_admin'] && $user['project_id'] == '')) {
            $departments = Departments::find([
                'project_id = :project_id:',
                'columns' => 'department_id, department_name',
                'bind' => [
                    'project_id' => $notice->project_id,
                ]
            ])->toArray();

            foreach ($departments as $department) {
                $department_list[$department['department_id']] = $department;
            }
        }else{
            $department_list [$user['department_id']] = [
                'department_id' => $user['department_id'],
                'department_name' => $user['department_name'],
            ];
        }

        $used_departments =  $departments = DepartmentNotices::find([
            'notice_id = :notice_id:',
            'bind' => [
                'notice_id' => $noticeId,
            ]
        ])->toArray();

        foreach ($used_departments as $department) {
            $used_department_list[$department['department_id']] = $department['department_id'];
        }

        $this->view->setVars([
            'project' => $project,
            'notice' => $notice,
            'department_list' => $department_list,
            'used_department_list' => $used_department_list,
        ]);
    }

    /**
     * 更新.
     */
    public function updateAction()
    {
        $noticeId = $this->request->getPost('notice_id');
        $projectId = $this->request->getPost('project_id');
        $departments = !empty($this->request->getPost('departments')) ? $this->request->getPost('departments') : [];
        $noticeTitle = $this->request->getPost('notice_title');
        $noticeStatus = $this->request->getPost('notice_status');
        $noticeContent = $this->request->getPost('notice_content');

        // 验证.
        if (!$projectId || !$noticeTitle) {
            $this->flash->warning('参数错误');
            return $this->dispatcher->forward(
                [
                    'namespace'  => 'app\Controllers\Admin',
                    'controller' => 'notice',
                    'action'     => 'create',
                ]
            );
        }

        if (mb_strlen($noticeTitle, 'UTF-8') > 100) {
            $this->flash->warning('名称长度 1 - 100 个字符');
            return $this->dispatcher->forward(
                [
                    'namespace'  => 'app\Controllers\Admin',
                    'controller' => 'notice',
                    'action'     => 'create',
                ]
            );
        }

        // 准备参数.
        $user = $this->session->get('user');

        $param = [
            'project_id' => $user['project_id'] ? $user['project_id'] : $projectId,
            'notice_title' => $noticeTitle,
            'notice_content' => $noticeContent ? $noticeContent : '',
            'notice_status' => $noticeStatus,
        ];

        // 执行.
        $res = Notices::updateNotice($noticeId, $param, $departments);

        if ($res) {
            $this->flashSession->success('更新成功');

            return $this->response->redirect('admin/notice');
        } else {
            return $this->flash->error('更新成功，请稍后重试');
        }
    }

}