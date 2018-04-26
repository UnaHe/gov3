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
     * 新增告示.
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
}