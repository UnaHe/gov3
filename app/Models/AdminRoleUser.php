<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/12
 * Time: 15:35
 */

namespace app\Models;

/**
 * 用户角色关联表
 * Class AdminRoleUser
 * @package app\Models
 */
class AdminRoleUser extends ModelBase
{
    public function initialize()
    {
        $this->useDynamicUpdate(true);
    }
}