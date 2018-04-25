<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/12
 * Time: 15:35
 */

namespace app\Models;

/**
 * 用户状态表.
 * Class UserStatus
 * @package app\Models
 */
class UserStatus extends ModelBase
{
    public function initialize()
    {
        $this->useDynamicUpdate(true);
    }

    /**
     * 获取冲突的数据
     * @param $user_id
     * @param $start_time
     * @param $end_time
     * @param bool $user_status_id
     * @return bool 没有冲突返回true
     */
    public static function getConflict($user_id, $start_time, $end_time, $user_status_id = false)
    {
        $builder = UserStatus::query();
        $builder->where('user_id = :user_id:', [
            'user_id' => $user_id
        ]);

        $builder->andWhere('(start_time <= :start_time: AND end_time >= :end_time:) OR (start_time > :start_time: AND start_time < :end_time:) OR (end_time > :start_time: AND end_time < :end_time:)', [
            'start_time' => $start_time,
            'end_time' => $end_time
        ]);

        if ($user_status_id) {
            $builder->andWhere('user_status_id != :user_status_id:', [
                'user_status_id' => $user_status_id
            ]);
        }
        $data = $builder->execute()->toArray();

        return count($data) ? false : true;
    }

}