<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/5/15
 * Time: 17:20
 */

namespace app\Controllers\Api;
use app\Models\Users;

/**
 * 登录控制器
 * Class LoginController
 * @package app\Controller\Api
 */
class LoginController  extends ControllerBase
{
    /**
     * 登录页面.
     */
    public function indexAction()
    {
        // 获取参数.
        $input = $this->request->get();

        $tpl = isset($input['tpl']) && $input['tpl'] == 'm' ? 'mobile' : 'pc';
        $this->session->remove('staff');
        $this->session->remove('tpl');

        // 判断记住.
        if (array_key_exists($this->config->img['staff_remember_token'], $_COOKIE)) {
            $staff_remember_token = $_COOKIE[$this->config->img['staff_remember_token']];

            // 查询用户.
            $user = Users::findFirst([
                'remember_token = :remember_token:',
                'bind' => [
                    'remember_token' => $staff_remember_token
                ],
            ]);

            if($user !== false){
                unset($user->user_pass);
                $this->session->set('staff', json_decode(json_encode($user)));
                $this->session->set('tpl', $tpl);
                return $this->response->redirect('staff/refresh');
            }
        }

        $this->view->setVars([
            'tpl' => $tpl,
        ]);

        $this->view->pick($tpl . '/login');

        return true;
    }

}