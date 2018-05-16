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

    public function beforeCreate()
    {
        // 设置创建时间.
        $this->created_at = time();
    }

    // 得到所有有效的项目列表.
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

    // 得到项目详情通过项目.
    public function getDetailsByProject($projects){
        $project_list = [];
        if(!empty($projects) && is_array($projects)){
            $builder = Project::getModelsManager()->createBuilder()->addFrom('app\Models\Project', 'project');
            if(array_key_exists('department_id',$projects)!==false){
                $builder->columns('project.*, departments.department_id, departments.department_name');
                $builder->leftJoin('app\Models\departments','departments.project_id = project.project_id', 'departments');
                $builder->where('project.project_id = :project_id:', [
                    'project_id' => $projects['project_id'],
                ]);
                if(!empty($projects['department_id'])){
                    $builder->andWhere('departments.department_id = :department_id:', [
                        'department_id' => $projects['department_id'],
                    ]);
                }
            }else{
                $builder->inWhere('project.project_id',$projects);
            }
            $list = $builder->getQuery()->execute();
            foreach ($list as $v){
                $project_list[$v->project->project_id] = $v;
            }
        }

        return $project_list;
    }

}