<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/23
 * Time: 9:38
 */

namespace app\Controllers\Admin;

use app\Library\CryptModel;
use app\Models\Departments;
use app\Models\Forwards;
use app\Models\Project;

/**
 * 二维码控制器
 * Class LoginController
 * @package app\Controller\Admin
 */
class QrcodeController extends ControllerBase
{
    /**
     * 二维码列表.
     */
    public function indexAction()
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

        if ($user['user_is_super'] && empty($user['project_id'])) {
            $data['project_list'] = Project::getProjectList();
            if(!empty($input['project_id'])){
                $data['department_list'] = (new Departments())->getTree(0, 0, $input['project_id']);
            }
        } else {
            $input['project_id'] = $user['project_id'];
            if($user['user_is_admin']){
                $data['department_list'] = (new Departments())->getTree(0, 0, $user['project_id']);
            }else{
                $input['department_id'] = $user['department_id'];
            }
        }
        if ($user['user_is_admin']) {
            $input['project_id'] = $user['project_id'];
        }
        $data['list'] = (new Forwards())->getList($input, $page, $limit);

        // 页面参数.
        $this->view->setVars([
            'data' => $data,
            'input' => $input,
            'APP_URL' => APP_URL,
        ]);
    }

    /**
     * 修改.
     * @param $id
     */
    public function editAction($id)
    {
        $user = $this->session->get('user');

        $department_list = [];
        if ($user['user_is_super'] && empty($user['project_id'])) {
            $project = Project::getProjectList();
        } else {
            $project = $user['project_name'];
            if($user['user_is_admin']){
                $department_list = (new Departments())->getTree(0,0,$user['project_id']);
            }
        }
        $forward = [];
        if($id != 0){
            $forward = Forwards::findFirst($id);
            $department_list = (new Departments())->getTree(0,0,$forward->project_id);
        }

        // 页面参数.
        $this->view->setVars([
            'project' => $project,
            'department_list' => $department_list,
            'id' => $id,
            'forward' => $forward,
        ]);
    }

    /**
     * 验证二维码ID.
     */
    public function validAction()
    {
        $input = $this->request->getPost();

        // 规范参数, 避免查询出错.
        foreach ($input as $k => $v){
            if ($v === '') {
                $input[$k] = NULL;
            }
        }

        $data =  $res = Forwards::findFirst([
            'forward_id = :forward_id:',
            'bind' => [
                'forward_id' => $input['forward_id']
            ]
        ]);

        if ($data === false) {
            $old_department = Forwards::findFirst([
                'project_id = :project_id: AND department_id = :department_id:',
                'bind' => [
                    'project_id' => $input['project_id'],
                    'department_id'=>$input['department_id']
                ]
            ]);
            if ($old_department !== false) {
                return $this->ajaxError('注意: 此部门已经有绑定的二维码编号 ' . $old_department->forward_id . ', 提交将使用新编号 ' . $input['forward_id'], 405);
            } else {
                return $this->ajaxSuccess();
            }
        } else {
            return $this->ajaxError('此编号已经被使用，请重新填写', 406);
        }
    }

    /**
     * 更新or添加.
     */
    public function updateAction()
    {
        $id = $this->request->getPost('id');
        $forwardId = $this->request->getPost('forward_id');
        $projectId = $this->request->getPost('project_id');
        $departmentId = $this->request->getPost('department_id');
        $forwardIntroduction = $this->request->getPost('forward_introduction');

        if (!$projectId || !$departmentId || !$forwardId) {
            $this->flash->warning('参数错误');
            return $this->dispatcher->forward(
                [
                    'namespace'  => 'app\Controllers\Admin',
                    'controller' => 'QrCode',
                    'action'     => 'index',
                ]
            );
        }

        if ($forwardId <= 0 || !is_numeric($departmentId)) {
            $this->flash->warning('编号为大于0的数字');
            return $this->dispatcher->forward(
                [
                    'namespace'  => 'app\Controllers\Admin',
                    'controller' => 'QrCode',
                    'action'     => 'index',
                ]
            );
        }
        $route = '/status/workerStatusList';

        // 匹配加号.
        $p = str_replace('+','%2B', CryptModel::encrypt($projectId,CryptModel::POINT_KEY));
        $d = str_replace('+','%2B', CryptModel::encrypt($departmentId, CryptModel::POINT_KEY));

        $forwardString = APP_URL . $route . '?p=' . $p . '&d=' . $d;

        $params = [
            'forward_id' => $forwardId,
            'forward_string' => $forwardString,
            'project_id' => $projectId,
            'department_id' => $departmentId,
            'forward_introduction' => $forwardIntroduction,
        ];

        $user = $this->session->get('user');

        $Forwards = new Forwards();

        // 开启事务.
        $this->db->begin();

        try{

            if($id === '0'){
                //新增
                $params['project_id'] = !empty($user['project_id']) ? $user['project_id'] : $params['project_id'];

                $old_department = $Forwards->findFirst([
                    'project_id = :project_id: AND department_id = :department_id:',
                    'bind' => [
                        'project_id' => $projectId,
                        'department_id'=>$departmentId
                    ]
                ]);

                if ($old_department !== false) {
                    if ($old_department->delete() === false){
                        throw new \LogicException('绑定失败，请稍后重试');
                    }
                }

                if ($Forwards->create($params) === false) {
                    throw new \LogicException('创建失败，请稍后重试');
                }
            }else{
                // 更新.
                $department = $Forwards->findFirst([
                    'id = :id:',
                    'bind' => [
                        'id' => $id
                    ]
                ]);

                if($department->update($params) === false) {
                    throw new \LogicException('更新失败，请稍后重试');
                }
            }

            // 保存.
            $this->db->commit();
            $this->flashSession->success('绑定成功');
            return $this->response->redirect('admin/qrcode');
        }catch (\Exception $e){
            $this->db->rollback();
            $error = $e instanceof \LogicException ? $e->getMessage() : '系统错误，请稍后重试';
            $this->flashSession->error($error);
            return $this->response->redirect('admin/qrcode');
        }
    }

    /**
     * 删除绑定.
     */
    public function deleteAction()
    {
        $id = $this->request->getPost('id');

        $Forward = Forwards::findFirst($id);

        if ($Forward ===false) {
            return $this->ajaxError('已删除');
        }

        if ($Forward->delete() === true) {
            return $this->ajaxSuccess('删除成功', 201);
        } else {
            return $this->ajaxError('删除失败, 请稍后重试');
        }
    }

    /**
     * 获取二维码.
     */
    public function ajaxGetForwardQrCodeAction()
    {
        $id = $this->request->getPost('id');

        $Forward = Forwards::findFirst($id);

        if ($Forward ===false) {
            return $this->ajaxError('二维码不存在');
        }

        $image = APP_URL . '/forward/' . $Forward->forward_id;

        if ($image) {
            return $this->ajaxSuccess($image);
        } else {
            return $this->ajaxError('系统错误, 请稍后重试');
        }
    }

}