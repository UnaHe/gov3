<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/11
 * Time: 10:24
 */

namespace app\Controllers\Admin;

use app\Models\Departments;
use app\Models\Sections;
use app\Models\Status;

/**
 * 通用控制器
 * Class CommonController
 * @package app\Controller
 */
class CommonController extends ControllerBase
{
    public static $upload_url =  '';
    public static $upload_path =  '';

    public function initialize()
    {
        //设置用户照片的URL
        self::$upload_url = self::$upload_url ? self::$upload_url : ($this->config->img)['upload_url'];
        self::$upload_path = self::$upload_path ? self::$upload_path : ($this->config->img)['upload_path'];
    }

    /**
     * 图片上传
     */
    public function uploadAction()
    {
        // 验证是否上传.
        if ($this->request->hasFiles()) {
            // 获取文件相关信息
            $files = $this->request->getUploadedFiles();

            // 上传文件夹.
            $folder = $this->request->getPost('type');

            // 文件上传路径.
            $path = ($this->config->img)['upload'] . $folder . '/';
            if (!file_exists($path)) {
                @mkdir($path,0777,true);
            }

            $newName = '';
            foreach ($files as $file) {
                //上传文件的后缀.
                $extension = $file->getExtension();

                // 重命名后的文件名.
                $newName = date('YmdHis').mt_rand(100,999).'.'.$extension;

                // 移动文件.
                $file->moveTo($path . $newName);
            }

            // 返回路径.
            return $folder . '/' .$newName;
        }

        return false;
    }

    /**
     * 得到单位列表通过项目id.
     */
    public function ajaxGetOptionsByProjectAction()
    {
        $projectId = $this->request->getPost('project_id');
        $type = $this->request->getPost('type') ? $this->request->getPost('type') : 0;

        $list['department_list'] = (new Departments())->getTree(0, 0, $projectId);

        if($type >= 1){

            $list['section_list'] = (new Sections())->getTree(0, 0, $projectId);
        }

        if($type >= 2){
            $list['status_list'] = Status::getListByProjectId($projectId);
        }

        if ($list) {
            return $this->ajaxSuccess($list);
        } else {
            return $this->ajaxError('暂无法获取，请稍后重试');
        }
    }

}