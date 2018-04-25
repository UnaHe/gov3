<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/4
 * Time: 11:46
 */

namespace app\Controllers\Admin;

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    public function beforeExecuteRoute()
    {
        // 是否登录.
        if (!$this->session->has('user') && $this->getCname() !== 'LoginController') {
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

    public function afterExecuteRoute()
    {
        $this->view->setViewsDir($this->view->getViewsDir() . 'admin/');
        $this->view->_csrfKey = $this->security->getTokenKey();
        $this->view->_csrf = $this->security->getToken();
        $this->view->_session = $this->session->get('user');
        $this->view->_config = $this->config->img;
        $this->view->_Controller = $this->getCname();
    }


    /**
     * AjaxSuccess
     * @param int $status
     * @param string $msg
     * @param bool $flag
     * @return string
     */
    protected function ajaxSuccess($msg = '', $status = 200, $flag = true)
    {
        return $this->ajaxResponse($status, $msg, $flag);
    }

    /**
     * AjaxError.
     * @param string $msg
     * @param int $status
     * @param bool $flag
     * @return string
     */
    protected function ajaxError($msg = '', $status = 400, $flag = false)
    {
        return $this->ajaxResponse($status, $msg, $flag);
    }

    /**
     * AjaxResponse.
     * @param int $status
     * @param string $msg
     * @param bool $flag
     * @return string
     */
    private function ajaxResponse($status, $msg, $flag)
    {
        $data = ['status' => $status, 'msg' => $msg, 'flag' => $flag];
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 获取控制器名称.
     * @return mixed
     */
    public function getCname()
    {
        return explode('\\',$this->dispatcher->getControllerClass())[3];
    }

}