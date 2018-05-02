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
use Phalcon\Mvc\Model\Resultset\Simple;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

/**
 * 用户所属领导表.
 * Class UserBelongs
 * @package app\Models
 */
class UserBelongs extends ModelBase
{
    public function initialize()
    {
        $this->useDynamicUpdate(true);
    }

    /**
     * 获取列表.
     * @param $input
     * @param $page
     * @param $limit
     * @return mixed
     */
    public function getList($input, $page, $limit)
    {
        $where = '';
        $bindParams = [];
        if (isset($input['project_id']) && !empty($input['project_id'])) {
            $where .= (empty($where) ? ' WHERE' : ' AND') . ' n_z_users.project_id=?';
            $bindParams[] = $input['project_id'];
        }
        if (isset($input['department_id']) && !empty($input['department_id'])) {
            $where .= (empty($where) ? ' WHERE' : ' AND') . ' n_z_users.department_id=?';
            $bindParams[] = $input['department_id'];
        }

        $sql = 'SELECT "n_z_user_belongs"."belong_id","n_z_users"."user_name","n_z_users"."user_job","n_z_project"."project_name","n_z_departments"."department_name",b.users 
                FROM "n_z_user_belongs" 
                LEFT JOIN "n_z_users" ON "n_z_user_belongs"."belong_id"="n_z_users"."user_id" 
                LEFT JOIN "n_z_project" ON "n_z_users"."project_id"="n_z_project"."project_id" 
                LEFT JOIN "n_z_departments" ON "n_z_users"."department_id"="n_z_departments"."department_id" 
                LEFT JOIN (
                                SELECT ub.belong_id,GROUP_CONCAT (u.user_name) users 
                                FROM n_z_user_belongs AS ub 
                                LEFT JOIN n_z_users AS u ON u.user_id=ub.user_id 
                                GROUP BY ub.belong_id
                            ) b ON b.belong_id="n_z_user_belongs"."belong_id" 
                ' . $where . '
                GROUP BY "n_z_user_belongs"."belong_id","n_z_users"."user_name","n_z_users"."user_job","n_z_project"."project_name","n_z_departments"."department_name","n_z_project"."project_id",b.users 
                ORDER BY "n_z_project"."project_id" ASC';

        $data = new Simple(null, $this, $this->getReadConnection()->query($sql, $bindParams));

        $paginator = new PaginatorModel(
            [
                "data"  => $data,
                "limit" => $limit,
                "page"  => $page,
            ]
        );

        // 获取分页数据.
        $data = $paginator->getPaginate();

        return $data;
    }

    /**
     * 新增归属.
     * @param $user_id
     * @param $users
     * @return bool|string
     */
    public function addBelong($user_id, $users)
    {
        if (empty($user_id) || (!is_array($users) && !count($users))) {
            return false;
        }

        // 创建事务管理.
        $manager = new TxManager();
        // 请求事务.
        $transaction = $manager->get();

        try {

            $UserBelongs = UserBelongs::find([
                'belong_id = :belong_id:',
                'bind' => [
                    'belong_id' => $user_id
                ]
            ]);

            foreach ($UserBelongs as $v) {
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

            $sql = '';
            foreach ($users as $v){
                $sql .= "($user_id, $v), ";
            }

            // PDO.
            $execute = (new DepartmentNotices)->getDI()->get('db');
            $res = $execute->execute('INSERT INTO n_z_user_belongs (belong_id, user_id) VALUES '. rtrim($sql, ', '));

            if ($res !== true) {
                foreach ($res->getMessages() as $message) {
                    $transaction->rollback(
                        $message->getMessage()
                    );
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
     * 删除归属.
     * @param $userId
     * @return bool
     * @throws \Exception
     */
    public function belongsDelete($belongId)
    {
        if (empty($belongId)) {
            return false;
        }

        // 创建事务管理.
        $manager = new TxManager();
        // 请求事务.
        $transaction = $manager->get();

        try {

            // 查询归属表.
            $UserBelongs = UserBelongs::find([
                'belong_id = :belong_id:',
                'bind' => [
                    'belong_id' => $belongId
                ]
            ]);

            // 执行删除.
            foreach ($UserBelongs as $v) {
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