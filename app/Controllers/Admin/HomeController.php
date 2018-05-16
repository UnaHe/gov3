<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/8
 * Time: 10:27
 */

namespace app\Controllers\Admin;

use app\Models\Users;

/**
 * 主页控制器
 * Class HomeController
 * @package app\Controller\Admin
 */
class HomeController extends ControllerBase
{
    /**
     * 主页.
     */
    public function indexAction()
    {

    }

    /**
     * 修改密码页.
     */
    public function changePwdAction()
    {

    }

    /**
     * 修改密码.
     * @return string
     */
    public function updatePwdAction()
    {
        // 获取数据.
        $userId = $this->session->get('user')['user_id'];
        $oldPass = $this->session->get('user')['user_pass'];
        $newPass = $this->request->getPost('user_pass');

        if (!$userId || !$oldPass || !$newPass) {
            return $this->ajaxError('参数错误', 406);
        }

        if ($this->security->checkHash($newPass, $oldPass)) {
            return $this->ajaxError('新密码不能与旧密码相同', 406);
        }

        // 执行修改.
        $res = Users::findFirst([
            'user_id = :user_id:',
            'bind' => [
                "user_id" => $userId
            ]
        ])->update(['user_pass' => $this->security->hash($newPass)]);

        if ($res === true) {
            return $this->ajaxSuccess('密码修改成功, 请重新登录', 201);
        } else {
            return $this->ajaxError('密码修改失败, 请稍后重试');
        }
    }

    /**
     * 验证旧密码.
     * @return string
     */
    public function authPwdAction()
    {
        // 获取数据.
        $oldPass = $this->request->getPost('old_pass');
        $userPass = $this->session->get('user')['user_pass'];

        // 验证.
        $flag = $this->security->checkHash($oldPass, $userPass);

        return $this->ajaxSuccess('', 200, $flag);
    }

}