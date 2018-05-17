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
use Phalcon\Mvc\Model\Resultset\Simple;
use Phalcon\Paginator\Adapter\QueryBuilder as PaginatorQueryBuilder;
use stdClass;

/**
 * 用户表
 * Class Users
 * @package app\Models
 */
class Users extends ModelBase
{
    public $created_at;
    public $updated_at;

    public function initialize()
    {
        $this->useDynamicUpdate(true);
    }

    public function beforeCreate()
    {
        // 设置时间.
        $this->created_at = date('Y-m-d H:i:s');
    }

    public function beforeUpdate()
    {
        $this->updated_at = date('Y-m-d H:i:s');
    }


    /**
     * 根据手机号查询相应信息.
     * @param $tel
     * @return array|bool
     */
    public function getDetailsByTel($tel)
    {
        $user_info = [];
        if (!empty($tel)) {
            $sql = "SELECT users.*, project.project_id, project.project_name, departments.department_id, departments.department_name, project.work_start_time, project.work_end_time, roleuser.role_id, role.code as role_code
                    FROM n_z_users AS users
                    LEFT JOIN n_z_project AS project ON project.project_id = users.project_id
                    LEFT JOIN n_z_departments AS departments ON departments.department_id = users.department_id
                    LEFT JOIN n_z_admin_role_user AS roleuser ON roleuser.user_id = users.user_id
                    LEFT JOIN n_z_admin_roles AS role ON role.id = roleuser.role_id
                    WHERE users.user_phone = ? AND users.user_status <> 0";

            $data = new Simple(null, $this, $this->getReadConnection()->query($sql, [$tel]));

            $user_info = $data->valid() ? $data->toArray()[0] : false;
        }

        return $user_info;
    }

    /**
     * 根据手机号查询相应信息2.
     * @param $tel
     * @return array|bool
     */
    public function getUserDetailsByTel($tel)
    {
        $user_info = [];
        if (!empty($tel)) {
            $sql = 'SELECT users.user_id, users.user_name, users.user_age, users.user_sex, users.user_intro, users.user_status, users.user_phone, users.user_image, users.user_job, users.remember_token, users.user_comments, project.project_id, project.project_name, departments.department_id, departments.department_name, project.work_start_time, project.work_end_time 
                    FROM n_z_users AS users
                    LEFT JOIN n_z_project AS project ON project.project_id = users.project_id
                    LEFT JOIN n_z_departments AS departments ON departments.department_id = users.department_id 
                    WHERE users.user_phone = ? AND project.project_status = 1 AND users.user_status <> 0';

            $data = new Simple(null, $this, $this->getReadConnection()->query($sql, [$tel]));

            $user_info = $data->valid() ? $data->toArray()[0] : false;
        }

        return $user_info;
    }

    /**
     * 获取用户和项目等详细信息.
     * @param $user_id
     * @return array|bool
     */
    public function getDetailById($user_id)
    {
        $user_info = [];
        if (!empty($user_id)) {
            $sql = "SELECT users.user_id, users.user_name, users.user_job, users.user_image, users.user_phone, users.user_sex, users.user_intro, users.user_comments, users.user_age, users.user_status, project.project_id, project.project_name, users.department_id, users.section_id
                    FROM n_z_users AS users
                    LEFT JOIN n_z_project AS project ON project.project_id = users.project_id
                    WHERE users.user_id = ?";

            $data = new Simple(null, $this, $this->getReadConnection()->query($sql, [$user_id]));

            $user_info = $data->valid() ? $data->toArray()[0] : false;
        }

        return $user_info;
    }

