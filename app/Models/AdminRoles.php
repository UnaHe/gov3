<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/12
 * Time: 15:35
 */

namespace app\Models;

/**
 * 角色表
 * Class AdminRoles
 * @package app\Models
 */
class AdminRoles extends ModelBase
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

}