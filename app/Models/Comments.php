<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/12
 * Time: 15:35
 */

namespace app\Models;

use Phalcon\Mvc\Model\Resultset\Simple;
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
    public $created_time;

    public function initialize()
    {
        $this->useDynamicUpdate(true);
    }

    public function beforeCreate()
    {
        // 设置创建时间.
        $this->created_time = time();
    }

    /**
     * 根据userId查询留言.
     * @param $user_id
     * @return array|bool
     */
    public function getCommentsByUserId($user_id)
    {
        $comment_list = [];
        if (!empty($user_id)) {
            $sql = 'SELECT * FROM n_z_comments WHERE user_id = ? ORDER BY created_time DESC LIMIT 5';

            $data = new Simple(null, $this, $this->getReadConnection()->query($sql, [$user_id]));

            $comment_list = $data->valid() ? $data->toArray() : false;
        }

        return $comment_list;
    }

    /**
     * 根据userId查询留言2.
     * @param $user_id
     * @return array|bool
     */
    public function getCommentByUserId($user_id)
    {
        $comment_list = [];
        if (!empty($user_id)) {
            $sql = "SELECT to_char(to_timestamp(created_time), 'YYYY-MM-DD HH24:MI:SS') as created_time, comment_id, comment_content, comment_phone, comment_name, comment_status, user_id 
                    FROM n_z_comments 
                    WHERE user_id = ? 
                    ORDER BY created_time DESC";

            $data = new Simple(null, $this, $this->getReadConnection()->query($sql, [$user_id]));

            $comment_list = $data->valid() ? $data->toArray() : false;
        }

        return $comment_list;
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

    /**
     * 查询comments表数量（用于统计）.
     * @param $input
     * @param bool $need_relation
     * @return mixed
     */
    public function getCommentsCount($input, $need_relation = false)
    {
        $where = '';
        $bindParams = [];

        $sql = 'SELECT COUNT(*) AS AGGREGATE FROM n_z_comments AS comments INNER JOIN n_z_users AS users ON comments.user_id = users.user_id ';

        if($need_relation){
            $sql .= 'LEFT JOIN n_z_user_belongs AS userBelongs ON userBelongs.user_id = comments.user_id AND userBelongs.belong_id = ?';
            $bindParams[] = $input['user_id'];
        }

        if (!$need_relation && isset($input['user_id']) && !is_array($input['user_id'])){
            $where .= (empty($where) ? ' WHERE' : ' AND') . ' comments.user_id = ?';
            $bindParams[] = $input['user_id'];
        }

        if (isset($input['time']) && !is_array($input['time'])){
            $input['time'] = strtotime(date("y-m-d"),$input['time']);
            $where .= (empty($where) ? ' WHERE' : ' AND') . ' comments.created_time >= ?';
            $bindParams[] = $input['time'];
        }

        $sql .= $where;
        $sql1 = $sql;

        $sql .= (empty($where) ? ' WHERE' : ' AND') . ' comments.comment_status = \'1\'';
        $sql1 .= (empty($where) ? ' WHERE' : ' AND') . ' comments.comment_status = \'0\'';

        $obj = new Simple(null, $this, $this->getReadConnection()->query($sql, $bindParams));
        $obj1 = new Simple(null, $this, $this->getReadConnection()->query($sql1, $bindParams));
        $data['read'] = $obj->valid() ? $obj->toArray()[0]['aggregate'] : false;
        $data['unread'] = $obj1->valid() ? $obj1->toArray()[0]['aggregate'] : false;

        return $data;
    }

}