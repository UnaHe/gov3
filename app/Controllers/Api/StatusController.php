<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/5/18
 * Time: 11:44
 */

namespace app\Controllers\Api;

use app\Models\Status;
use app\Models\UserStatus;

/**
 * 事件控制器
 * Class StatusController
 * @package app\Controller\Api
 */
class StatusController  extends ControllerBase
{
    // 新增(修改)状态.
    public function addStatusAction()
    {
        if ($this->request->isPost()) {
            // 获取参数.
            $input = $this->request->getPost();

            $data = [
                'user_status_id' => $input['user_status_id'],
                'status_id' => $input['status_id'],
                'user_id' => $input['user_id'],
                'start_time' => $input['start_time'],
                'end_time' => $input['end_time'],
                'user_status_desc' => $input['desc'],
            ];
            
            if (is_null($data['start_time']) || is_null($data['end_time'])) {
                return $this->ajaxError('开始时间结束时间必须填写', 406);
            }

            // 时间戳.
            $data['start_time'] = strtotime($data['start_time']);
            $data['end_time'] = strtotime($data['end_time']);

            // 修改还是新增.
            if (!empty($data['user_status_id']) || !is_null($data['user_status_id'])) {
                if (!UserStatus::getConflict($data['user_id'], $data['start_time'], $data['end_time'], $data['user_status_id'])) {
                    return $this->ajaxError('时间段内存在其它事件, 请合理安排');
                }

                if (UserStatus::findFirst($data['user_status_id'])->update($data) === true) {
                    return $this->ajaxSuccess('事件修改成功', 201);
                } else {
                    return $this->ajaxError('事件修改失败, 请稍后重试');
                }

            } else {
                if (!UserStatus::getConflict($data['user_id'], $data['start_time'], $data['end_time'])) {
                    return $this->ajaxError('时间段内存在其它事件, 请合理安排');
                }

                unset($data['user_status_id']);

                if ((new UserStatus())->create($data) === true) {
                    return $this->ajaxSuccess('事件添加成功', 201);
                } else {
                    return $this->ajaxError('事件添加失败, 请稍后重试');
                }
            }
        }

        $user_status_id = $this->request->get('user_status_id');
        $old_info = [];
        if(!empty($user_status_id)){
            $old_info = (new UserStatus())->getInfoById($user_status_id);
        }
        $statuslist = Status::getListByProjectId($this->session->get('staff')['project_id']);

        // 页面参数.
        $this->view->setVars([
            'statuslist' => $statuslist,
            'old_info' => $old_info,
        ]);

        $this->view->pick($this->session->get('tpl') . '/state');

        return true;
    }

    /**
     * 删除状态.
     */
    public function delStatusAction()
    {
        // 获取参数.
        $user_status_id = $this->request->getPost('user_status_id');

        // 查询记录.
        $user_status = UserStatus::findFirst($user_status_id);

        if ($user_status === false) {
            return $this->ajaxError('记录不存在或已删除');
        }

        // 执行删除.
        if ($user_status->delete() === true) {
            return $this->ajaxSuccess('状态记录删除成功', 201);
        } else {
            return $this->ajaxError('状态记录删除失败，请稍后重试');
        }
    }

    /**
     * 状态列表.
     */
    public function statusListAction()
    {
        // 获取参数.
        $project_id = $this->request->getPost('project_id');

        // 查询记录.
        $statusList = Status::getListByProjectId($project_id);

        $res = [
            'status' => 200,
            'msg' => '操作成功！',
            'data' => $statusList->toArray(),
        ];

        return json_encode($res, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 状态详情.
     */
    public function statusInfoAction()
    {
        // 获取参数.
        $user_status_id = $this->request->getPost('user_status_id');

        // 查询记录.
        $statusInfo = (new UserStatus())->getInfoById($user_status_id);
        
        if ($statusInfo !== false) {
            $statusInfo = json_decode(json_encode($statusInfo));

            $statusInfo->start_time_hi = date('H:i',$statusInfo->start_time);
            $statusInfo->start_time = date('Y-m-d',$statusInfo->start_time);
            $statusInfo->end_time_hi = date('H:i',$statusInfo->end_time);
            $statusInfo->end_time = date('Y-m-d',$statusInfo->end_time);
        }

        $res = [
            'status' => 200,
            'msg' => '操作成功！',
            'data' => $statusInfo,
        ];

        return json_encode($res, JSON_UNESCAPED_UNICODE);
    }

}