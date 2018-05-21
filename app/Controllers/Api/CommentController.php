<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/5/17
 * Time: 18:52
 */

namespace app\Controllers\Api;

use app\Models\Comments;

/**
 * 留言控制器
 * Class CommentController
 * @package app\Controller\Api
 */
class CommentController  extends ControllerBase
{
    /**
     * 留言详情.
     */
    public function commentOneAction()
    {
        $comment_id = $this->request->get('comment_id');

        if (!$comment_id) {
            return $this->ajaxError('参数错误');
        }

        $data = (new Comments())->getDetailsByCommentId($comment_id);

        if ($this->request->isPost()) {
            $res = [
                'status' => 200,
                'msg' => '',
                'data' => $data,
            ];

            return json_encode($res, JSON_UNESCAPED_UNICODE);
        } else {
            // 页面参数.
            $this->view->setVars([
                'comment' => $data,
            ]);

            $this->view->pick($this->session->get('tpl') . '/commentdetail');

            return true;
        }
    }

    /**
     * 修改状态.
     */
    public function changeCommentStatusAction()
    {
        // 获取参数.
        $comment_id = $this->request->getPost('comment_id');

        if (!$comment_id) {
            return $this->ajaxError('参数错误');
        }

        // 查询数据.
        $comment = Comments::findFirst($comment_id);

        if (!$comment || $comment->comment_status == 1) {
            return $this->ajaxError('留言不存在或已处理');
        }

        if ($comment->update(['comment_status' => 1])) {
            return $this->ajaxSuccess('留言处理成功', 201);
        } else {
            return $this->ajaxError('留言处理失败, 请稍后重试');
        }
    }

}