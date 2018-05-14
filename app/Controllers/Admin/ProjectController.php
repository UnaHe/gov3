<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/10
 * Time: 15:02
 */

namespace app\Controllers\Admin;

use app\Models\AdminRoles;
use app\Models\AdminRoleUser;
use app\Models\Departments;
use app\Models\Project;
use app\Models\Users;
use Phalcon\Mvc\Model\Transaction\Failed as TxFailed;
use Phalcon\Mvc\Model\Transaction\Manager as TxManager;
use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;

/**
 * 单位控制器
 * Class ProjectController
 * @package app\Controller\Admin
 */
class ProjectController extends ControllerBase
{
    /**
     * 单位列表.
     */
    public function indexAction()
    {
        // 分页参数.
        $limit = $this->request->get('limit', 'int', 10);
        $page = $this->request->get('page', 'int', 1);

        // 搜索参数.
        $keywords = $this->request->get('keywords');
        $status = $this->request->get('status');

        // 查询数据.
        $builder = $this->modelsManager->createBuilder()->from('app\Models\Project');

        // 是否有搜索条件.
        if ($keywords !== '' && $keywords !== NULL) {
            $builder->where('project_name LIKE :project_name: OR project_profile LIKE :project_profile:', [
                'project_name' => '%' . $keywords . '%',
                'project_profile' => '%' . $keywords . '%',
                ]);
            $this->view->keywords = $keywords;
        }

        if ($status !== '' && $status !== NULL) {
            $builder->andWhere('project_status = :project_status:',
                [
                    'project_status' => $status
                ]);
            $this->view->status = $status;
        }

        if (($keywords !== '' && $keywords !== NULL) || ($status !== '' && $status !== NULL)) {
            $builder->orderBy('created_at DESC');
        } else {
            $builder->orderBy('project_status DESC, project_id DESC');
        }

        // 分页.
        $paginator = new PaginatorQueryBuilder(
            array(
                "builder" => $builder,
                "limit"   => $limit,
                "page"    => $page
            )
        );

        // 获取分页数据.
        $result = $paginator->getPaginate();

        $this->view->data = $result;
    }

    /**
     * 添加单位页面.
     */
    public function createAction()
    {

    }

    /**
     * 保存单位.
     */
    public function saveAction()
    {
        // 获取数据.
        $projectName = $this->request->getPost('project_name');
        $projectStatus = $this->request->getPost('project_status');
        $projectProfile = $this->request->getPost('project_profile');
        $projectImage = $this->request->getPost('project_image');

        // 验证.
        if (empty($projectName) || mb_strlen($projectName, 'UTF-8') > 100) {
            $this->flash->warning('名称长度 1 - 100 个字符');
            return $this->dispatcher->forward(
                [
                    'namespace'  => 'app\Controllers\Admin',
                    'controller' => 'project',
                    'action'     => 'create',
                ]
            );
        }

        $unit = Project::findFirst([
            'project_name = :project_name:',
            'bind' => [
                'project_name' => $projectName
            ]
        ]);

        if ($unit !== false) {
            $this->flash->error('单位名已经存在');
            return $this->dispatcher->forward(
                [
                    'namespace'  => 'app\Controllers\Admin',
                    'controller' => 'project',
                    'action'     => 'create',
                ]
            );
        }

        // 准备数据.
        $data = [
            'project_name' => $projectName,
            'project_status' => $projectStatus,
            'project_profile' => $projectProfile,
            'project_image' => $projectImage,
        ];

        // 创建.
        $project = new Project();
        if ($project->create($data) === true) {
            $this->flashSession->success('创建成功');

            return $this->response->redirect('admin/project');
        } else {
            return $this->flash->error('项目创建失败，请稍后重试');
        }
    }

    /**
     * 编辑单位信息.
     * @param $id
     * @return bool|\Phalcon\Http\Response|\Phalcon\Http\ResponseInterface|\Phalcon\Mvc\Model
     */
    public function editAction($id)
    {
        // 查找数据.
        $unit = Project::findFirst($id);

        if ($unit === false) {
            $this->flashSession->error('单位不存在');

            return $this->response->redirect('admin/project');
        }

        $this->view->unit = $unit;

        return true;
    }

