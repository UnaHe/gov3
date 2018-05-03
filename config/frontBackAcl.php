<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/5/3
 * Time: 10:01
 */

use Phalcon\Acl\Role;
use Phalcon\Config;

return new Config([
    'administrator' => [
        'rote' => new Role("administrator"),
        'resource' => [
            'Comment' => ['index', 'changeAll', 'deleteAll', 'change', 'delete'],
            'Common' => ['upload', 'ajaxGetOptionsByProject'],
            'Department' => ['index', 'create', 'save', 'show', 'edit', 'update', 'delete', 'ajaxGetUsersByDepartmentOrOthers', 'updateUsersDepartment'],
            'Errors' => ['show404', 'show401'],
            'Home' => ['index', 'changePwd', 'updatePwd', 'authPwd'],
            'Login' => ['index', 'login', 'logout'],
            'Notice' => ['index', 'ajaxGetDepartments', 'updateNoticeDepartment', 'delete', 'show', 'changeStatus', 'create', 'save', 'edit', 'update'],
            'Permissions' => ['index', 'create', 'save', 'edit', 'update', 'delete'],
            'Project' => ['index', 'create', 'save', 'edit', 'update', 'delete', 'show', 'createAdmin', 'saveAdmin', 'adminUserList'],
            'QrCode' => ['index', 'edit', 'valid', 'update', 'delete', 'ajaxGetForwardQrCode'],
            'Roles' => ['index', 'create', 'save', 'edit', 'update', 'delete', 'permission', 'savePermission'],
            'Section' => ['index', 'create', 'save', 'edit', 'update', 'ajaxGetUsersBySectionOrOthers', 'updateUsersSection', 'delete'],
            'Status' => ['index', 'create', 'save', 'edit', 'update', 'validName', 'delete', 'changeOrder', 'setDefault', 'settingWorkTimeList', 'settingWorkTime', 'userStatus', 'changeStatus', 'workerStatusList', 'ajaxGetStatusOptionByUser', 'saveUserStatus'],
            'User' => ['index', 'create', 'save', 'edit', 'update', 'delete', 'resetPwd', 'addBelong', 'belongs', 'belongsDelete', 'role', 'roleSave', 'workerStatusList', 'ajaxGetStatusOptionByUser', 'saveUserStatus'],
        ],
    ],
    'system_administrator' => [
        'rote' => new Role("system_administrator"),
        'resource' => [
            'Comment' => ['index', 'changeAll', 'deleteAll', 'change', 'delete'],
            'Common' => ['upload', 'ajaxGetOptionsByProject'],
            'Department' => ['index', 'create', 'save', 'show', 'edit', 'update', 'delete', 'ajaxGetUsersByDepartmentOrOthers', 'updateUsersDepartment'],
            'Errors' => ['show404', 'show401'],
            'Home' => ['index', 'changePwd', 'updatePwd', 'authPwd'],
            'Login' => ['index', 'login', 'logout'],
            'Notice' => ['index', 'ajaxGetDepartments', 'updateNoticeDepartment', 'delete', 'show', 'changeStatus', 'create', 'save', 'edit', 'update'],
            'Permissions' => ['index', 'create', 'save', 'edit', 'update', 'delete'],
            'Project' => ['index', 'create', 'save', 'edit', 'update', 'delete', 'show', 'createAdmin', 'saveAdmin', 'adminUserList'],
            'QrCode' => ['index', 'edit', 'valid', 'update', 'delete', 'ajaxGetForwardQrCode'],
            'Roles' => ['index', 'create', 'save', 'edit', 'update', 'delete', 'permission', 'savePermission'],
            'Section' => ['index', 'create', 'save', 'edit', 'update', 'ajaxGetUsersBySectionOrOthers', 'updateUsersSection', 'delete'],
            'Status' => ['index', 'create', 'save', 'edit', 'update', 'validName', 'delete', 'changeOrder', 'setDefault', 'settingWorkTimeList', 'settingWorkTime', 'userStatus', 'changeStatus', 'workerStatusList', 'ajaxGetStatusOptionByUser', 'saveUserStatus'],
            'User' => ['index', 'create', 'save', 'edit', 'update', 'delete', 'resetPwd', 'addBelong', 'belongs', 'belongsDelete', 'role', 'roleSave', 'workerStatusList', 'ajaxGetStatusOptionByUser', 'saveUserStatus'],
        ],
    ],
    'project_administrator' => [
        'rote' => new Role("project_administrator"),
        'resource' => [
            'Comment' => ['index', 'changeAll', 'deleteAll', 'change', 'delete'],
            'Common' => ['upload', 'ajaxGetOptionsByProject'],
            'Department' => ['index', 'create', 'save', 'show', 'edit', 'update', 'delete', 'ajaxGetUsersByDepartmentOrOthers', 'updateUsersDepartment'],
            'Errors' => ['show404', 'show401'],
            'Home' => ['index', 'changePwd', 'updatePwd', 'authPwd'],
            'Login' => ['index', 'login', 'logout'],
            'Notice' => ['index', 'ajaxGetDepartments', 'updateNoticeDepartment', 'delete', 'show', 'changeStatus', 'create', 'save', 'edit', 'update'],
//            'Permissions' => ['index', 'create', 'save', 'edit', 'update', 'delete'],
            'Project' => ['edit', 'update'], // 单位管理员权限.
            'QrCode' => ['index', 'ajaxGetForwardQrCode'], // 单位管理员权限.
//            'Roles' => ['index', 'create', 'save', 'edit', 'update', 'delete', 'permission', 'savePermission'],
            'Section' => ['index', 'create', 'save', 'edit', 'update', 'ajaxGetUsersBySectionOrOthers', 'updateUsersSection', 'delete'],
            'Status' => ['index', 'create', 'save', 'edit', 'update', 'validName', 'delete', 'changeOrder', 'setDefault', 'settingWorkTimeList', 'settingWorkTime', 'userStatus', 'changeStatus', 'workerStatusList', 'ajaxGetStatusOptionByUser', 'saveUserStatus'],
            'User' => ['index', 'create', 'save', 'edit', 'update', 'delete', 'resetPwd', 'addBelong', 'belongs', 'belongsDelete', 'role', 'roleSave', 'workerStatusList', 'ajaxGetStatusOptionByUser', 'saveUserStatus'],
        ],
    ],
    'common_admin' => [
        'rote' => new Role("common_admin"),
        'resource' => [
            'Comment' => ['index', 'changeAll', 'deleteAll', 'change', 'delete'],
            'Common' => ['upload', 'ajaxGetOptionsByProject'],
            'Department' => ['index', 'create', 'save', 'show', 'edit', 'update', 'delete', 'ajaxGetUsersByDepartmentOrOthers', 'updateUsersDepartment'],
            'Errors' => ['show404', 'show401'],
            'Home' => ['index', 'changePwd', 'updatePwd', 'authPwd'],
            'Login' => ['index', 'login', 'logout'],
            'Notice' => ['index', 'ajaxGetDepartments', 'updateNoticeDepartment', 'delete', 'show', 'changeStatus', 'create', 'save', 'edit', 'update'],
//            'Permissions' => ['index', 'create', 'save', 'edit', 'update', 'delete'],
            'Project' => ['index', 'create', 'save', 'edit', 'update', 'delete', 'show', 'createAdmin', 'saveAdmin', 'adminUserList'],
            'QrCode' => ['index', 'edit', 'valid', 'update', 'delete', 'ajaxGetForwardQrCode'],
//            'Roles' => ['index', 'create', 'save', 'edit', 'update', 'delete', 'permission', 'savePermission'],
            'Section' => ['index', 'create', 'save', 'edit', 'update', 'ajaxGetUsersBySectionOrOthers', 'updateUsersSection', 'delete'],
            'Status' => ['index', 'create', 'save', 'edit', 'update', 'validName', 'delete', 'changeOrder', 'setDefault', 'settingWorkTimeList', 'settingWorkTime', 'userStatus', 'changeStatus', 'workerStatusList', 'ajaxGetStatusOptionByUser', 'saveUserStatus'],
            'User' => ['index', 'create', 'save', 'edit', 'update', 'delete', 'resetPwd', 'addBelong', 'belongs', 'belongsDelete', 'role', 'roleSave', 'workerStatusList', 'ajaxGetStatusOptionByUser', 'saveUserStatus'],
        ],
    ],
    'User' => [
        'rote' => new Role("User"),
        'resource' => [
            'Login' => ['index', 'login', 'logout'],
            'Errors' => ['show404', 'show401'],
        ],
    ],

]);