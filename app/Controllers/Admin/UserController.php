<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/16
 * Time: 15:25
 */

namespace app\Controllers\Admin;

use app\Models\AdminRoles;
use app\Models\AdminRoleUser;
use app\Models\Departments;
use app\Models\Project;
use app\Models\Sections;
use app\Models\UserBelongs;
use app\Models\Users;
use Phalcon\Http\Response;

/**
 * 用户控制器
 * Class UserController
 * @package app\Controller\Admin
 */
class UserController extends ControllerBase
{
    /**
     * 人员列表.
     * @throws \Exception
     */
    public function indexAction()
    {
        // 参数.
        $page = $this->request->get('page', 'int', 1);
        $limit = $this->request->get('limit', 'int', 10);
        $input = $this->request->getQuery();

        // 规范参数, 避免查询出错.
        foreach ($input as $k => $v) {
            if ($v === '') {
                $input[$k] = NULL;
            }
        }

        $user = $this->session->get('user');

        if ($user['user_is_super'] && empty($user['project_id'])) {
            $data['project_list'] = Project::getProjectList();
            if (!empty($input['project_id'])) {
                $data['department_list'] = (new Departments())->getTree(0, 0, $input['project_id']);
                $data['section_list'] = (new Sections())->getTree(0, 0, $input['project_id']);
            }
        } else {
            $input['project_id'] = !empty($user['project_id']) ? $user['project_id'] : $input['project_id'];
            $data['department_list'] = (new Departments())->getTree(0, 0, $user['project_id']);
            $data['section_list'] = (new Sections())->getTree(0, 0, $input['project_id']);
        }

        $data['list'] = (new Users())->getRelationWithCategory($input, $page, $limit);

        // 页面参数.
        $this->view->setVars([
            'data' => $data,
            'input' => $input,
        ]);
    }

    /**
     * 新增用户.
     */
    public function createAction()
    {
        $user = $this->session->get('user');

        $department_list = $section_list = [];

        if ($user['user_is_super'] && empty($user['project_id'])) {
            $project = Project::getProjectList();
        } else {
            $project = $user['project_name'];
            $department_list = (new Departments())->getTree(0, 0, $user['project_id']);
            $section_list = (new Sections())->getTree(0, 0, $user['project_id']);
        }

        // 页面参数.
        $this->view->setVars([
            'project' => $project,
            'department_list' => $department_list,
            'section_list' => $section_list,
        ]);
    }

    /**
     * 保存用户.
     */
    public function saveAction()
    {
        $user = $this->session->get('user');

        // 获取数据.
        $input = $this->request->getPost();
        $input['user_pass'] = $input['user_pass'] ? $this->security->hash($input['user_pass']) : $this->security->hash('123456');
        $input['project_id'] = !empty($user['project_id']) ? $user['project_id'] : $input['project_id'];

        // 规范参数, 避免查询出错.
        foreach ($input as $k => $v) {
            if ($v === '') {
                $input[$k] = NULL;
            }
        }

        // 验证.
        if (mb_strlen($input['user_name'], 'UTF-8') < 2 || mb_strlen($input['user_name'], 'UTF-8') > 18) {
            $this->flash->warning('名称长度 2 - 18 个字符');
            return $this->dispatcher->forward(
                [
                    'namespace' => 'app\Controllers\Admin',
                    'controller' => 'user',
                    'action' => 'create',
                ]
            );
        }
        if ($input['user_age'] <= 0 || $input['user_age'] >= 100) {
            $this->flash->warning('请输入正确的年龄');
            return $this->dispatcher->forward(
                [
                    'namespace' => 'app\Controllers\Admin',
                    'controller' => 'user',
                    'action' => 'create',
                ]
            );
        }
        if ($input['user_sex'] === NULL) {
            $this->flash->warning('请选择性别');
            return $this->dispatcher->forward(
                [
                    'namespace' => 'app\Controllers\Admin',
                    'controller' => 'user',
                    'action' => 'create',
                ]
            );
        }
        if (!preg_match('/^1[3456789]{1}\d{9}$/', $input['user_phone'])) {
            $this->flash->warning('手机号格式不正确');
            return $this->dispatcher->forward(
                [
                    'namespace' => 'app\Controllers\Admin',
                    'controller' => 'user',
                    'action' => 'create',
                ]
            );
        }
        $issetUser = Users::findFirst([
            'user_phone = :user_phone:',
            'bind' => [
                'user_phone' => $input['user_phone']
            ]
        ]);
        if ($issetUser !== false) {
            $this->flash->warning('手机号码已存在');
            return $this->dispatcher->forward(
                [
                    'namespace' => 'app\Controllers\Admin',
                    'controller' => 'user',
                    'action' => 'create',
                ]
            );
        }

        // 创建用户.
        $Users = new Users();
        if ($Users->create($input) === true) {
            $this->flashSession->success('创建成功');

            return $this->response->redirect('admin/users?project_id=' . $input['project_id']);
        } else {
            return $this->flash->error('用户创建失败，请稍后重试');
        }
    }

