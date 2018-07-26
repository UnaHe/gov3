<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/12
 * Time: 15:35
 */

namespace app\Models;

use Phalcon\Mvc\Model\Transaction\Failed as TxFailed;
use Phalcon\Mvc\Model\Transaction\Manager as TxManager;

/**
 * 部门表
 * Class Sections
 * @package app\Models
 */
class Sections extends ModelBase
{
    public function initialize()
    {
        $this->useDynamicUpdate(true);
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

        $builder = Sections::query();
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
            if($ignore && $v->section_id == $ignore){
                continue;
            }
            $v->section_name = str_repeat('&nbsp;&nbsp;', $spac) . '|--' . $v->section_name;
            $result[] = $v;
            $this->getTree($v->section_id, $spac, $project_id, $ignore, $result);
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
        $builder = Sections::getModelsManager()->createBuilder()->addFrom('app\Models\Sections', 'sections');
        $builder->columns('sections.*, project.project_name, count(distinct users.user_id) as user_count, count(comments.comment_id) as comment_count');
        $builder->leftJoin('app\Models\Project', 'sections.project_id = project.project_id AND project.project_status = 1', 'project');
        $builder->leftJoin('app\Models\Users', 'sections.section_id = users.section_id AND users.user_status = 1', 'users');
        $builder->leftJoin('app\Models\Comments', 'users.user_id = comments.user_id', 'comments');

        if ($project_id) {
            $builder->andWhere('sections.project_id = :project_id:', [
                'project_id' => $project_id
            ]);
        }

        if($parent_id == 0){
            $builder->andWhere('sections.parent_id IS NULL OR sections.parent_id = 0');
        }else{
            $builder ->andWhere('sections.parent_id = :parent_id:', [
                'parent_id' => $parent_id
            ]);
        }

        $builder->groupBy('sections.section_id, project.project_name');

        $builder->orderBy('sections.project_id ASC');

        $data = $builder->getQuery()->execute();

        foreach ($data as $k => $v) {
            $v->sections->section_name = str_repeat('&nbsp;&nbsp;', $spac) . '|--' . $v->sections->section_name;
            $result[$v->sections->section_id] = $v;
            $this->getDetailTree($v->sections->section_id, $spac, $project_id, $result);
        }

        return $result;
    }

    /**
     * 根据部门id得到部门所属的员工和没有分配部门的员工列表
     * @param int $projectId 单位ID
     * @param int $sectionId 部门ID
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public static function GetUsersBySectionOrOthers($projectId,$sectionId){
        $builder = Users::query();
        $builder->where('project_id = :project_id:', [
            'project_id' => $projectId
        ]);

        $builder ->andWhere('section_id = :section_id: OR section_id = 0 OR section_id IS NULL', [
            'section_id' => $sectionId
        ]);
        $builder->orderBy('section_id DESC');

        $data = $builder->execute();

        return $data;
    }

    /**
     * 根据部门id得到部门所属的员工和没有分配部门的员工列表
     * @param int $sectionId 部门ID
     * @param int|array $users 人员
     * @return bool|string
     */
    public static function updateUsersSection($sectionId, $users){
        // 创建事务管理.
        $manager = new TxManager();
        // 请求事务.
        $transaction = $manager->get();

        try {

            if ($users === NULL) {
                // 查询用户.
                $Users = Users::find([
                    'section_id = :section_id:',
                    'bind' =>[
                        'section_id' => $sectionId
                    ]
                ]);

                foreach ($Users as $v){
                    $v->setTransaction($transaction);

                    // 出错回滚.
                    if ($v->update(['section_id' => NUll]) === false) {
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
                    if ($v->update(['section_id' => $sectionId]) === false) {
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