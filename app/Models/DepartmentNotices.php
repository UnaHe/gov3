<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/12
 * Time: 15:35
 */

namespace app\Models;

/**
 * 部门告示关联表
 * Class DepartmentNotices
 * @package app\Models
 */
class DepartmentNotices extends ModelBase
{
    public function initialize()
    {
        $this->useDynamicUpdate(true);
    }

}