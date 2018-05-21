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
 * 告示表
 * Class Notices
 * @package app\Models
 */
class Notices extends ModelBase
{
    public $created_at;

    public function initialize()
    {
        $this->useDynamicUpdate(true);
    }

    public function beforeCreate()
    {
        // 设置时间.
        $this->created_at = time();
    }

    /**
     * 获取告示列表
     * @param $input
     * @param int $page
     * @param int $limit
     * @return \stdClass
     */
    public function getList($input, $page = 1, $limit = 10)
    {
        $where = '';
        $params = [];

        if (isset($input['department_id']) && !empty($input['department_id'])) {
            $sql = 'SELECT notices.*, project.project_name, d.department_name 
                    FROM n_z_notices AS notices 
                    LEFT JOIN n_z_department_notices AS dn ON dn.notice_id = notices.notice_id 
                    INNER JOIN n_z_departments AS d ON dn.department_id = d.department_id ';
        } else {
            $sql = 'SELECT notices.*, project.project_name, b.department_name 
                    FROM n_z_notices AS notices 
                    LEFT JOIN (
                      SELECT dn.notice_id AS n_id, GROUP_CONCAT(d.department_name) AS department_name 
                      FROM n_z_department_notices AS dn 
                      LEFT JOIN n_z_departments AS d ON d.department_id = dn.department_id 
                      GROUP BY dn.notice_id
                    ) AS b ON b.n_id = notices.notice_id ';
        }

        $sql .= 'LEFT JOIN n_z_project AS project on notices.project_id = project.project_id ';

        if (isset($input['project_id']) && !empty($input['project_id'])) {
            $where .= (empty($where) ? ' WHERE' : ' AND') . ' notices.project_id = ?';
            $params[] = $input['project_id'];
        }

        if (isset($input['department_id']) && !empty($input['department_id'])) {
            $where .= (empty($where) ? ' WHERE' : ' AND') . ' dn.department_id = ?';
            $params[] = $input['department_id'];
        }

        $sql .= $where . ' ORDER BY notices.created_at DESC, notices.project_id ASC ';

        $data = new Simple(null, $this, $this->getReadConnection()->query($sql, $params));

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
     * 保存公告部门列表
     * @param $notice_id
     * @param array $departments
     * @return bool
     */
    public function updateDepartmentNotice($notice_id, $departments = [])
    {
        if(empty($notice_id)){
            return false;
        }

        // 创建事务管理.
        $manager = new TxManager();
        // 请求事务.
        $transaction = $manager->get();

        try{

            $Dn = DepartmentNotices::find([
                'notice_id = :notice_id:',
                'bind' => [
                    'notice_id' => $notice_id
                ]
            ]);

            foreach ($Dn as $v) {
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

            if (!empty($departments)) {
                // 插入SQL.
                $sql = '';
                foreach ($departments as $v) {
                    $sql .= "($notice_id, $v), ";
                }

                // PDO.
                $execute = (new DepartmentNotices)->getDI()->get('db');
                $res = $execute->execute('INSERT INTO n_z_department_notices (notice_id, department_id) VALUES '. rtrim($sql, ', '));

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

    /**
     * 保存告示
     * @param $param
     * @param bool $departments
     * @return string
     */
    public static function addNotice($param, $departments = false)
    {
        if ($departments === false) {
            $departments = Departments::find([
                'project_id = :project_id:',
                'columns' => 'department_id, department_name',
                'bind' => [
                    'project_id' => $param['project_id'],
                ]
            ])->toArray();
        }

        // 创建事务管理.
        $manager = new TxManager();
        // 请求事务.
        $transaction = $manager->get();

        try{

            $Notices = new Notices();

            $Notices->setTransaction($transaction);

            // 创建告示.
            if ($Notices->create($param) === false) {

                foreach ($Notices->getMessages() as $message) {
                    $transaction->rollback(
                        $message->getMessage()
                    );
                }
            }

            // 创建部门告示关联表.
            $noticeId = $Notices->notice_id;
            if (!empty($departments)) {
                $sql = '';
                foreach ($departments as $v) {
                    $sql .= "($noticeId, $v), ";
                }

                // PDO.
                $execute = (new DepartmentNotices)->getDI()->get('db');
                $res = $execute->execute('INSERT INTO n_z_department_notices (notice_id, department_id) VALUES '. rtrim($sql, ', '));

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

    /**
     * 更新公告部门列表
     * @param $noticeId
     * @param $param
     * @param bool $departments
     * @return string
     */
    public static function updateNotice($noticeId, $param, $departments = false)
    {
        // 创建事务管理.
        $manager = new TxManager();
        // 请求事务.
        $transaction = $manager->get();
        try{

            $Notices = new Notices();

            $Notices->setTransaction($transaction);

            $notice = $Notices::findFirst($noticeId);

            if ($notice->update($param) ===false) {
                foreach ($notice->getMessages() as $message) {
                    $transaction->rollback(
                        $message->getMessage()
                    );
                }
            }

            $Dn = DepartmentNotices::find([
                'notice_id = :notice_id:',
                'bind' => [
                    'notice_id' => $noticeId
                ]
            ]);

            foreach ($Dn as $v) {
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

            if (!empty($departments)) {
                // 插入SQL.
                $sql = '';
                foreach ($departments as $v) {
                    $sql .= "($noticeId, $v), ";
                }

                // PDO.
                $execute = (new DepartmentNotices)->getDI()->get('db');
                $res = $execute->execute('INSERT INTO n_z_department_notices (notice_id, department_id) VALUES '. rtrim($sql, ', '));

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

    /**
     * 获取部门的告示列表.
     * @param $input
     * @param int $page
     * @param int $limit
     * @return array|Simple
     */
    public function getListByDepartment($input, $page = 1, $limit = 10)
    {
        $notice_list = [];
        if (isset($input['department_id'])) {
            $sql = "SELECT to_char(to_timestamp(n_z_notices.created_at), 'YYYY/MM/DD') as created_at, n_z_notices.notice_id, n_z_notices.notice_content, n_z_notices.notice_status, n_z_notices.notice_title 
                FROM n_z_notices 
                LEFT JOIN n_z_department_notices AS dn ON dn.notice_id = n_z_notices.notice_id 
                WHERE dn.department_id = ? AND n_z_notices.notice_status = '1' 
                ORDER BY n_z_notices.created_at DESC 
                LIMIT ? 
                OFFSET ?";

            $offset = ($page - 1) * $limit;

            $notice_list = new Simple(null, $this, $this->getReadConnection()->query($sql, [$input['department_id'], $limit, $offset]));
        }

        return $notice_list;
    }

}