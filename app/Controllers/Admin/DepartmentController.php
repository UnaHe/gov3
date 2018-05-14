<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/17
 * Time: 14:26
 */

namespace app\Controllers\Admin;

use app\Models\Departments;
use app\Models\Project;
use app\Models\Users;

/**
 * 科室控制器
 * Class DepartmentController
 * @package app\Controller\Admin
 */
class DepartmentController extends ControllerBase
{
    /**
     * 科室列表.
     */
    public function indexAction()
    {
        // 获取用户.
        $user = $this->session->get('user');
        $input = $this->request->getQuery();

        $data = [];
        $projects = [];

        // 权限.
        if ($user['user_is_super'] || ($user['user_is_admin'] && $user['project_id'] == '')) {
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
        $departments = (new Departments())->getDetailTree(0, 0, $input['project_id']);

        // 页面参数.
        $this->view->setVars([
            'data' => $data,
            'input' => $input,
            'departments' => $departments,
        ]);
    }

    /**
     * 添加科室.
     */
    public function createAction()
    {
        // 获取用户.
        $user = $this->session->get('user');

        // 权限.
        if ($user['user_is_super'] || ($user['user_is_admin'] && $user['project_id'] == '')) {
            $project = Project::getProjectList();
            $department_list = [];
        } else {
            $department_list = (new Departments())->getTree(0, 0, $user['project_id']);
            $project = [
                'project_id' => $user['project_id'],
                'project_name' => $user['project_name'],
            ];
        }

        // 页面参数.
        $this->view->setVars([
            'department_list' => $department_list,
            'project' => $project,
        ]);
    }

    /**
     * 保存科室.
     */
    public function saveAction()
    {
        // 获取用户.
        $user = $this->session->get('user');

        // 获取参数.
        $projectId = !empty($user['project_id']) ? $user['project_id'] : $this->request->getPost('project_id');
        $parentId = $this->request->getPost('parent_id');
        $departmentName = $this->request->getPost('department_name');
        $departmentDesc = $this->request->getPost('department_desc');

        // 验证.
        if (!$projectId) {
            $this->flash->warning('请选择单位');
            return $this->dispatcher->forward(
                [
                    'namespace'  => 'app\Controllers\Admin',
                    'controller' => 'department',
                    'action'     => 'create',
                ]
            );
        }

        if (empty($departmentName) || mb_strlen($departmentName, 'UTF-8') > 100) {
            $this->flash->warning('名称长度 1 - 100 个字符');
            return $this->dispatcher->forward(
                [
                    'namespace'  => 'app\Controllers\Admin',
                    'controller' => 'department',
                    'action'     => 'create',
                ]
            );
        }

        if ($parentId === NULL || $parentId === '0') {
            $unit = Departments::findFirst([
                'department_name = :department_name: AND project_id = :project_id:',
                'bind' => [
                    'department_name' => $departmentName,
                    'project_id' => $projectId,
                ]
            ]);
        } else {
            $unit = Departments::findFirst([
                'department_name = :department_name: AND parent_id = :parent_id: AND project_id = :project_id:',
                'bind' => [
                    'department_name' => $departmentName,
                    'parent_id' => $parentId,
                    'project_id' => $projectId,
                ]
            ]);
        }

        if ($unit !== false) {
            $this->flash->error('科室名已经存在');
            return $this->dispatcher->forward(
                [
                    'namespace'  => 'app\Controllers\Admin',
                    'controller' => 'department',
                    'action'     => 'create',
                ]
            );
        }

        $data = [
            'department_name' => $departmentName,
            'parent_id' => $parentId,
            'project_id' => $projectId,
            'department_desc' => $departmentDesc,
        ];

        $Departments = new Departments();
        if ($Departments->create($data) === true) {
            $this->flashSession->success('创建成功');

            return $this->response->redirect('admin/department?project_id=' . $projectId);
        } else {
            return $this->flash->error('科室创建失败，请稍后重试');
        }
    }

    /**
     * 显示详情.
     */
    public function showAction()
    {
        $departmentId = $this->request->getPost('department_id');
        // 查找单位.
        $Department = Departments::findFirst($departmentId);
        if ($Department !== false) {
            $data = $Department->toArray();
            return $this->ajaxSuccess($data['department_desc']);
        } else {
            return $this->ajaxError('暂无法获取，请稍后重试');
        }
    }

    /**
     * 编辑科室详情.
     * @param $id
     * @return bool|\Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function editAction($id)
    {
        // 查找数据.
        $unit = Departments::findFirst($id);

        if ($unit === false) {
            $this->flashSession->error('科室不存在');

            return $this->response->redirect('admin/department');
        }

        $data = (new Departments())->getTree(0, 0, $unit->project_id, $unit->department_id);

        // 页面参数.
        $this->view->setVars([
            'unit' => $unit,
            'data' => $data,
        ]);

        return true;
    }

    /**
     * 更新科室详情.
     */
    public function updateAction()
    {
        $departmentId = $this->request->getPost('department_id');

        // 查找数据.
        $Department = Departments::findFirst($departmentId);

        if ($Department === false) {
            $this->flashSession->error('科室不存在');

            return $this->response->redirect('admin/department');
        }

        // 获取数据.
        $departmentName = $this->request->getPost('department_name');
        $parentId = $this->request->getPost('parent_id');
        $projectId = $this->request->getPost('project_id');
        $departmentDesc = $this->request->getPost('department_desc');

        // 验证.
        if (empty($departmentName) || mb_strlen($departmentName, 'UTF-8') > 100) {
            $this->flashSession->warning('名称最大长度 100 个字符');

            return $this->response->redirect('/admin/department/' . $departmentId . '/edit');
        }

        if ($parentId === NULL || $parentId === '0') {
            $unit = Departments::findFirst([
                'department_name = :department_name: AND project_id = :project_id:',
                'bind' => [
                    'department_name' => $departmentName,
                    'project_id' => $projectId,
                ]
            ]);
        } else {
            $unit = Departments::findFirst([
                'department_name = :department_name: AND parent_id = :parent_id: AND project_id = :project_id:',
                'bind' => [
                    'department_name' => $departmentName,
                    'parent_id' => $parentId,
                    'project_id' => $projectId,
                ]
            ]);
        }

        if ($unit !== false) {
            $this->flash->error('科室名已经存在');
            return $this->dispatcher->forward(
                [
                    'namespace'  => 'app\Controllers\Admin',
                    'controller' => 'department',
                    'action'     => 'create',
                ]
            );
        }

        $data = [
            'department_name' => $departmentName,
            'parent_id' => $parentId,
            'project_id' => $projectId,
            'department_desc' => $departmentDesc,
        ];

        if ($Department->update($data) === true) {
            $this->flashSession->success('更新成功');

            return $this->response->redirect('admin/department?project_id=' . $projectId);
        } else {
            return $this->flash->error('科室更新失败，请稍后重试');
        }

    }

    /**
     * 删除科室.
     */
    public function deleteAction()
    {
        // 获取参数.
        $departmentId = $this->request->getPost('department_id');

        // 科室下是否存在子科室.
        $child = Departments::find([
            'parent_id = :parent_id:',
            'bind' =>[
                'parent_id' => $departmentId
            ]
        ])->toArray();

        if (!empty($child)) {
            $str = '';
            foreach ($child as $v) {
                $str .= $v['department_name'] . ',';
            }

            return $this->ajaxError('删除失败,存在下级科室:' . $str,406);
        }

        // 查找科室.
        $department = Departments::findFirst($departmentId);

        // 执行删除.
        if ($department->delete() !== false) {
            return $this->ajaxSuccess('删除成功', 201);
        } else {
            return $this->ajaxError('科室删除失败，请稍后重试');
        }
    }

    /**
     * 获取单位所有人员.
     */
    public function ajaxGetUsersByDepartmentOrOthersAction()
    {
        // 获取参数
        $input = $this->request->getPost();

        $data = Departments::GetUsersByDepartmentOrOthers($input['project_id'], $input['department_id']);

        if ($data) {
            return $this->ajaxSuccess($data);
        } else {
            return $this->ajaxError('操作失败，请稍后重试');
        }
    }

    /**
     * 更新人员所在科室.
     */
    public function updateUsersDepartmentAction()
    {
        $departmentId = $this->request->getPost('department_id');
        $users = $this->request->getPost('users');

        $data = Departments::updateUsersDepartment($departmentId, $users);

        if ($data !== true) {
            $this->logger->error($this->getCname() . '---' . $data);
            return $this->ajaxError('操作失败，请稍后重试');
        } else {
            return $this->ajaxSuccess('操作成功', 201);
        }
    }

}