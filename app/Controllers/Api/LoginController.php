<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/5/15
 * Time: 17:20
 */

namespace app\Controllers\Api;

use app\Models\Comments;
use app\Models\Status;
use app\Models\Users;
use app\Models\UserStatus;

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
    public function loginAction()
    {
        // 获取参数.
        $input = $this->request->get();

        if ($this->request->isPost()) {
            // 验证数据.
            if (!preg_match('/^1[3456789]{1}\d{9}$/', $input['user_phone']) || !$input['user_pass']) {
                return $this->ajaxError('参数错误', 406);
            }

            // 查询用户.
            $userInfo = (new Users)->getDetailsByTel($input['user_phone']);

            // 验证用户密码.
            if(!$userInfo || !($this->security->checkHash($input['user_pass'], $userInfo['user_pass']))) {
                return $this->ajaxError('帐号或密码错误', 401);
            }

            // 从哪来的请求.
            $routes = explode('/', $this->request->getURI());
            if (!in_array('api', $routes)) {
                // 记住我.
                $remember = boolval($input['remember']);
                if ($remember) {
                    // 记住密码后将用户的remember_token.
                    $this->cookies->set($this->config->constants['staff_remember_token'], $userInfo['remember_token'], time() + 86400);
                } else {
                    if ($this->cookies->has($this->config->constants['staff_remember_token'])) {
                        $this->cookies->set($this->config->constants['staff_remember_token'], '', time() - 3600);
                    }
                }
                $tpl = isset($input['tpl']) ? $input['tpl'] : 'pc';

                $this->session->set('staff', json_decode(json_encode($userInfo), true));
                $this->session->set('tpl', $tpl);
            }

            return $this->ajaxSuccess('登陆成功');
        }

        // 从哪登录.
        $tpl = isset($input['tpl']) && $input['tpl'] == 'm' ? 'mobile' : 'pc';
        $this->session->remove('staff');
        $this->session->remove('tpl');

        // 判断记住.
        if ($this->cookies->has($this->config->constants['staff_remember_token'])) {
            $staff_remember_token = $this->cookies->get($this->config->constants['staff_remember_token'])->getValue();

            // 查询用户.
            $userInfo = Users::findFirst([
                'remember_token = :remember_token:',
                'bind' => [
                    'remember_token' => $staff_remember_token
                ],
            ])->toArray();

            // 设置session.
            if($userInfo !== false){
                unset($userInfo['user_pass']);
                $this->session->set('staff', json_decode(json_encode($userInfo), true));
                $this->session->set('tpl', $tpl);
                return $this->response->redirect('staff/refresh');
            }
        }

        // 页面参数.
        $this->view->setVars([
            'tpl' => $tpl,
        ]);

        $this->view->pick($tpl . '/login');

        return true;
    }

    /**
     * 刷新页面.
     */
    public function refreshAction()
    {
        $routes = explode('/', $this->request->getURI());

        if (!in_array('api', $routes)) {
            $data['user_phone'] = $this->session->get('staff')['user_phone'];
        } else {
            $user_phone = $this->request->get('user_phone');

            if (!preg_match('/^1[3456789]{1}\d{9}$/', $user_phone)) {
                return $this->ajaxError('参数错误', 406);
            }

            $data['user_phone'] = $this->request->get('user_phone');
        }

        // 查询用户.
        $userInfo = (new Users)->getUserDetailsByTel($data['user_phone']);

        // 验证用户密码.
        if(!$userInfo) {
            if (!in_array('api', $routes)) {
                return $this->response->redirect('staff/login');
            }

            return $this->ajaxError('帐号不存在', 401);
        }

        // 用户头像.
        if ($userInfo['user_image']) {
            $userInfo['user_image'] = self::$upload_url . $userInfo['user_image'];
        } else {
            $userInfo['user_image'] = Config("constants.defalut_staff_img");
        }

        // 留言列表.
        $userInfo['comments'] = (new Comments())->getCommentByUserId($userInfo['user_id']);

        // 计划列表.
        $userInfo['statuslist'] = (new UserStatus())->getPlanListByUserId($userInfo['user_id']);

        // 当前状态.
        $currentStatus = (new UserStatus())->getStatusByUser($userInfo['user_id']);
        if ($currentStatus !== false) {
            $userInfo['nowstatus'] = $currentStatus[0];
        } else {
            // 默认状态.
            $project_default_status = (new Status())->getDefaultStatusByProject($userInfo['project_id']);

            $project_default_status_arr = [];
            foreach ($project_default_status as $v) {
                $project_default_status_arr[$v->project_id][$v->status_is_default] = $v->toArray();
            }
            unset($project_default_status);

            $time = time();
            $key = (date("H:i", $time) > $userInfo['work_start_time'] && date("H:i",  $time) < $userInfo['work_end_time']) ? 1 : 2;
            $project = isset($project_defalut_status_arr[$userInfo['project_id']]) && array_key_exists($key, $project_default_status_arr[$userInfo['project_id']]) !== false ? $userInfo['project_id'] : 0;
            $userInfo['nowstatus'] = $project_default_status_arr[$project][$key];
            $userInfo['nowstatus']['start_time'] = $time;
            $userInfo['nowstatus']['end_time'] = strtotime($userInfo['work_end_time']);
        }

        if ($this->request->isPost()) {
            $data = [
                'status' => 200,
                'msg' => '',
                'data' => $userInfo,
            ];

            return json_encode($data, JSON_UNESCAPED_UNICODE);
        } else {
            // 页面参数.
            $this->view->setVars([
                'data' => $userInfo,
            ]);
            $this->view->pick($this->session->get('tpl') . '/index');

            return true;
        }
    }

    /**
     * 设置页面.
     */
    public function settingAction()
    {
        $this->view->pick($this->session->get('tpl') . '/setting');
    }

    /**
     * 修改我的留言.
     */
    public function editCommentsAction()
    {
        if ($this->request->isPost()) {
            // 获取参数.
            $user_phone = $this->request->get('user_phone');
            $user_comments = $this->request->get('user_comments');

            if (!preg_match('/^1[3456789]{1}\d{9}$/', $user_phone) || !$user_comments) {
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
            }

            // 执行修改.
            $res = $user->update(['user_comments' => $user_comments]);

            if ($res) {
                return $this->ajaxSuccess('操作成功', 201);
            } else {
                return $this->ajaxError('系统错误, 请稍候重试');
            }
        }

        // 用户信息.
        $userInfo = Users::findFirst([
            'user_phone = :user_phone:',
            'bind' => [
                'user_phone' => $this->session->get('staff')['user_phone'],
            ],
            'columns' => 'user_phone, user_comments',
        ])->toArray();

        // 页面参数.
        $this->view->setVars([
            'data' => $userInfo,
        ]);

        $this->view->pick($this->session->get('tpl') . '/mycomment');

        return true;
    }
    
    /**
     * 修改密码.
     */
    public function changePasswordAction()
    {
        if ($this->request->isPost()) {
            // 获取参数.
            $input = $this->request->getPost();

            if (!$input['user_phone'] || !$input['user_pass'] || !$input['new_user_pass']) {
                return $this->ajaxError('参数错误.', 406);
            }

            if ($this->security->checkHash($input['new_user_pass'], $this->session->get('staff')['user_pass'])) {
                return $this->ajaxError('新密码不能与旧密码相同', 406);
            }

            if (!($this->security->checkHash($input['user_pass'], $this->session->get('staff')['user_pass']))) {
                return $this->ajaxError('旧密码错误', 406);
            }

            $res = Users::findFirst([
                'user_phone = :user_phone:',
                'bind' => [
                    "user_phone" => $input['user_phone'],
                ]
            ])->update(['user_pass' => $this->security->hash($input['new_user_pass'])]);

            if ($res) {
                return $this->ajaxSuccess('密码修改成功, 请重新登录', 201);
            } else {
                return $this->ajaxError('密码修改失败, 请稍后重试');
            }
        }

        $this->view->pick($this->session->get('tpl') . '/remedial');

        return true;
    }

    /**
     * 登出.
     */
    public function logoutAction()
    {
        $tpl = $this->session->get('tpl') == 'mobile' ? '?tpl=m' : '';

        // 删除session, cookies.
        $this->session->remove('staff');
        $this->session->remove('tpl');
        $this->cookies->set($this->config->constants['staff_remember_token'], '', time() - 3600);

        if ($this->request->isPost()) {
            return $this->ajaxSuccess('已退出');
        } else {
            return $this->response->redirect('staff/login' . $tpl);
        }
    }

}