    /**
     * 管理员列表.
     * @param $input
     * @param int $page
     * @param int $limit
     * @return \stdClass
     */
    public function getAdminUsers($input = NULL, $page = 1, $limit = 10)
    {
        $builder = Users::getModelsManager()->createBuilder()->addFrom('app\Models\Users', 'users');
        $builder->columns('users.*, departments.department_name as cate_name, project.project_name');
        $builder->leftJoin('app\Models\Departments', 'users.department_id = departments.department_id', 'departments');
        $builder->leftJoin('app\Models\Project', 'users.project_id = project.project_id', 'project');

        $builder->where('users.user_is_admin = :user_is_admin:', [
            'user_is_admin' => 1
        ]);

        if (isset($input['project_id']) && !empty($input['project_id'])) {
            $builder->andWhere('users.project_id = :project_id:', [
                'project_id' => $input['project_id'],
            ]);
        }

        if (isset($input['admin_type']) && !empty($input['admin_type'])) {
            if ($input['admin_type'] == 1) {  //单位管理员
                $builder->andWhere('users.project_id > 0');
            } elseif ($input['admin_type'] == 2) { //系统管理员
                $builder->andWhere('users.project_id IS NULL OR users.project_id = 0');
            }
        }

        if (isset($input['keywords']) && !empty($input['keywords'])) {
            $builder->andWhere('users.user_name LIKE :user_name: OR users.user_phone LIKE :user_phone: OR project.project_name LIKE :project_name:', [
                'user_name' => '%' . $input['keywords'] . '%',
                'user_phone' => '%' . $input['keywords'] . '%',
                'project_name' => '%' . $input['keywords'] . '%',
            ]);
        }

        $builder->orderBy('users.user_id DESC');

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
     * 删除管理员以及相关信息.
     * @param $userId
     * @return bool
     * @throws \Exception
     */
    public static function deleteAdmin($userId)
    {
        if (empty($userId)) {
            return false;
        }

        // 创建事务管理.
        $manager = new TxManager();
        // 请求事务.
        $transaction = $manager->get();

        try {

            // 查询用户角色表.
            $AdminRoleUsers = AdminRoleUser::find([
                'user_id = :user_id:',
                'bind' => [
                    'user_id' => $userId
                ]
            ]);
            // 执行删除.
            foreach ($AdminRoleUsers as $v) {
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

            // 查询用户状态表.
            $UserStatus = UserStatus::find([
                'user_id = :user_id:',
                'bind' => [
                    'user_id' => $userId
                ]
            ]);
            // 执行删除.
            foreach ($UserStatus as $v) {
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

            // 查询用户所属表.
            $UserBelongs = UserBelongs::find([
                'user_id = :user_id:',
                'bind' => [
                    'user_id' => $userId
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

            // 查询评论表.
            $Comments = Comments::find([
                'user_id = :user_id:',
                'bind' => [
                    'user_id' => $userId
                ]
            ]);
            // 执行删除.
            foreach ($Comments as $v) {
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

            // 查询用户表.
            $Users = Users::find([
                'user_id = :user_id:',
                'bind' => [
                    'user_id' => $userId
                ]
            ]);
            // 执行删除.
            foreach ($Users as $v) {
                $v->setTransaction($transaction);

                // 删除上传图片.
                @unlink(BASE_PATH . '/public/upload/' . $v->user_image);

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
     * 获取项目用户.
     * @param array $input
     * @param bool $isPage
     * @param int $page
     * @param int $limit
     * @param bool $user_status
     * @return mixed|stdClass
     */
    public function getProjectUsersByProject($input = [], $isPage = true, $page = 1, $limit = 10, $user_status = false)
    {
        $builder = Users::getModelsManager()->createBuilder()->addFrom('app\Models\GetUserDetail', 'a');

        if ($user_status) {
            $builder->columns('a.*, user_status.user_status_id, user_status.start_time, user_status.end_time, user_status.user_status_desc, status.status_id, status.status_name, status.status_color');
            $builder->leftjoin('app\Models\UserStatus', 'a.user_id = user_status.user_id AND user_status.start_time <= ' . $input['time'] . ' AND user_status.end_time >= ' . $input['time'], 'user_status');
            $builder->leftjoin('app\Models\Status', 'user_status.status_id = status.status_id', 'status');
            $builder->where('a.user_status = 1');
        }

        $builder->andWhere('a.project_id IS NOT NULL AND a.project_id > 0');

        if (isset($input['project_id']) && !empty($input['project_id'])) {
            $builder->andWhere('a.project_id = :project_id:', [
                'project_id' => $input['project_id']
            ]);
        }

        if (isset($input['department_id']) && !empty($input['department_id'])) {
            $builder->andWhere('a.department_id = :department_id:', [
                'department_id' => $input['department_id']
            ]);
        }

        if (isset($input['section_id']) && !empty($input['section_id'])) {
            $builder->andWhere('a.section_id = :section_id:', [
                'section_id' => $input['section_id']
            ]);
        }

        if (isset($input['user_name']) && !empty($input['user_name'])) {
            $builder->andWhere('a.user_name LIKE :user_name:', [
                'user_name' => '%' . $input['user_name'] . '%'
            ]);
        }

        if ($isPage) {
            $builder->orderBy('a.project_id DESC, a.department_id DESC, a.user_is_admin ASC, a.user_job ASC, a.user_is_leader ASC');

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
            $data = $builder->orderBy('a.department_id DESC')->getQuery()->execute();
        }

        return $data;
    }

    /**
     * 用户与分类关联.
     * @param array $input
     * @param int $page
     * @param int $limit
     * @return mixed|stdClass
     */
    public function getRelationWithCategory($input, $page = 1, $limit = 10)
    {
        $builder = Users::getModelsManager()->createBuilder()->addFrom('app\Models\Users', 'users');
        $builder->columns('users.*, departments.department_name as cate_name, project.project_name, sections.section_name');
        $builder->leftjoin('app\Models\Departments', 'users.department_id = departments.department_id', 'departments');
        $builder->leftjoin('app\Models\Project', 'users.project_id = project.project_id', 'project');
        $builder->leftjoin('app\Models\Sections', 'users.section_id = sections.section_id', 'sections');

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

        if (isset($input['keywords']) && !empty($input['keywords'])) {
            $builder-> andWhere('users.user_name LIKE :user_name: OR users.user_phone LIKE :user_phone: OR departments.department_name LIKE :department_name:', [
                'user_name' => '%' . $input['keywords'] . '%',
                'user_phone' => '%' . $input['keywords'] . '%',
                'department_name' => '%' . $input['keywords'] . '%',
            ]);
        }

        $builder->orderBy('users.project_id DESC, users.user_is_admin ASC, users.department_id DESC, users.user_id ASC');

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
     * 获取用户的项目部门等详细信息.
     * @param $userId
     * @return array|bool
     */
    public function getProjectDetailsById($userId)
    {
        $user_info = [];
        if (!empty($userId)) {
            $sql = 'SELECT n_z_users.user_id, n_z_users.user_name, n_z_users.user_job, n_z_users.user_image, n_z_users.user_phone, n_z_users.user_sex, n_z_users.user_intro, n_z_users.user_comments, n_z_users.user_age, n_z_users.user_status, n_z_project.project_id, n_z_project.project_name, n_z_project.work_start_time, n_z_project.work_end_time, n_z_departments.department_id, n_z_departments.department_name 
                    FROM n_z_users 
                    LEFT JOIN n_z_project ON n_z_project.project_id = n_z_users.project_id 
                    LEFT JOIN n_z_departments ON n_z_users.department_id = n_z_departments.department_id 
                    WHERE n_z_users.user_id = ? AND n_z_users.user_status = 1';

            $data = new Simple(null, $this, $this->getReadConnection()->query($sql, [$userId]));

            $user_info = $data->valid() ? $data->toArray()[0] : false;
        }

        return $user_info;
    }

    /**
     * 用于统计, 包括所有状态的用户列表.
     * @param array $input
     * @param bool $isPage
     * @param int $page
     * @param int $limit
     * @param bool $need_relation
     * @return array|bool
     */
    public function getUsersListUseCountByUser($input = [], $isPage = true, $page = 1, $limit = 10, $need_relation = true)
    {
        $input['time'] = isset($input['time']) ? $input['time'] : time();

        $where = '';
        $bindParams = [];

        $where .= (empty($where) ? ' WHERE' : ' AND') . ' A.project_id > 0 AND A.user_status = 1';

        if (isset($input['department_id']) && !empty($input['department_id'])) {
            $where .= (empty($where) ? ' WHERE' : ' AND') . ' A.department_id = ?';
            $bindParams[] = $input['department_id'];
        }
        if (isset($input['section_id']) && !empty($input['section_id'])) {
            $where .= (empty($where) ? ' WHERE' : ' AND') . ' A.section_id = ?';
            $bindParams[] = $input['section_id'];
        }

        if ($isPage) {
            $offset = ($page - 1) * $limit;
            $limits = ' ORDER BY A.project_id DESC, status.status_id ASC, A.department_id DESC, A.user_job ASC LIMIT ? OFFSET ?';
            $bindParams[] = $limit;
            $bindParams[] = $offset;
        } else {
            $limits = ' ORDER BY A.department_id DESC';
        }

        $sql = 'SELECT A.*, userStatus.user_status_id, userStatus.start_time, userStatus.end_time, userStatus.user_status_desc, status.status_id, status.status_name, status.status_color 
                FROM n_z_get_user_detail AS A 
                INNER JOIN n_z_user_belongs AS userBelongs ON userBelongs.user_id = A.user_id AND userBelongs.belong_id = '. $input['user_id'] .'
                LEFT JOIN n_z_user_status AS userStatus ON A.user_id = userStatus.user_id AND userStatus.start_time <= '. $input['time'] .' AND userStatus.end_time >= '. $input['time'] .'
                LEFT JOIN n_z_status AS status ON userStatus.status_id = status.status_id '
                . $where . $limits;

        $data = new Simple(null, $this, $this->getReadConnection()->query($sql, $bindParams));

        $users_list = $data->valid() ? $data->toArray() : false;

        return $users_list;
    }

    /**
     * 用于统计(默认状态时)用户列表.
     * @param $input
     * @param bool $need_relation
     * @param int $page
     * @param int $limit
     * @return array|bool
     */
    public function getUserListUseCountOnDefault($input, $need_relation = false, $page = 1, $limit = 10)
    {
        $where = '';
        $bindParams = [];

        $where .= (empty($where) ? ' WHERE' : ' AND') . ' users.user_status = 1 AND b.user_id IS NULL';

        if (isset($input['department_id']) && !empty($input['department_id'])) {
            $where .= (empty($where) ? ' WHERE' : ' AND') . ' users.department_id = ?';
            $bindParams[] = $input['department_id'];
        }
        if (isset($input['section_id']) && !empty($input['section_id'])) {
            $where .= (empty($where) ? ' WHERE' : ' AND') . ' sections.section_id = ?';
            $bindParams[] = $input['section_id'];
        }

        $sql = 'SELECT users.user_id, users.user_name, users.user_phone, departments.department_name, sections.section_name 
                FROM n_z_users AS users 
                LEFT JOIN n_z_departments AS departments ON departments.department_id = users.department_id 
                LEFT JOIN n_z_sections AS sections ON sections.section_id = users.section_id ';

        if ($need_relation) {
            $sql .= 'INNER JOIN n_z_user_belongs AS userBelongs ON userBelongs.user_id = users.user_id AND userBelongs.belong_id = '. $input['user_id'] .'
	                LEFT JOIN (SELECT user_id FROM n_z_user_status WHERE start_time <= '. $input['time'] .' AND end_time >= '. $input['time'] .' ) b ON b.user_id = users.user_id ';
        }

        $offset = ($page - 1) * $limit;
        $sql .= $where . ' ORDER BY users.project_id DESC, users.department_id DESC, users.user_job ASC LIMIT ? OFFSET ?';
        $bindParams[] = $limit;
        $bindParams[] = $offset;

        $data = new Simple(null, $this, $this->getReadConnection()->query($sql, $bindParams));

        $users_list = $data->valid() ? $data->toArray() : false;

        return $users_list;
    }

    /**
     * 用于统计(非默认状态时)用户列表.
     * @param $input
     * @param bool $need_relation
     * @param int $page
     * @param int $limit
     * @return array|bool
     */
    public function getUserListUseCount($input, $need_relation = false, $page = 1, $limit = 10)
    {
        $where = '';
        $bindParams = [];

        $where .= (empty($where) ? ' WHERE' : ' AND') . ' users.user_status = 1';

        if (isset($input['department_id']) && !empty($input['department_id'])) {
            $where .= (empty($where) ? ' WHERE' : ' AND') . ' users.department_id = ?';
            $bindParams[] = $input['department_id'];
        }
        if (isset($input['section_id']) && !empty($input['section_id'])) {
            $where .= (empty($where) ? ' WHERE' : ' AND') . ' sections.section_id = ?';
            $bindParams[] = $input['section_id'];
        }
        if (isset($input['status_id']) && !empty($input['status_id'])) {
            $where .= (empty($where) ? ' WHERE' : ' AND') . ' userStatus.status_id = ?';
            $bindParams[] = $input['status_id'];
        }

        $sql = 'SELECT userStatus.*, status.status_id, status.status_name, status.status_color, users.user_name, users.user_phone, departments.department_name, sections.section_name 
                FROM n_z_users AS users 
                INNER JOIN n_z_user_status AS userStatus ON userStatus.user_id = users.user_id 
                INNER JOIN n_z_status AS status ON status.status_id = userStatus.status_id 
                INNER JOIN n_z_departments AS departments ON departments.department_id = users.department_id 
                LEFT JOIN n_z_sections AS sections ON sections.section_id = users.section_id ';

        if ($need_relation) {
            $sql .= 'INNER JOIN n_z_user_belongs AS userBelongs ON userBelongs.user_id = users.user_id AND userBelongs.belong_id = '. $input['user_id'];

            if (isset($input['time']) && !empty($input['time'])) {
                $where .= (empty($where) ? ' WHERE' : ' AND') . ' (userStatus.start_time <= '. $input['time'] .' AND userStatus.end_time >= '. $input['time'] .')';
            }
        }

        $offset = ($page - 1) * $limit;
        $sql .= $where . ' ORDER BY users.project_id DESC, users.department_id DESC, users.user_job ASC LIMIT ? OFFSET ?';
        $bindParams[] = $limit;
        $bindParams[] = $offset;

        $data = new Simple(null, $this, $this->getReadConnection()->query($sql, $bindParams));

        $status_list = $data->valid() ? $data->toArray() : false;

        return $status_list;
    }

}