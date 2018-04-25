<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/4
 * Time: 17:34
 */

namespace app\Models;

/**
 * 单位表
 * Class Project
 * @package app\Models
 */
class Project extends ModelBase
{
    public $created_at;

    public function initialize()
    {
        $this->useDynamicUpdate(true);
    }

    public function beforeSave()
    {
        // 设置创建时间.
        $this->created_at = time();
    }

    //得到所有有效的项目列表
    public static function getProjectList(){
        $data = Project::find([
            'project_status = :project_status:',
            'bind' => [
                'project_status' => 1
            ],
            'order' => 'project_id'
        ]);
        return $data;
    }

}