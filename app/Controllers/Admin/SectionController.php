<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/23
 * Time: 18:16
 */

namespace app\Controllers\Admin;

use app\Models\Project;
use app\Models\Sections;

/**
 * 部门控制器
 * Class SectionController
 * @package app\Controller\Admin
 */
class SectionController extends ControllerBase
{
    /**
     * 部门列表.
     */
    public function indexAction()
    {
        // 获取数据.
        $user = $this->session->get('user');
        $input = $this->request->getQuery();

        $data = [];
        $projects = [];

        // 获取列表.
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

        $sections = (new Sections())->getDetailTree(0, 0, $input['project_id']);

        // 页面参数.
        $this->view->setVars([
            'data' => $data,
            'sections' => $sections,
            'input' => $input
        ]);
    }

    /**
     * 添加部门页面.
     */
    public function createAction()
    {
        // 获取用户.
        $user = $this->session->get('user');

        // 权限.
        if ($user['user_is_super'] || ($user['user_is_admin'] && $user['project_id'] == '')) {
            $project = Project::getProjectList();
            $section_list = [];
        } else {
            $section_list = (new Sections())->getTree(0, 0, $user['project_id']);
            $project = [
                'project_id' => $user['project_id'],
                'project_name' => $user['project_name'],
            ];
        }

        // 页面参数.
        $this->view->setVars([
            'section_list' => $section_list,
            'project' => $project,
        ]);
    }

    /**
     * 保存部门.
     */
    public function saveAction()
    {
        // 获取用户.
        $user = $this->session->get('user');

        // 获取参数.
        $projectId = !empty($user['project_id']) ? $user['project_id'] : $this->request->getPost('project_id');
        $parentId = $this->request->getPost('parent_id');
        $sectionName = $this->request->getPost('section_name');

        // 验证.
        if (!$projectId) {
            $this->flash->warning('请选择单位');
            return $this->dispatcher->forward(
                [
                    'namespace'  => 'app\Controllers\Admin',
                    'controller' => 'section',
                    'action'     => 'create',
                ]
            );
        }

        if (empty($sectionName) || mb_strlen($sectionName, 'UTF-8') > 100) {
            $this->flash->warning('名称长度 1 - 100 个字符');
            return $this->dispatcher->forward(
                [
                    'namespace'  => 'app\Controllers\Admin',
                    'controller' => 'section',
                    'action'     => 'create',
                ]
            );
        }

        if ($parentId === NULL || $parentId === '0') {
            $unit = Sections::findFirst([
                'section_name = :section_name: AND project_id = :project_id:',
                'bind' => [
                    'section_name' => $sectionName,
                    'project_id' => $projectId,
                ]
            ]);
        } else {
            $unit = Sections::findFirst([
                'section_name = :section_name: AND parent_id = :parent_id: AND project_id = :project_id:',
                'bind' => [
                    'section_name' => $sectionName,
                    'parent_id' => $parentId,
                    'project_id' => $projectId,
                ]
            ]);
        }

        if ($unit !== false) {
            $this->flash->error('部门名已经存在');
            return $this->dispatcher->forward(
                [
                    'namespace'  => 'app\Controllers\Admin',
                    'controller' => 'section',
                    'action'     => 'create',
                ]
            );
        }

        $data = [
            'section_name' => $sectionName,
            'parent_id' => $parentId,
            'project_id' => $projectId,
        ];

        $Sections = new Sections();
        if ($Sections->create($data) === true) {
            $this->flashSession->success('创建成功');

            return $this->response->redirect('admin/section?project_id=' . $projectId);
        } else {
            return $this->flash->error('部门创建失败，请稍后重试');
        }
    }

    /**
     * 修改部门信息.
     * @param $sectionId
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface|\Phalcon\Mvc\View
     */
    public function editAction($sectionId)
    {
        // 查找.
        $section = Sections::findFirst($sectionId);

        if ($section === false) {
            $this->flashSession->error('部门不存在');

            return $this->response->redirect('admin/section');
        }

        $data = (new Sections())->getTree(0, 0, $section->project_id, $section->section_id);

        // 页面参数.
        return $this->view->setVars([
            'section' => $section,
            'data' => $data,
        ]);
    }

    /**
     * 更新部门信息.
     */
    public function updateAction()
    {
        // 获取数据.
        $sectionId = $this->request->getPost('section_id');
        $projectId = $this->request->getPost('project_id');
        $parentId = $this->request->getPost('parent_id');
        $sectionName = $this->request->getPost('section_name');

        $section = Sections::findFirst($sectionId);

        if ($section === false) {
            $this->flashSession->error('部门不存在');

            return $this->response->redirect('admin/section');
        }

        // 准备数据.
        $data = [
            'parent_id' => $parentId,
            'section_name' => $sectionName,
        ];

        // 执行更新.
        if ($section->update($data) === true) {
            $this->flashSession->success('部门更新成功');

            return $this->response->redirect('admin/section?project_id=' . $projectId);
        } else {
            return $this->flash->error('部门更新失败，请稍后重试');
        }
    }

    /**
     * 获取部门人员.
     */
    public function ajaxGetUsersBySectionOrOthersAction()
    {
        // 获取参数
        $input = $this->request->getPost();

        $data = Sections::GetUsersBySectionOrOthers($input['project_id'], $input['section_id']);

        if ($data) {
            return $this->ajaxSuccess($data);
        } else {
            return $this->ajaxError('操作失败，请稍后重试');
        }
    }

    /**
     * 修改部门人员.
     */
    public function updateUsersSectionAction()
    {
        // 获取参数
        $sectionId = $this->request->getPost('section_id');
        $users = $this->request->getPost('users');

        $data = Sections::updateUsersSection($sectionId, $users);

        if ($data !== true) {
            $this->logger->error($this->getCname() . '---' . $data);
            return $this->ajaxError('操作失败，请稍后重试');
        } else {
            return $this->ajaxSuccess('操作成功', 201);
        }
    }

    /**
     * 删除部门.
     */
    public function deleteAction()
    {
        // 获取参数.
        $sectionId = $this->request->getPost('section_id');

        // 科室下是否存在子科室.
        $child = Sections::find([
            'parent_id = :parent_id:',
            'bind' =>[
                'parent_id' => $sectionId
            ]
        ])->toArray();

        if (!empty($child)) {
            $str = '';
            foreach ($child as $v) {
                $str .= $v['section_name'] . ',';
            }

            return $this->ajaxError('删除失败,存在下级部门:' . $str,406);
        }

        // 查找科室.
        $section = Sections::findFirst($sectionId);

        // 执行删除.
        if ($section->delete() !== false) {
            return $this->ajaxSuccess('删除成功', 201);
        } else {
            return $this->ajaxError('部门删除失败，请稍后重试');
        }
    }

}