    /**
     * 更新单位信息.
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface|string
     */
    public function updateAction()
    {
        $projectId = $this->request->getPost('project_id');
        // 查找数据.
        $project = Project::findFirst($projectId);

        if ($project === false) {
            $this->flashSession->error('单位不存在');

            return $this->response->redirect('admin/project');
        }

        // 获取数据.
        $projectName = $this->request->getPost('project_name');
        $projectStatus = $this->request->getPost('project_status');
        $projectProfile = $this->request->getPost('project_profile');
        $projectImage = $this->request->getPost('project_image');

        // 验证.
        if (empty($projectName) || mb_strlen($projectName, 'UTF-8') > 100) {
            $this->flashSession->warning('名称最大长度 100 个字符');

            return $this->response->redirect('/admin/project/' . $projectId . '/edit');
        }

        $unit = Project::findFirst([
            'project_name = :project_name:',
            'bind' => [
                'project_name' => $projectName
            ]
        ]);

        if ($unit !== false && $unit->project_id != $projectId) {
            $this->flashSession->error('单位名已经存在');
            return $this->response->redirect('/admin/project/' . $projectId . '/edit');
        }

        // 准备数据.
        $data = [
            'project_name' => $projectName,
            'project_status' => $projectStatus,
            'project_profile' => $projectProfile,
            'project_image' => $projectImage,
        ];

        // 更新.
        $oldImg = $project->project_image;
        if ($project->update($data) === true) {
            // 判断是否删除图片.
            if ($projectImage && $oldImg) {
                // 都存在, 删除旧图.
                @unlink($this->config->img['upload_path'] . $oldImg);
            } else if (!$projectImage && $oldImg) {
                // 没新图, 移除旧图, 删除旧图.
                @unlink($this->config->img['upload_path'] . $oldImg);
            }

            // 判断用户权限.
            $this->flashSession->success('更新成功');
            $session = $this->session->get('user');
            if ($session['user_is_super'] || ($session['user_is_admin'] && $session['project_id'] == '')) {
                return $this->response->redirect('admin/project');
            } else {
                return $this->response->redirect('/admin/project/' . $projectId . '/edit');
            }
        } else {
            return $this->flash->error('项目更新失败，请稍后重试');
        }
    }

    /**
     * 关闭单位.
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface|string
     */
    public function deleteAction()
    {
        // CSRF.
        if (!$this->security->checkToken()) {
            return $this->ajaxError('CSRF', 401);
        }

        $projectId = $this->request->getPost('project_id');
        // 查找单位.
        $project = Project::findFirst($projectId);
        if ($project === false) {
            $this->flashSession->error('单位不存在');

            return $this->response->redirect('admin/project');
        }

        if ($project->update(['project_status' => 0]) === true) {
            return $this->ajaxSuccess('项目关闭成功', 201);
        } else {
            return $this->ajaxError('项目关闭失败，请稍后重试');
        }
    }

    /**
     * 显示详情.
     * @return string
     */
    public function showAction()
    {
        $projectId = $this->request->getPost('project_id');
        // 查找单位.
        $project = Project::findFirst($projectId);
        if ($project) {
            $data = $project->toArray();
            return $this->ajaxSuccess($data['project_profile']);
        } else {
            return $this->ajaxError('暂无法获取，请稍后重试');
        }
    }

    /**
     * 创建管理员.
     */
    public function createAdminAction()
    {
        // 得到所有有效的项目列表.
        $projects = Project::getProjectList();
        $userId = $this->request->get('user_id') ? : 0;
        $userInfo = $departmentList = $oldRole = $projectAdministrator = [];

        // 默认为单位管理员.
        $adminType = 1;
        if(!empty($userId)){
            $userInfo = (new Users)->getDetailById($userId);
            if(!empty($userInfo['project_id']) && $userId){
                $departmentList = Departments::find([
                    'project_id = :project_id:',
                    'bind' => [
                        'project_id' => $userInfo['project_id']
                    ],
                ]);
            }else{
                $adminType = 0; //系统管理员
            }

            // 查询用户权限.
            $oldRole = AdminRoleUser::findfirst([
                'user_id = :user_id:',
                'bind' => [
                    'user_id' => $userId
                ]
            ])->role_id;
        }

        // 所有角色.
        $roles = AdminRoles::find()->toArray();
        foreach ($roles as $k=>$v){
            if($v['code'] == 'administrator'){
                unset($roles[$k]);
            }
            if($v['code'] == 'project_administrator'){
                $projectAdministrator = $v;
                unset($roles[$k]);
            }
        }

        // 模版赋值.
        $this->view->setVars(
            [
                'project' => $projects,
                'user_id' => $userId,
                'user_info' => $userInfo,
                'department_list' => $departmentList,
                'roles' => $roles,
                'old_role' => $oldRole,
                'project_administrator' => $projectAdministrator,
                'admin_type' => $adminType
            ]
        );
    }

