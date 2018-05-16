<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/5/15
 * Time: 11:55
 */

namespace app\Controllers\Api;

use app\Library\AjaxResponse;
use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    use AjaxResponse;

    public static $upload_url = '';

    public function beforeExecuteRoute()
    {
        // 设置用户照片的URL.
        self::$upload_url = self::$upload_url ? self::$upload_url : $this->config->constants['upload_url'];
    }

    public function afterExecuteRoute()
    {
        // 设置视图目录.
        $this->view->setViewsDir($this->view->getViewsDir() . 'staff/');

        // 设置页面公共参数.
        $this->view->setVars([
            '_csrfKey' => $this->security->getTokenKey(),
            '_csrf' => $this->security->getToken(),
            '_config' => $this->config->constants,
            '_session' => $this->session->get('staff'),
        ]);
    }

}