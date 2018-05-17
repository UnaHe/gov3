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

        $data = (new Comments())->getDetailsByCommentId($comment_id);
    }

    /**
     * 修改状态.
     */
    public function changeCommentStatusAction()
    {

    }
}