    /**
     * 修改用户信息.
     * @param $userId
     * @return Response|\Phalcon\Http\ResponseInterface|\Phalcon\Mvc\View
     */
    public function editAction($userId)
    {
        $field = (new Users())->getDetailById($userId);

        if ($field === false) {
            $this->flashSession->error('用户不存在');

            return $this->response->redirect('admin/users');
        }

        $department_list = (new Departments())->getTree(0, 0, $field['project_id']);
        $section_list = (new Sections())->getTree(0, 0, $field['project_id']);

        // 页面参数.
        return $this->view->setVars([
            'field' => $field,
            'department_list' => $department_list,
            'section_list' => $section_list,
        ]);
    }

    /**
     * 更新用户信息.
     */
    public function updateAction()
    {
        // 获取数据.
        $input = $this->request->getPost();

        // 规范参数, 避免查询出错.
        foreach ($input as $k => $v) {
            if ($v === '') {
                $input[$k] = NULL;
            }
        }

        // 查找数据.
        $users = Users::findFirst($input['user_id']);

        if ($users === false) {
            $this->flashSession->error('用户不存在');

            return $this->response->redirect('admin/users');
        }

        // 验证.
        if (mb_strlen($input['user_name'], 'UTF-8') < 2 || mb_strlen($input['user_name'], 'UTF-8') > 18) {
            $this->flashSession->warning('名称长度 2 - 18 个字符');
            return $this->response->redirect('/admin/users/' . $input['user_id'] . '/edit');
        }
        if ($input['user_age'] <= 0 || $input['user_age'] >= 100) {
            $this->flashSession->warning('请输入正确的年龄');
            return $this->response->redirect('/admin/users/' . $input['user_id'] . '/edit');
        }
        if ($input['user_sex'] === NULL) {
            $this->flashSession->warning('请选择性别');
            return $this->response->redirect('/admin/users/' . $input['user_id'] . '/edit');
        }
        if (!preg_match('/^1[3456789]{1}\d{9}$/', $input['user_phone'])) {
            $this->flashSession->warning('手机号格式不正确');
            return $this->response->redirect('/admin/users/' . $input['user_id'] . '/edit');
        }
        $issetUser = Users::findFirst([
            'user_phone = :user_phone: AND user_id <> :user_id:',
            'bind' => [
                'user_phone' => $input['user_phone'],
                'user_id' => $input['user_id'],
            ]
        ]);
        if ($issetUser !== false) {
            $this->flashSession->warning('手机号码已存在');
            return $this->response->redirect('/admin/users/' . $input['user_id'] . '/edit');
        }

        // 更新.
        $oldImg = $users->user_image;
        if ($users->update($input) === true) {
            // 判断是否删除图片.
            if ($input['user_image'] && $oldImg) {
                // 都存在, 删除旧图.
                @unlink($this->config->img['upload_path'] . $oldImg);
            } else if (!$input['user_image'] && $oldImg) {
                // 没新图, 移除旧图, 删除旧图.
                @unlink($this->config->img['upload_path'] . $oldImg);
            }

            $this->flashSession->success('更新成功');
            return $this->response->redirect('admin/users');
        } else {
            return $this->flash->error('用户更新失败，请稍后重试');
        }
    }

