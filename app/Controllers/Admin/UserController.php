<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/16
 * Time: 15:25
 */

namespace app\Controllers\Admin;

use app\Models\Departments;
use app\Models\Project;
use app\Models\Users;

/**
 * 用户控制器
 * Class UserController
 * @package app\Controller
 */
class UserController extends ControllerBase
{
    /**
     * 删除用户.
     * @throws \Exception
     */
    public function deleteAction()
    {
        $userId = $this->request->getPost('user_id');

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

}