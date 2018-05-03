<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/5/3
 * Time: 10:40
 */

namespace app\Library;

use Phalcon\Acl;
use Phalcon\Acl\Adapter\Memory as AclList;
use Phalcon\Acl\Resource;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\User\Plugin;

class SecurityDeep extends Plugin {

    public function __construct() { }

    /**
     * 获取ACL列表.
     * @return AclList
     */
    public function _getAcl()
    {
        $acl = new AclList();

        // 默认权限(拒绝).
        $acl->setDefaultAction(Acl::DENY);

        // 创建.
        $allResource = $this->_callAcl();

        foreach($allResource as $key=>$value)
        {
            // 创建角色,并将角色添加到ACL.
            $acl->addRole($value['rote']);

            foreach($value['resource'] as $k=>$v)
            {
                foreach($v as $ky=>$vy)
                {
                    // 添加资源.
                    $acl->addResource(new Resource(strtolower($k)), $vy);

                    // 添加访问权限.
                    $acl->allow($key, strtolower($k), $vy);
                }
            }
        }

        return $acl;
    }

    /**
     * 获取DI服务.
     * @return mixed
     */
    public function _callAcl()
    {
        if($this->persistent->acl == null) {
            $this->persistent->acl = $this->aclResource;
        }

        return $this->persistent->acl;
    }

    /**
     * 验证权限.
     * @param Event $event
     * @param Dispatcher $dispatcher
     */
    public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher)
    {
        // 控制器,方法名.
        $controller = $dispatcher->getControllerName();
        $action = $dispatcher->getActionName();

        $role = '';

        if( $this->session->has('user')) {
            $managerInfo = $this->session->get('user');
            $role = $managerInfo['role_code'];
        }

        if(empty($role)) $role = 'User';
        $acl = $this->_getAcl();

        // 是否有访问权限.
        $isAllowed = $acl->isAllowed($role, $controller, $action);

        if(!$isAllowed) {
            $dispatcher->forward(
                [
                    'namespace'  => 'app\Controllers\Admin',
                    'controller' => 'errors',
                    'action'     => 'show401',
                ]
            );
        }
    }

}