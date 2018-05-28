<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/8
 * Time: 14:01
 */

namespace app\Models;

use Phalcon\Mvc\Model;

class ModelBase extends Model
{
    /**
     * 设置KEY.
     * @param $prefix
     * @param string $params
     * @return string
     */
    public static function makeKey($prefix, $params = '') {
        return md5($prefix . json_encode($params));
    }

    /**
     * 覆盖find方法.
     * @param null $parameters
     * @return Model|Model\ResultsetInterface
     */
//    public static function find($parameters = null)
//    {
//        if (!CACHING) {
//            return parent::find($parameters);
//        }
//
//        if (!isset($parameters)) {
//            $parameters = [get_class() => 'all'];
//        }
//
//        if (!is_array($parameters)) {
//            $parameters = [$parameters];
//        }
//
//        if (!isset($parameters['cache'])) {
//            $parameters['cache'] = [
//                "key" => self::makeKey($_SERVER['REQUEST_URI'], $parameters),
//                "lifetime" => 120
//            ];
//        }
//
//        return parent::find($parameters);
//    }

    /**
     * 覆盖findFirst方法.
     * @param null $parameters
     * @return Model
     */
//    public static function findFirst($parameters = null)
//    {
//        if (!CACHING) {
//            return parent::findFirst($parameters);
//        }
//
//        if (!isset($parameters)) {
//            $parameters = [get_class() => 'first'];
//        }
//
//        if (!is_array($parameters)) {
//            $parameters = [$parameters];
//        }
//
//        if (!isset($parameters['cache'])) {
//            $parameters['cache'] = [
//                "key" => self::makeKey($_SERVER['REQUEST_URI'], $parameters),
//                "lifetime" => 120
//            ];
//        }
//
//        return parent::findFirst($parameters);
//    }

}