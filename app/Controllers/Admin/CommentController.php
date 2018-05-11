<?php
/**
 * Created by PhpStorm.
 * User: Cl
 * Date: 2018/4/18
 * Time: 14:28
 */

namespace app\Controllers\Admin;

use app\Models\Comments;
use app\Models\Departments;
use app\Models\Project;
use app\Models\Sections;

/**
 * 留言控制器
 * Class CommentController
 * @package app\Controller\Admin
 */
class CommentController extends ControllerBase
{
    /**
     * 留言列表.
     */
    public function indexAction()
    {
        // 参数.
        $page = $this->request->get('page', 'int', 1);
        $limit = $this->request->get('limit', 'int', 10);
        $input = $this->request->getQuery();

        // 规范参数, 避免查询出错.
        foreach ($input as $k => $v){
            if ($v === '') {
                $input[$k] = NULL;
            }
        }

        $user = $this->session->get('user');

        // 权限.
        if ($user['user_is_super'] || ($user['user_is_admin'] && $user['project_id'] == '')) {
            $data['project_list'] = Project::getProjectList();
            if(!empty($input['project_id'])){
                $data['department_list'] = (new Departments())->getTree(0, 0, $input['project_id']);
                $data['section_list'] = (new Sections())->getTree(0, 0, $input['project_id']);
            }
        } else {
            $input['project_id'] = $user['project_id'];
            $data['department_list'] = (new Departments())->getTree(0, 0, $user['project_id']);
            $data['section_list'] = (new Sections())->getTree(0, 0, $input['project_id']);
        }

        // 查询评论表.
        $list = (new Comments())->index($input, true, $page, $limit);

        $data['list'] = $list;

        // 页面参数.
        $this->view->setVars([
            'data' => $data,
            'input' => $input,
        ]);
    }

    /**
     * 批量修改留言状态.
     */
    public function changeAllAction()
    {
        // 获取参数.
        $commentId = $this->request->getPost('comment_id') ? explode(',', $this->request->getPost('comment_id')) : '';

        // 执行变更.
        $data = Comments::changeAllAction($commentId);

        //返回.
        if ($data !== true) {
            $this->logger->error($this->getCname() . '---' . $data);
            return $this->ajaxError('状态变更失败，请稍后重试');
        } else {
            return $this->ajaxSuccess('状态变更成功', 201);
        }
    }

    /**
     * 批量删除留言.
     */
    public function deleteAllAction()
    {
        // 获取参数.
        $commentId = $this->request->getPost('comment_id') ? explode(',', $this->request->getPost('comment_id')) : '';

        // 执行删除.
        $data = Comments::deleteAllAction($commentId);

        //返回.
        if ($data !== true) {
            $this->logger->error($this->getCname() . '---' . $data);
            return $this->ajaxError('删除失败，请稍后重试');
        } else {
            return $this->ajaxSuccess('删除成功', 201);
        }
    }

    /**
     * 修改一条留言状态.
     */
    public function changeAction()
    {
        $commentId = $this->request->getPost('comment_id');
        $commentStatus = $this->request->getPost('comment_status');

        // 查找留言.
        $Comment = Comments::findFirst($commentId);
        if ($Comment === false) {
            $this->flashSession->error('留言不存在');

            return $this->response->redirect('admin/comment');
        }

        if ($Comment->update(['comment_status' => $commentStatus]) === true) {
            return $this->ajaxSuccess('状态变更成功', 201);
        } else {
            return $this->ajaxError('状态变更失败，请稍后重试');
        }
    }

    /**
     * 删除一条留言.
     */
    public function deleteAction()
    {
        $commentId = $this->request->getPost('comment_id');

        // 查找留言.
        $Comment = Comments::findFirst($commentId);
        if ($Comment === false) {
            $this->flashSession->error('留言不存在');

            return $this->response->redirect('admin/comment');
        }

        if ($Comment->delete() === true) {
            return $this->ajaxSuccess('留言删除成功', 201);
        } else {
            return $this->ajaxError('留言删除失败，请稍后重试');
        }
    }

}

