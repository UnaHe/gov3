<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/12
 * Time: 15:35
 */

namespace app\Models;

use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;
use stdClass;

/**
 * 用户状态表.
 * Class UserStatus
 * @package app\Models
 */
class UserStatus extends ModelBase
{
    public function initialize()
    {
        $this->useDynamicUpdate(true);
    }

    /**
     * 获取冲突的数据
     * @param $user_id
     * @param $start_time
     * @param $end_time
     * @param bool $user_status_id
     * @return bool 没有冲突返回true
     */
    public static function getConflict($user_id, $start_time, $end_time, $user_status_id = false)
    {
        $builder = UserStatus::query();
        $builder->where('user_id = :user_id:', [
            'user_id' => $user_id
        ]);

        $builder->andWhere('(start_time <= :start_time: AND end_time >= :end_time:) OR (start_time > :start_time: AND start_time < :end_time:) OR (end_time > :start_time: AND end_time < :end_time:)', [
            'start_time' => $start_time,
            'end_time' => $end_time
        ]);

        if ($user_status_id) {
            $builder->andWhere('user_status_id != :user_status_id:', [
                'user_status_id' => $user_status_id
            ]);
        }
        $data = $builder->execute()->toArray();

        return count($data) ? false : true;
    }

    /**
     * 获取为员工添加的事件
     * @param $input
     * @param bool $need_relation
     * @param int $page
     * @param int $limit
     * @return stdClass
     */
    public function getUserStatusList($input, $need_relation = false, $page = 1, $limit = 10)
    {
//        echo '<pre>';
//        var_dump($input);
//        die;
        $builder = UserStatus::getModelsManager()->createBuilder()->addFrom('app\Models\UserStatus', 'user_status');
        $builder->columns('user_status.*, status.status_id, status.status_name, status.status_color, users.user_name, users.user_phone, users.project_id, project.project_name, departments.department_name');
        $builder->leftjoin('app\Models\Status', 'status.status_id = user_status.status_id', 'status');
        $builder->leftjoin('app\Models\Users', 'users.user_id = user_status.user_id', 'users');
        $builder->leftjoin('app\Models\Project', 'project.project_id = users.project_id', 'project');
        $builder->leftjoin('app\Models\Departments', 'departments.department_id = users.department_id', 'departments');

        if($need_relation){
            $builder->leftjoin('app\Models\UserBelongs', 'user_belongs.user_id = users.user_id AND user_belongs.belong_id = ' . $input['user_id'], 'user_belongs');

            if (isset($input['time']) && !empty($input['time'])) {
                $builder->andWhere('user_status.start_time <= :start_time: AND user_status.end_time >= :end_time:', [
                    'start_time' => $input['time'],
                    'end_time' => $input['time'],
                ]);
            }
        }

        if (isset($input['project_id']) && !empty($input['project_id'])) {
            $builder->andWhere('users.project_id = :project_id:', [
                'project_id' => $input['project_id']
            ]);
        }

        if (isset($input['department_id']) && !empty($input['department_id'])) {
            $builder->andWhere('users.department_id = :department_id:', [
                'department_id' => $input['department_id']
            ]);
        }

        if (isset($input['status_id']) && !empty($input['status_id'])) {
            $builder->andWhere('user_status.status_id = :status_id:', [
                'status_id' => $input['status_id']
            ]);
        }

        if (isset($input['start_time']) && !empty($input['start_time'])) {
            $builder->andWhere('user_status.start_time >= :start_time:', [
                'start_time' => strtotime($input['start_time']),
            ]);
            if (!isset($input['end_time'])) {
                $builder->orwhere('user_status.end_time >= :end_time:', [
                    'end_time' => strtotime($input['start_time']),
                ]);
            }
        }

        if (isset($input['end_time']) && !empty($input['end_time'])) {
            $builder->andWhere('user_status.end_time <= :end_time:', [
                'end_time' => strtotime($input['end_time']),
            ]);
        }

        if (isset($input['user_name']) && !empty($input['user_name'])) {
            $builder->andWhere('users.user_name LIKE :user_name:', [
                'user_name' => '%' . $input['user_name'] . '%',
            ]);
        }

        $builder->orderBy('users.project_id ASC ,users.department_id ASC, user_status.start_time DESC');

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