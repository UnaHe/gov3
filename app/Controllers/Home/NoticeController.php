<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/5/14
 * Time: 17:00
 */

namespace app\Controllers\Home;

use app\Models\Notices;

/**
 * 告示控制器
 * Class NoticeController
 * @package app\Controller\Home
 */
class NoticeController extends ControllerBase
{
    /**
     * 告示列表.
     */
    public function indexAction()
    {

    }

    /**
     * 告示列表.
     */
    public function ajaxIndexAction()
    {
        // 获取分页参数.
        $page = $this->request->get('page', 'int', 1);
        $limit = $this->request->get('limit', 'int', 10);

        $input = $this->request->getPost();

        $input['department_id'] = self::$department_id;

        // 查询告示.
        $list = (new Notices())->getListByDepartment($input, $page, $limit);

        $data = [
            'status' => 200,
            'msg' => '',
            'data' => $list,
        ];

        // 返回ajax.
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 告示详情.
     */
    public function detailAction()
    {
        // 获取参数.
        $notice_id = $this->request->getQuery('notice_id');
        if($notice_id){
            // 查找公告.
            $data = Notices::findFirst($notice_id);

            $this->view->setVars([
                'data' => $data,
            ]);

            return true;
        }

        return false;
    }
}