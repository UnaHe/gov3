<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/4
 * Time: 17:34
 */

namespace app\Models;

use Phalcon\Mvc\Model\Transaction\Failed as TxFailed;
use Phalcon\Mvc\Model\Transaction\Manager as TxManager;

/**
 * 部门表
 * Class Departments
 * @package app\Models
 */
class Departments extends ModelBase
{
    public $created_at;

    public function beforeSave()
    {
        // 设置时间.
        $this->created_at = time();
    }

    /**
     * @param int $parent_id 父级id 默认为0顶级分类
     * @param int $spac 空格字符个数
     * @param int $project_id 项目id
     * @param bool $ignore
     * @param array $result 输出数组
     * @return array
     */
    public function getTree($parent_id = 0, $spac = 0, $project_id, $ignore = false, &$result = array())
    {
        $spac = $spac + 2;

        $builder = Departments::query();
        $builder->where('project_id = :project_id:', [
            'project_id' => $project_id
        ]);
        if($parent_id == 0){
            $builder->andWhere('parent_id IS NULL OR parent_id = 0');
        }else{
            $builder ->andWhere('parent_id = :parent_id:', [
                'parent_id' => $parent_id
            ]);
        }

        $data = $builder->execute();

        foreach ($data as $k => $v) {
            if($ignore && $v->department_id == $ignore){
                continue;
            }
            $v->department_name = str_repeat('&nbsp;&nbsp;', $spac) . '|--' . $v->department_name;
            $result[] = $v;
            $this->getTree($v->department_id, $spac, $project_id, $ignore, $result);
        }

        return $result;
    }

    /**
     * 分类列表页面的数组
     * @param int $parent_id 父级id 默认为0顶级分类
     * @param int $spac 空格字符个数
     * @param int $project_id 项目id
     * @param array $result 输出数组
     * @return array
     */
    public function getDetailTree($parent_id = 0, $spac = 0, $project_id, &$result = array())
    {
        $spac = $spac + 2;
        $builder = Departments::getModelsManager()->createBuilder()->addFrom('app\Models\Departments', 'departments');
        $builder->columns('departments.*, project.project_name, count(distinct users.user_id) as user_count, count(comments.comment_id) as comment_count');
        $builder->leftJoin('app\Models\Project', 'departments.project_id = project.project_id AND project.project_status = 1', 'project');
        $builder->leftJoin('app\Models\Users', 'departments.department_id = users.department_id AND users.user_status = 1', 'users');
        $builder->leftJoin('app\Models\Comments', 'users.user_id = comments.user_id', 'comments');

        if ($project_id) {
            $builder->andWhere('departments.project_id = :project_id:', [
                'project_id' => $project_id
            ]);
        }

        if($parent_id == 0){
            $builder->andWhere('departments.parent_id IS NULL OR departments.parent_id = 0');
        }else{
            $builder ->andWhere('departments.parent_id = :parent_id:', [
                'parent_id' => $parent_id
            ]);
        }

        $builder->groupBy('departments.department_id, project.project_name');

        $builder->orderBy('departments.project_id ASC');

        $data = $builder->getQuery()->execute();

        foreach ($data as $k => $v) {
            $v->departments->department_name = str_repeat('&nbsp;&nbsp;', $spac) . '|--' . $v->departments->department_name;
            $result[$v->departments->department_id] = $v;
            $this->getDetailTree($v->departments->department_id, $spac, $project_id, $result);
        }

        return $result;
    }

    /**
     * 分类列表页面的数组
     * @param $projectId 单位ID
     * @param $departmentId 科室ID
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public static function GetUsersByDepartmentOrOthers($projectId, $departmentId)
    {
        $builder = Users::query();
        $builder->where('project_id = :project_id:', [
            'project_id' => $projectId
        ]);

        $builder ->andWhere('department_id = :department_id: OR department_id = 0 OR department_id IS NULL', [
            'department_id' => $departmentId
        ]);
        $builder->orderBy('department_id DESC');

        $data = $builder->execute();

        return $data;
    }

    /**
     * 批量更新用户的归属科室
     * @param $departmentId
     * @param $users
     * @return bool|string
     */
    public static function updateUsersDepartment($departmentId, $users){
        // 创建事务管理.
        $manager = new TxManager();
        // 请求事务.
        $transaction = $manager->get();

        try {

            if ($users === NULL) {
                // 查询用户.
                $Users = Users::find([
                    'department_id = :department_id:',
                    'bind' =>[
                        'department_id' => $departmentId
                    ]
                ]);

                foreach ($Users as $v){
                    $v->setTransaction($transaction);

                    // 出错回滚.
                    if ($v->update(['department_id' => NUll]) === false) {
                        $messages = $v->getMessages();

                        foreach ($messages as $message) {
                            $transaction->rollback(
                                $message->getMessage()
                            );
                        }
                    }
                }
            } else {
                // 查询用户.
                $Users = Users::query()->inWhere('user_id', $users)->execute();

                foreach ($Users as $v){
                    $v->setTransaction($transaction);

                    // 出错回滚.
                    if ($v->update(['department_id' => $departmentId]) === false) {
                        $messages = $v->getMessages();

                        foreach ($messages as $message) {
                            $transaction->rollback(
                                $message->getMessage()
                            );
                        }
                    }
                }
            }

            // 保存.
            $transaction->commit();
            return true;
        } catch (TxFailed $e) {
            $transaction->rollback($e->getMessage());
            return $e->getMessage();
        }
    }

}