<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/5/11
 * Time: 11:56
 */

namespace app\Controllers\Home;

use app\Library\AjaxResponse;
use app\Library\CryptModel;
use app\Library\helper;
use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    use AjaxResponse, helper;

    public static $upload_url = '';
    public static $project_id = '';
    public static $department_id = '';

    public function beforeExecuteRoute()
    {
        // 设置用户照片的URL.
        self::$upload_url = self::$upload_url ? self::$upload_url : $this->config->img['upload_url'];

        // 获取URL参数.
        if($this->request->get('pid')!==null && $this->request->get('did')!==null){
            self::$project_id = $this->request->get('pid');
            self::$department_id = $this->request->get('did');
        }elseif($this->request->get('p')!==null && $this->request->get('d')!==null){
            self::$project_id = (int)CryptModel::decrypt($this->request->get('p'), CryptModel::POINT_KEY);
            self::$department_id = (int)CryptModel::decrypt($this->request->get('d'), CryptModel::POINT_KEY);
        }else{
            $this->error();
        }
    }

    public function afterExecuteRoute()
    {
        // 设置视图目录.
        $this->view->setViewsDir($this->view->getViewsDir() . 'home/');

        // 设置页面公共参数.
        $this->view->setVars([
            '_csrfKey' => $this->security->getTokenKey(),
            '_csrf' => $this->security->getToken(),
            '_session' => $this->session->get('user'),
            '_config' => $this->config->img,
            '_Controller' => $this->getCname(),
            'project_id' => self::$project_id,
            'department_id' => self::$department_id,
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

    /**
     * 错误提示.
     * @return mixed
     */
    function error($msg = '网络错误,请重新扫码！')
    {
        echo " <h1>{$msg}</h1>";
        exit;
    }

}