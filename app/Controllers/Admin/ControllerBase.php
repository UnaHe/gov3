<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/4
 * Time: 11:46
 */

namespace app\Controllers\Admin;

use app\Library\AjaxResponse;
use app\Library\helper;
use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    use AjaxResponse, helper;

    public function beforeExecuteRoute()
    {
        $namespace = $this->dispatcher->getNamespaceName();

        // 是否登录.
        if (!$this->session->has('user') && $this->getCname() !== 'LoginController' && $namespace !== 'app\Controllers\Admin') {
            // 跳转登录页.
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
        // 设置视图目录.
        $this->view->setViewsDir($this->view->getViewsDir() . 'admin/');

        // 设置页面公共参数.
        $this->view->setVars([
            '_csrfKey' => $this->security->getTokenKey(),
            '_csrf' => $this->security->getToken(),
            '_session' => $this->session->get('user'),
            '_config' => $this->config->constants,
            '_Controller' => $this->getCname(),
        ]);
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