    /**
     * 删除用户.
     * @throws \Exception
     */
    public function deleteAction()
    {
        $userId = $this->request->getPost('user_id');

        $user = $this->session->get('user');

        $count = Users::count([
            'project_id = :project_id: AND user_is_admin = "1"',
            'bind' => [
                'project_id' => $user['project_id'],
            ]
        ]);

        if ($userId == $user['user_id'] || $count <= 1) {
            return $this->ajaxError('唯一管理员无法删除');
        }

        $res = Users::deleteAdmin($userId);

        if ($res !== true) {
            $this->logger->error($this->getCname() . '---' . $res);
            return $this->ajaxError('删除失败，请稍后重试');
        } else {
            return $this->ajaxSuccess('删除成功', 201);
        }
    }


    /**
     * 重置密码.
     * return string
     */
    public function resetPwdAction()
    {
        // 获取参数.
        $userId = $this->request->getPost('user_id');

        //查询用户.
        $user = Users::findFirst([
            'user_id = :user_id:',
            'bind' => [
                "user_id" => $userId
            ]
        ]);
        if ($user === false) {
            return $this->ajaxError('用户不存在');
        }

        // 执行修改.
        $res = $user->update(['user_pass' => $this->security->hash('123456')]);

        if ($res === true) {
            return $this->ajaxSuccess('密码已重置', 201);
        } else {
            return $this->ajaxError('密码重置失败, 请稍后在试');
        }

    }

    /**
     * 新增归属.
     */
    public function addBelongAction()
    {
        if ($this->request->isPost()) {
            // 获取数据.
            $input = $this->request->get();

            // 规范参数, 避免查询出错.
            foreach ($input as $k => $v) {
                if ($v === '') {
                    $input[$k] = NULL;
                }
            }

            $param = !empty($input['users']) ? array_unique($input['users']) : [];

            $res = (new UserBelongs())->addBelong($input['user_id'], $param);

            if ($res) {
                $this->flashSession->success('操作成功');
                return $this->response->redirect('admin/users/belongs');
            } else {
                $this->logger->error($this->getCname() . '---' . $res);
                return $this->flash->error('操作失败, 请稍后重试');
            }
        } else {
            $userId = $this->request->get('user_id');
            if ($userId) {
                // 绑定主体信息.
                $users = Users::findFirst([
                    'user_id = :user_id:',
                    'bind' => [
                        'user_id' => $userId
                    ],
                    'columns' => 'user_id, user_name, project_id, user_job'
                ]);
                $builder = (new UserBelongs())->getModelsManager()->createBuilder()->addFrom('app\Models\UserBelongs', 'user_belongs');
                $builder->columns('users.user_id, users.user_name');
                $builder->leftJoin('app\Models\Users', 'user_belongs.user_id = users.user_id', 'users');
                $builder->where('user_belongs.belong_id = :belong_id:', [
                    'belong_id' => $userId
                ]);
                $old_s = $builder->getQuery()->execute();
                $old_list = [];
                foreach ($old_s as $v) {
                    $old_list[$v->user_id] = [
                        'user_id' => $v->user_id,
                        'user_name' => $v->user_name,
                    ];
                }

                // 科室列表.
                $department_list = (new Departments())->getTree(0, 0, $users->project_id);

                // 查询该项目的用户列表.
                $list = (new Users())->getProjectUsersByProject(['project_id' => $users->project_id], false);

                $user_list_by_dp = [];
                $user_list_by_name = [];
                $first_chars = [];
                foreach ($list as $v) {
                    if ($v->user_id == $userId) {
                        continue;
                    }

                    // 组装按科室排序的列表.
                    $user_list_by_dp[$v->department_id]['department_name'] = $v->department_name;
                    $user_list_by_dp[$v->department_id]['list'][] = [
                        'user_id' => $v->user_id,
                        'user_name' => $v->user_name,
                        'department_name' => $v->department_name
                    ];

                    // 组装按姓名首字母排序的列表.
                    $first_char = $this->getFirstCharter($v->user_name);
                    $first_char = $first_char ? $first_char : 'zother'; // 无法识别的.
                    $first_chars[$first_char] = $first_char;
                    $user_list_by_name[$first_char]['first_char'] = $first_char;
                    $user_list_by_name[$first_char]['list'][] = [
                        'user_id' => $v->user_id,
                        'user_name' => $v->user_name,
                        'department_name' => $v->department_name
                    ];
                }

                ksort($user_list_by_name);
                ksort($first_chars);
                $first_chars['zother'] = 'zother';

                // 页面参数.
                return $this->view->setVars([
                    'user' => $users,
                    'old_list' => $old_list,
                    'department_list' => $department_list,
                    'user_list_by_dp' => $user_list_by_dp,
                    'user_list_by_name' => $user_list_by_name,
                    'first_chars' => $first_chars
                ]);
            }
        }

        $this->flashSession->success('系统错误, 请稍后重试');
        return $this->response->redirect('admin/users/belongs');
    }