    /**
     * 保存管理员.
     */
    public function saveAdminAction()
    {
        // 获取数据.
        $user_id = $this->request->getPost('user_id');
        $admin_type = $this->request->getPost('admin_type');
        $project_id = $this->request->getPost('project_id');
        $department_id = $this->request->getPost('department_id');
        $user_name = $this->request->getPost('user_name');
        $user_pass = $this->request->getPost('user_pass') ? $this->security->hash($this->request->getPost('user_pass')) : $this->security->hash('123456');
        $user_image = $this->request->getPost('user_image');
        $user_sex = $this->request->getPost('user_sex');
        $user_age = $this->request->getPost('user_age');
        $user_phone = $this->request->getPost('user_phone');
        $user_status = $this->request->getPost('user_status');
        $user_job = $this->request->getPost('user_job');
        $user_intro = $this->request->getPost('user_intro');

        // 验证.
        if (mb_strlen($user_name, 'UTF-8') < 2 || mb_strlen($user_name, 'UTF-8') > 18) {
            $this->flash->warning('名称长度 2 - 18 个字符');
            return $this->dispatcher->forward(
                [
                    'namespace'  => 'app\Controllers\Admin',
                    'controller' => 'project',
                    'action'     => 'createAdmin',
                ]
            );
        }
        if ($user_age <= 0) {
            $this->flash->warning('请输入正确的年龄');
            return $this->dispatcher->forward(
                [
                    'namespace'  => 'app\Controllers\Admin',
                    'controller' => 'project',
                    'action'     => 'createAdmin',
                ]
            );
        }
        if ($user_sex === '') {
            $this->flash->warning('请选择性别');
            return $this->dispatcher->forward(
                [
                    'namespace'  => 'app\Controllers\Admin',
                    'controller' => 'project',
                    'action'     => 'createAdmin',
                ]
            );
        }
        if(!preg_match('/^1[3456789]{1}\d{9}$/', $user_phone)){
            $this->flash->warning('手机号格式不正确');
            return $this->dispatcher->forward(
                [
                    'namespace' => 'app\Controllers\Admin',
                    'controller' => 'project',
                    'action' => 'createAdmin',
                ]
            );
        }
        if ($user_id == '0') {
            $issetUser = Users::findFirst([
                'user_phone = :user_phone:',
                'bind' => [
                    'user_phone' => $user_phone
                ]
            ]);
            if ($issetUser !== false) {
                $this->flash->warning('手机号码已存在');
                return $this->dispatcher->forward(
                    [
                        'namespace' => 'app\Controllers\Admin',
                        'controller' => 'project',
                        'action' => 'createAdmin',
                    ]
                );
            }
        }

        // 用户权限
        if($admin_type == 1){
            $userRole = $this->request->getPost('roles1')[0];
        }else{
            $userRole = $this->request->getPost('roles2')[0];
            $project_id = NULL;
            $department_id = NULL;
        }

        // 用户基本数据.
        $data = [
            'user_is_admin' => 1, // 这个有疑问, 权限管理混乱, 迁移完成重写.
            'project_id' => $project_id,
            'department_id' => $department_id,
            'user_name' => $user_name,
            'user_pass' => $user_pass,
            'user_image' => $user_image,
            'user_sex' => $user_sex,
            'user_age' => $user_age,
            'user_phone' => $user_phone,
            'user_status' => $user_status,
            'user_job' => $user_job,
            'user_intro' => $user_intro,
        ];

        $Users = new Users();
        $AdminRoleUser = new AdminRoleUser();

        // 开启事务.
        $this->db->begin();

        try {

            if ($user_id == '0') {
                // 创建用户, 用户角色关联记录.
                if ($Users->create($data) === true && $Users->user_id) {
                    if ($AdminRoleUser->create(['user_id' => $Users->user_id, 'role_id' => $userRole]) === false) {
                        throw new \LogicException('创建失败，请稍后重试');
                    };
                    $this->flashSession->success('创建成功');
                }
            } else {
                unset($data['user_pass']);
                // 更新用户表.
                $user = $Users::findfirst([
                    'user_id = :user_id:',
                    'bind' => [
                        'user_id' => $user_id
                    ]
                ]);

                $oldImg = $user->user_image;
                // 判断是否删除图片.
                if ($user_image && $oldImg) {
                    // 都存在, 删除旧图.
                    @unlink($this->config->img['upload_path'] . $oldImg);
                } else if (!$user_image && $oldImg) {
                    // 没新图, 移除旧图, 删除旧图.
                    @unlink($this->config->img['upload_path'] . $oldImg);
                }

                if ($user->update($data) === true) {
                    // 更新用户角色关联记录.
                   $res = $AdminRoleUser::findfirst([
                        'user_id = :user_id:',
                        'bind' => [
                            'user_id' => $user_id
                        ]
                    ]);

                   if ($res->update(['role_id' => $userRole]) === false) {
                       throw new \LogicException('更新失败，请稍后重试');
                   }

                    $this->flashSession->success('更新成功');
                }
            }

            // 执行保存.
            $this->db->commit();
            return $this->response->redirect('admin/project/adminuserlist');
        } catch (\Exception $e) {
            $this->db->rollback();
            $error = $e instanceof \LogicException ? $e->getMessage() : '系统错误, 请稍后再试';
            $this->flashSession->error($error);
            return $this->response->redirect('admin/project/adminuserlist');
        }
    }

    /**
     * 管理员列表.
     */
    public function adminUserListAction()
    {
        // 分页参数.
        $page = $this->request->get('page', 'int', 1);
        $limit = $this->request->get('limit', 'int', 10);

        $input = $this->request->getQuery();
        $data['list'] = (new Users())->getAdminUsers($input, $page, $limit);
        $data['project_list'] = Project::find();

        $this->view->setVars([
            'data' => $data,
            'input' => $input,
        ]);
    }

}