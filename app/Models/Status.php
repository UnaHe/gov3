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
 * 状态表
 * Class Status
 * @package app\Models
 */
class Status extends ModelBase
{
    public $created_at;

    public function initialize()
    {
        $this->useDynamicUpdate(true);
    }

    public function beforeSave()
    {
        // 设置时间.
        $this->created_at = time();
    }

    /**
     * 根据项目ID获取事务(除默认事件外).
     * @param $project_id
     * @return mixed
     */
    public static function getListByProjectId($project_id){
        $status_list = Status::find([
            'project_id = :project_id: AND (status_is_default IS NULL OR status_is_default = 0)',
            'bind' => [
                'project_id' => $project_id
            ],
            'order' => 'status_order ASC'
        ]);

        return $status_list;
    }

    /**
     * 获取事务列表.
     * @param bool $project_id
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function getList($project_id = false, $page = 1, $limit = 10)
    {
        $builder = Status::getModelsManager()->createBuilder()->addFrom('app\Models\Status', 'status');
        $builder->columns('status.*, project.project_name as project_name, users.user_name as created_name');
        $builder->leftJoin('app\Models\Project','status.project_id = project.project_id', 'project');
        $builder->leftJoin('app\Models\Users','status.created_id = users.user_id', 'users');

        if($project_id){
            $builder->andWhere('project.project_id = :project_id: OR status.project_id = 0 OR status.project_id IS NULL', [
                'project_id' => $project_id
            ]);
        }
        $builder->orderBy('status.project_id ASC, status.status_order ASC, status.status_id ASC');

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

    /**
     * 修改事务的默认事件
     * @param $project_id
     * @param $status_id
     * @param $is_default
     * @return bool
     */
    public static function setDefault($project_id, $status_id, $is_default)
    {
        if (empty($project_id) || empty($status_id) || empty($is_default) || $is_default <= 0) {
            return false;
        }

        // 创建事务管理.
        $manager = new TxManager();
        // 请求事务.
        $transaction = $manager->get();

        try {

            $Status = new Status();

            $Status->setTransaction($transaction);

            $status = $Status::findFirst([
                'project_id = :project_id: AND status_is_default = :status_is_default:',
                'bind' => [
                    'project_id' => $project_id,
                    'status_is_default' => $is_default,
                ]
            ]);

            // 出错回滚.
            if ($status->update(['status_is_default' => 0]) === false) {
                $messages = $status->getMessages();

                foreach ($messages as $message) {
                    $transaction->rollback(
                        $message->getMessage()
                    );
                }
            }

            $status2 = $Status::findFirst([
                'project_id = :project_id: AND status_id = :status_id:',
                'bind' => [
                    'project_id' => $project_id,
                    'status_id' => $status_id,
                ]
            ]);

            // 出错回滚.
            if ($status2->update(['status_is_default' => $is_default]) === false) {
                $messages = $status2->getMessages();

                foreach ($messages as $message) {
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
     * 获取项目默认事件列表.
     * @param $projects
     * @return mixed
     */
    public static function getDefaultStatusByProject($projects)
    {
        $status_list = [];
        $status = new Status();

        if (!empty($projects)) {
            if (is_array($projects)) {

                $params = '';
                foreach ($projects as $v) {
                    $params .= $v . ',';
                }
                $params = rtrim($params, ',');

                $sql = 'select "n_z_status".* from "n_z_status" where "status_is_default" = 1 or "status_is_default" = 2 and "n_z_status"."project_id" in ('.$params.') or "n_z_status"."project_id" = 0';

                $status_list = new Simple(null, $status, $status->getReadConnection()->query($sql));
            } else {
                $sql = 'select "n_z_status".* from "n_z_status" where "status_is_default" = 1 or "status_is_default" = 2 and "n_z_status"."project_id" = ? or "n_z_status"."project_id" = 0';

                $status_list = new Simple(null, $status, $status->getReadConnection()->query($sql, $projects));
            }
        }

        return $status_list;
    }

}