    /**
     * 人员归属关系列表.
     */
    public function belongsAction()
    {
        // 获取参数.
        $page = $this->request->get('page', 'int', 1);
        $limit = $this->request->get('limit', 'int', 10);
        $input = $this->request->getQuery();

        $user = $this->session->get('user');

        if ($user['user_is_super'] && empty($user['project_id'])) {
            $data['project_list'] = Project::getProjectList();
            if (!empty($input['project_id'])) {
                $data['department_list'] = (new Departments())->getTree(0, 0, $input['project_id']);
            }
        } else {
            $input['project_id'] = $user['project_id'];
            $data['department_list'] = (new Departments())->getTree(0, 0, $user['project_id']);
        }

        $data['list'] = (new UserBelongs())->getList($input, $page, $limit);

        // 页面参数.
        $this->view->setVars([
            'data' => $data,
            'input' => $input,
        ]);
    }

    /**
     * 删除归属.
     * @throws \Exception
     */
    public function belongsDeleteAction()
    {
        $belongId = $this->request->getPost('belong_id');

        $res = (new UserBelongs())->belongsDelete($belongId);

        if ($res !== true) {
            $this->logger->error($this->getCname() . '---' . $res);
            return $this->ajaxError('删除失败，请稍后重试');
        } else {
            return $this->ajaxSuccess('删除成功', 201);
        }
    }

    /**
     * 角色管理.
     * @param $userId
     * @return mixed
     */
    public function roleAction($userId)
    {
        $roles = AdminRoles::find(['code != "administrator"']);

        $userRole = AdminRoleUser::findFirst([
            'user_id = :user_id:',
            'bind' => [
                'user_id' => $userId
            ]
        ]);

        if ($userRole === false) {
            $this->flashSession->error('用户不存在');
            return $this->response->redirect('admin/users');
        }

        // 页面参数.
        return $this->view->setVars([
            'roles' => $roles,
            'userRole' => $userRole,
        ]);
    }

    /**
     * 保存角色.
     */
    public function roleSaveAction()
    {
        $userId = $this->request->getPost('user_id');
        $role = $this->request->getPost('role');

        $userRole = AdminRoleUser::findFirst([
            'user_id = :user_id:',
            'bind' => [
                'user_id' => $userId
            ]
        ]);

        $roles = AdminRoles::findFirst($role);

        if ($roles === false) {
            $this->flashSession->error('未知角色');
            return $this->response->redirect('admin/users/' . $userId . '/role');
        }

        if ($userRole->update(['role_id' => $role]) === true) {
            $this->flashSession->success('修改成功');
            return $this->response->redirect('admin/users');
        } else {
            return $this->flash->error('角色修改失败，请稍后重试');
        }
    }

}