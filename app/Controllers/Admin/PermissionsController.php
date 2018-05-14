<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/5/2
 * Time: 14:10
 */

namespace app\Controllers\Admin;

use app\Models\AdminPermissions;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

/**
 * 权限控制器
 * Class PermissionsController
 * @package app\Controller\Admin
 */
class PermissionsController extends ControllerBase
{
    /*
     * 权限管理列表.
     */
    public function indexAction()
    {
        // 分页参数.
        $page = $this->request->get('page', 'int', 1);
        $limit = $this->request->get('limit', 'int', 10);

        $data = AdminPermissions::find();

        $paginator = new PaginatorModel(
            [
                "data"  => $data,
                "limit" => $limit,
                "page"  => $page,
            ]
        );

        // 获取分页数据.
        $permissions = $paginator->getPaginate();

        // 页面数据.
        $this->view->setVars([
            'permissions' => $permissions,
        ]);
    }

    /*
     * 添加权限.
     */
    public function createAction()
    {

    }

    /*
     * 保存权限.
     */
    public function saveAction()
    {
        // 获取数据.
        $input = $this->request->getPost();

        // 验证.
        if (!$input['name'] || !$input['description']) {
            $this->flashSession->warning('参数必填');
            return $this->response->redirect('admin/permissions/create');
        }

        $Permission = AdminPermissions::findFirst([
            'name = :name:',
            'bind' => [
                'name' => $input['name'],
            ]
        ]);

        if ($Permission !== false) {
            $this->flashSession->error('权限已存在');
            return $this->response->redirect('admin/permissions/create');
        }

        // 添加权限.
        $Permissions = new AdminPermissions();
        if ($Permissions->create($input) === true) {
            $this->flashSession->success('添加权限成功');

            return $this->response->redirect('admin/permissions');
        } else {
            return $this->flash->error('添加权限失败，请稍后重试');
        }
    }

    /**
     * 编辑权限信息.
     * @param $permissionId
     * @return bool|\Phalcon\Http\Response|\Phalcon\Http\ResponseInterface|\Phalcon\Mvc\View
     */
    public function editAction($permissionId)
    {
        // 查询数据.
        $permission = AdminPermissions::findFirst($permissionId);

        if ($permission === false) {
            $this->flashSession->error('数据不存在');

            return $this->response->redirect('admin/permissions');
        }

        // 页面参数.
        $this->view->setVars([
            'permission' => $permission,
        ]);

        $this->view->pick('permissions/create');

        return true;
    }

    /**
     * 更新.
     */
    public function updateAction()
    {
        $input = $this->request->getPost();

        // 验证.
        if (!$input['name'] || !$input['description']) {
            $this->flashSession->warning('参数必填');
            return $this->response->redirect('admin/permissions/' . $input['id'] . '/edit');
        }

        // 查询.
        $permission = AdminPermissions::findFirst($input['id']);

        if ($permission === false) {
            $this->flashSession->error('数据不存在');

            return $this->response->redirect('admin/permissions');
        }

        // 更新.
        if ($permission->update($input) === true) {
            $this->flashSession->success('修改权限信息成功');

            return $this->response->redirect('admin/permissions');
        } else {
            return $this->flash->error('修改权限信息失败，请稍后重试');
        }
    }

    /**
     * 删除权限.
     */
    public function deleteAction()
    {
        $permissionId = $this->request->getPost('id');

        // 查询.
        $permission = AdminPermissions::findFirst($permissionId);

        if ($permission === false) {
            $this->flash->error('数据不存在');
            return $this->dispatcher->forward(
                [
                    'namespace'  => 'app\Controllers\Admin',
                    'controller' => 'permissions',
                    'action'     => 'index',
                ]
            );
        }

        // 删除.
        if ($permission->delete() === true) {
            return $this->ajaxSuccess('权限删除成功', 201);
        } else {
            return $this->ajaxError('权限删除失败，请稍后重试');
        }
    }

}