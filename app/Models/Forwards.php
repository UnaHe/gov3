<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/12
 * Time: 15:35
 */

namespace app\Models;

use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;

/**
 * 二维码链接关系
 * Class Forwards
 * @package app\Models
 */
class Forwards extends ModelBase
{
    public function initialize()
    {
        $this->useDynamicUpdate(true);
    }

    /**
     * 获取告示列表
     * @param $input
     * return array
     * @param int $page
     * @param int $limit
     * @return \stdClass
     */
    public function getList($input, $page = 1, $limit = 10)
    {
        $builder = Forwards::getModelsManager()->createBuilder()->addFrom('app\Models\Forwards', 'forwards');
        $builder->columns('forwards.* , project.project_name as project_name, departments.department_name');
        $builder->leftJoin('app\Models\Project','forwards.project_id = project.project_id', 'project');
        $builder->leftJoin('app\Models\Departments','forwards.department_id = departments.department_id', 'departments');

        if(isset($input['project_id']) && !empty($input['project_id'])){
            $builder->andWhere('forwards.project_id = :project_id:', [
                'project_id' => $input['project_id']
            ]);
        }

        if(isset($input['department_id']) && !empty($input['department_id'])){
            $builder->andWhere('forwards.department_id = :department_id:',[
                'department_id' => $input['department_id']
            ]);
        }

        $builder->orderBy('forwards.project_id ASC, forwards.forward_id ASC');

        // 分页.
        $paginator = new PaginatorQueryBuilder(
            array(
                "builder" => $builder,
                "limit"   => $limit,
                "page"    => $page
            )
        );

        // 获取分页数据.
        $data = $paginator->getPaginate();

        return $data;
    }
}