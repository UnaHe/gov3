<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/5/2
 * Time: 15:27
 */

namespace app\Controllers\Admin;

use app\Models\AdminPermissionRole;
use app\Models\AdminPermissions;
use app\Models\AdminRoles;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

/**
 * 角色控制器
 * Class RolesController
 * @package app\Controller
 */
class RolesController  extends ControllerBase
{
    /*
     * 角色列表.
     */
    public function indexAction()
    {
        // 分页参数.
        $page = $this->request->get('page', 'int', 1);
        $limit = $this->request->get('limit', 'int', 10);

        $data = AdminRoles::find();

        $paginator = new PaginatorModel(
            [
                "data"  => $data,
                "limit" => $limit,
                "page"  => $page,
            ]
        );

        // 获取分页数据.
        $roles = $paginator->getPaginate();

        $this->view->setVars([
            'roles' => $roles,
        ]);
    }

    /*
     * 添加角色.
     */
    public function createAction()
    {

    }

    /*
     * 保存角色.
     */
    public function saveAction()
    {
        // 获取参数.
        $input = $this->request->getPost();

        // 验证.
        if (!$input['name'] || !$input['code']|| !$input['description']) {
            $this->flashSession->warning('参数必填');
            return $this->response->redirect('admin/roles/create');
        }

        // 查询.
        $Role = AdminRoles::findFirst([
            'name = :name: OR code = :code:',
            'bind' => [
                'name' => $input['name'],
                'code' => $input['code'],
            ]
        ]);

        if ($Role !== false) {
            $this->flashSession->error('角色已存在');
            return $this->response->redirect('admin/roles/create');
        }

        // 添加权限.
        $AdminRoles = new AdminRoles();
        if ($AdminRoles->create($input) === true) {
            $this->flashSession->success('添加角色成功');

            return $this->response->redirect('admin/roles');
        } else {
            return $this->flash->error('添加角色失败，请稍后重试');
        }
    }

    /**
     * 编辑角色.
     * @param $roleId
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface|\Phalcon\Mvc\View
     */
    public function editAction($roleId)
    {
        // 查询数据.
        $role = AdminRoles::findFirst($roleId);

        if ($role === false || $roleId === '1') {
            $this->flashSession->error('数据不存在');

            return $this->response->redirect('admin/roles');
        }

        // 页面参数.
        $this->view->setVars([
            'role' => $role,
        ]);

        return $this->view->pick('roles/create');
    }

    /**
     * 更新.
     */
    public function updateAction()
    {
        $input = $this->request->getPost();

        // 查询.
        $role = AdminRoles::findFirst($input['id']);

        if ($role === false) {
            $this->flashSession->error('数据不存在');

            return $this->response->redirect('admin/roles');
        }

        // 更新.
        if ($role->update($input) === true) {
            $this->flashSession->success('修改角色信息成功');

            return $this->response->redirect('admin/roles');
        } else {
            return $this->flash->error('修改角色信息失败，请稍后重试');
        }
    }

    /**
     * 删除角色.
     */
    public function deleteAction()
    {
        $roleId = $this->request->getPost('id');

        // 查询.
        $role = AdminRoles::findFirst($roleId);

        if ($role === false) {
            $this->flash->error('数据不存在');
            return $this->dispatcher->forward(
                [
                    'namespace'  => 'app\permissions',
                    'controller' => 'roles',
                    'action'     => 'index',
                ]
            );
        }

        // 删除.
        if ($role->delete() === true) {
            return $this->ajaxSuccess('角色删除成功', 201);
        } else {
            return $this->ajaxError('角色删除失败，请稍后重试');
        }
    }

    /**
     * 角色权限列表.
     * @param $roleId
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface|\Phalcon\Mvc\View
     */
    public function permissionAction($roleId)
    {
        if (!$roleId || $roleId === '1') {
            $this->flashSession->error('数据不存在');

            return $this->response->redirect('admin/roles');
        }

        $permissions = AdminPermissions::find();
        $myPermissions = AdminPermissionRole::find([
            'role_id = :role_id:',
            'bind' => [
                'role_id' => $roleId
            ]
        ]);

        $myPermissionsArray = [];
        foreach ($myPermissions as $k => $v) {
            $myPermissionsArray[$v->permission_id] = $v->permission_id;
        }

        // 页面参数.
        return $this->view->setVars([
            'roleId' => $roleId,
            'permissions' => $permissions,
            'myPermissionsArray' => $myPermissionsArray,
        ]);
    }

    /**
     * 保存角色权限.
     */
    public function savePermissionAction()
    {
        $roleId = $this->request->getPost('role_id');
        $permissions = $this->request->getPost('permissions');

        // 验证.
        if (!$roleId || $roleId === '1') {
            $this->flashSession->error('数据不存在');

            return $this->response->redirect('admin/roles');
        }

        // 执行修改.
        $res = (new AdminPermissionRole())->saveRolePermission($roleId, $permissions);

        if ($res) {
            $this->flashSession->success('权限保存成功');
            $this->logger->error($this->getCname() . '---' . $res);
            return $this->response->redirect('admin/roles');
        } else {
            return $this->flash->error('权限保存失败，请稍后重试');
        }
    }

}