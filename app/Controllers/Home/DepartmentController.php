<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/5/14
 * Time: 18:19
 */

namespace app\Controllers\Home;

use app\Models\Departments;
use app\Models\Project;

/**
 * 科室控制器
 * Class DepartmentController
 * @package app\Controller\Home
 */
class DepartmentController extends ControllerBase
{
    /**
     * 单位介绍.
     */
    public function projectDetailAction()
    {
        $project_info = Project::findFirst(self::$project_id);

        $this->view->setVars([
            'project_info' => $project_info,
        ]);
    }

    /**
     * 科室列表.
     */
    public function departmentListAction()
    {

    }

    public function ajaxDepartmentListAction()
    {
        // 获取分页参数.
        $page = $this->request->get('page', 'int', 1);
        $limit = $this->request->get('limit', 'int', 10);

        $list = (new Departments())->getDepartmentsByProject(self::$project_id, true, $page, $limit);

        $data = [
            'status' => 200,
            'msg' => '',
            'data' => $list,
        ];

        // 返回ajax.
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    // 科室介绍.
    public function detailAction()
    {
        $department_info = Departments::findFirst([
            'department_id = :department_id:',
            'bind' => [
                'department_id' => self::$department_id,
            ],
            'columns' => 'department_name, department_desc, created_at'
        ]);

        $this->view->setVars([
            'department_info' => $department_info,
        ]);
    }

}