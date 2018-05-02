<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/5/2
 * Time: 14:19
 */

namespace app\Models;

/**
 * 权限表
 * Class AdminPermissions
 * @package app\Models
 */
class AdminPermissions extends ModelBase
{
    public function initialize()
    {
        $this->useDynamicUpdate(true);
    }

}