<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/5/16
 * Time: 18:18
 */

namespace app\Controllers\Api;

use app\Models\Users;

/**
 * 统计控制器
 * Class CountController
 * @package app\Controller\Api
 */
class CountController  extends ControllerBase
{
    /**
     * 统计列表.
     */
    public function indexAction()
    {
        $this->view->pick($this->session->get('tpl') . '/count');
    }

    /**
     * 我的统计.
     */
    public function myCountAction()
    {
        $routes = explode('/', $this->request->getURI());

        if (!in_array('api', $routes)) {
            $user_id = $this->session->get('staff')['user_id'];
        } else {
            $user_phone = $this->request->get('user_phone');

            if (!preg_match('/^1[3456789]{1}\d{9}$/', $user_phone)) {
                return $this->ajaxError('参数错误', 406);
            }

            $user = Users::findFirst([
                'user_phone = :user_phone:',
                'bind' => [
                    'user_phone' => $user_phone,
                ],
            ]);

            if ($user === false) {
                return $this->ajaxError('用户不存在');
            } else {
                $user_id = $user->user_id;
            }

            $time = time();

            // 查询计划表中今天的工作列表.
            $input['user_id'] = $user_id;
            $input['start_time'] = $time;
        }
    }
}