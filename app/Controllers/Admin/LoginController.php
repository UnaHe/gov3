<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/4
 * Time: 11:46
 */

namespace app\Controllers\Admin;

use app\Library\FilterModel;
use app\Models\Users;

/**
 * 登录控制器
 * Class LoginController
 * @package app\Controller
 */
class LoginController extends ControllerBase
{
    /**
     * 登录页面.
     */
    public function indexAction()
    {

    }

    /**
     * 登录.
     * @return string
     */
    public function loginAction()
    {
        // CSRF.
        if (!$this->security->checkToken()) {
            return $this->ajaxError('CSRF', 401);
        }

        // 验证数据.
        $rules = array(
            'user_phone' => array(
                'filter' => FILTER_SANITIZE_STRING,
                'required'
            ),
            'user_pass' => array(
                'filter' => FILTER_SANITIZE_STRING,
                'required'
            )
        );

        $filter = new FilterModel($rules);
        if (!$filter->isValid($this->request->getPost())) {
            return $this->ajaxError('参数错误', 406);
        }

        // 获取验证后数据.
        $input = $filter->getResult();
        $userInfo = (new Users)->getDetailsByTel($input['user_phone']);

        // 验证用户密码.
        if(!$userInfo || !($this->security->checkHash($input['user_pass'], $userInfo['user_pass'])) || ($userInfo['project_id'] > 0 && $userInfo['user_is_admin'] != '1')) {
            return $this->ajaxError('电话或密码错误', 401);
        }

        // 成功保存session信息.
        $this->session->set('user', $userInfo);

        return $this->ajaxSuccess();
    }

    /**
     * 退出.
     */
    public function logoutAction()
    {
        // 销毁Session.
        $this->session->remove('user');

        // 跳转首页.
        $this->dispatcher->forward(
            [
                'namespace'  => 'app\Controllers\Admin',
                'controller' => 'login',
                'action'     => 'index',
            ]
        );
    }

}