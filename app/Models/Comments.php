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
use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;

/**
 * 评论表.
 * Class Comments
 * @package app\Models
 */
class Comments extends ModelBase
{
    public function initialize()
    {
        $this->useDynamicUpdate(true);
    }

    /**
     * 查询comments表.
     * @param $input
     * @param bool $isPage
     * @param int $page
     * @param int $limit
     * @return mixed|\stdClass
     */
    public function index($input = NULL, $isPage = true, $page = 1, $limit = 10)
    {
        $builder = Comments::getModelsManager()->createBuilder()->addFrom('app\Models\Comments', 'comments');
        $builder->columns('comments.*, users.user_name as user_name, project.project_name, departments.department_name, sections.section_name');
        $builder->innerJoin('app\Models\Users', 'comments.user_id = users.user_id', 'users');
        $builder->leftJoin('app\Models\Project', 'users.project_id = project.project_id', 'project');
        $builder->leftJoin('app\Models\Departments', 'users.department_id = departments.department_id', 'departments');
        $builder->leftJoin('app\Models\Sections', 'users.section_id = sections.section_id', 'sections');

        if (isset($input['user_id']) && !is_array($input['user_id'])) {
            $builder->andWhere('comments.user_id = :user_id:', [
                'user_id' => $input['user_id']
            ]);
        } else if (isset($input['user_id']) && is_array($input['user_id'])){
            $builder->inWhere('comments.user_id', $input['user_id']);
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

        if (isset($input['section_id']) && !empty($input['section_id'])) {
            $builder->andWhere('users.section_id = :section_id:', [
                'section_id' => $input['section_id']
            ]);
        }

        if (isset($input['status']) && ($input['status'] === '0' || $input['status'] === '1')) {
            $builder->andWhere('comments.comment_status = :comment_status:', [
                'comment_status' => $input['status']
            ]);
        }

        if (isset($input['key_words']) && !empty($input['key_words'])) {
            $builder->andWhere('comments.comment_content LIKE :comment_content: OR comments.comment_phone LIKE :comment_phone: OR comments.comment_name LIKE :comment_name: OR users.user_name LIKE :user_name:', [
                'comment_content' => '%' . $input['key_words'] . '%',
                'comment_phone' => '%' . $input['key_words'] . '%',
                'comment_name' => '%' . $input['key_words'] . '%',
                'user_name' => '%' . $input['key_words'] . '%',
            ]);
        }

        if (isset($input['start_time']) && !is_array($input['start_time'])){
            $builder->andWhere('comments.created_time >= :created_time:', [
                'created_time' => $input['start_time']
            ]);
        }

        if (isset($input['end_time']) && !is_array($input['end_time'])){
            $builder->andWhere('comments.created_time <= :created_time:', [
                'created_time' => $input['end_time']
            ]);
        }

        $builder->orderBy('comments.created_time DESC');

        if ($isPage) {
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
        } else {
            $data = $builder->getQuery()->execute();
        }

        return $data;
    }

    /**
     * 批量修改留言状态.
     * @param $commentId
     * @return bool|string
     */
    public static function changeAllAction($commentId)
    {
        // 创建事务管理.
        $manager = new TxManager();
        // 请求事务.
        $transaction = $manager->get();

        try {

            // 查询留言.
            $Comments = Comments::query()->inWhere('comment_id', $commentId)->execute();

            foreach ($Comments as $v){
                $v->setTransaction($transaction);

                // 执行反向操作.
                $status = $v->comment_status === '0' ? '1' : '0';

                // 出错回滚.
                if ($v->update(['comment_status' => $status]) === false) {
                    $messages = $v->getMessages();

                    foreach ($messages as $message) {
                        $transaction->rollback(
                            $message->getMessage()
                        );
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

    /**
     * 批量删除留言.
     * @param $commentId
     * @return bool|string
     */
    public static function deleteAllAction($commentId)
    {
        // 创建事务管理.
        $manager = new TxManager();
        // 请求事务.
        $transaction = $manager->get();

        try {

            // 查询留言.
            $Comments = Comments::query()->inWhere('comment_id', $commentId)->execute();

            foreach ($Comments as $v){
                $v->setTransaction($transaction);

                // 出错回滚.
                if ($v->delete() === false) {
                    $messages = $v->getMessages();

                    foreach ($messages as $message) {
                        $transaction->rollback(
                            $message->getMessage()
                        );
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