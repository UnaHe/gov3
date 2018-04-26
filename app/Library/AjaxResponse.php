<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/25
 * Time: 17:34
 */

namespace app\Library;

trait AjaxResponse
{
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

}