<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/5/2
 * Time: 16:05
 */

namespace app\Models;

use Phalcon\Mvc\Model\Transaction\Failed as TxFailed;
use Phalcon\Mvc\Model\Transaction\Manager as TxManager;

/**
 * 角色权限关联表
 * Class AdminPermissionRole
 * @package app\Models
 */
class AdminPermissionRole extends ModelBase
{
    public function initialize()
    {
        $this->useDynamicUpdate(true);
    }

    /**
     * 保存角色权限.
     * @param $roleId
     * @param array $permissions
     * @return bool
     */
    public function saveRolePermission($roleId, $permissions = [])
    {
        if(empty($roleId)){
            return false;
        }

        // 创建事务管理.
        $manager = new TxManager();
        // 请求事务.
        $transaction = $manager->get();

        try{

            $apr = AdminPermissionRole::find([
                'role_id = :role_id:',
                'bind' => [
                    'role_id' => $roleId
                ]
            ]);

            foreach ($apr as $v) {
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

            if (!empty($permissions)) {
                // 插入SQL.
                $sql = '';
                foreach ($permissions as $v) {
                    $sql .= "($roleId, $v), ";
                }

                // PDO.
                $execute = (new AdminPermissionRole)->getDI()->get('db');
                $res = $execute->execute('INSERT INTO n_z_admin_permission_role (role_id, permission_id) VALUES '. rtrim($sql, ', '));

                if ($res !== true) {
                    foreach ($res->getMessages() as $message) {
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