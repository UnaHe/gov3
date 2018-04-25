<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/12
 * Time: 15:35
 */

namespace app\Models;

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
}