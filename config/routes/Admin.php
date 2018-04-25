<?php
/**
 * Created by PhpStorm.
 * User: 何杨涛
 * Date: 2018/4/10
 * Time: 14:44
 */

/**
 * 单位管理.
 */

// 单位列表.
$router->addGet('/admin/project', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'project',
    'action'     => 'index',
]);

// 添加单位页面.
$router->addGet('/admin/project/create', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'project',
    'action'     => 'create',
]);

// 保存单位.
$router->addPost('/admin/project', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'project',
    'action'     => 'save',
]);

// 编辑单位信息.
$router->addGet('/admin/project/{id:[0-9]+}/edit', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'project',
    'action'     => 'edit',
]);
$router->addPost('/admin/project/update', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'project',
    'action'     => 'update',
]);

// 关闭单位.
$router->addPost('/admin/project/delete', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'project',
    'action'     => 'delete',
]);

// 显示单位详情.
$router->addPost('/admin/project/show', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'project',
    'action'     => 'show',
]);

// 创建管理员.
$router->addGet('/admin/project/createadmin', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'project',
    'action'     => 'createAdmin',
]);

// 保存管理员.
$router->addPost('/admin/project/saveadmin', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'project',
    'action'     => 'saveAdmin',
]);

// 管理员列表.
$router->addGet('/admin/project/adminuserlist', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'project',
    'action'     => 'adminUserList',
]);

/**
 * 用户管理.
 */

// 删除用户.
$router->addPost('/admin/users/delete', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'user',
    'action'     => 'delete',
]);

// 重置密码.
$router->addPost('/admin/users/resetpwd', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'user',
    'action'     => 'resetPwd',
]);

/**
 * 科室管理.
 */

// 科室列表.
$router->addGet('/admin/department', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'department',
    'action'     => 'index',
]);

// 添加科室.
$router->addGet('/admin/department/create', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'department',
    'action'     => 'create',
]);

// 保存科室.
$router->addPost('/admin/department', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'department',
    'action'     => 'save',
]);

// 显示科室详情.
$router->addPost('/admin/department/show', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'department',
    'action'     => 'show',
]);

// 编辑科室详情.
$router->addGet('/admin/department/{id:[0-9]+}/edit', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'department',
    'action'     => 'edit',
]);
$router->addPost('/admin/department/update', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'department',
    'action'     => 'update',
]);

// 删除科室.
$router->addPost('/admin/department/delete', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'department',
    'action'     => 'delete',
]);

// 获取单位所有人员.
$router->addPost('/admin/department/ajaxGetUsersByDepartmentOrOthers', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'department',
    'action'     => 'ajaxGetUsersByDepartmentOrOthers',
]);

// 更新人员所在科室.
$router->addPost('/admin/department/updateUsersDepartment', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'department',
    'action'     => 'updateUsersDepartment',
]);


/**
 * 二维码管理.
 */

// 二维码列表.
$router->addGet('/admin/qrcode', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'QrCode',
    'action'     => 'index',
]);

// 编辑二维码信息.
$router->addGet('/admin/qrcode/{id:[0-9]+}/edit', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'QrCode',
    'action'     => 'edit',
]);
$router->addPost('/admin/qrcode/update', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'QrCode',
    'action'     => 'update',
]);

// 验证二维码ID.
$router->addPost('/admin/qrcode/valid', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'QrCode',
    'action'     => 'valid',
]);

// 删除绑定.
$router->addPost('/admin/qrcode/delete', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'QrCode',
    'action'     => 'delete',
]);

// 获取二维码.
$router->addPost('/admin/qrcode/ajaxGetForwardQrCode', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'QrCode',
    'action'     => 'ajaxGetForwardQrCode',
]);

/**
 * 留言管理.
 */

// 留言列表.
$router->addGet('/admin/comment', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'comment',
    'action'     => 'index',
]);

// 批量修改留言状态.
$router->addPost('/admin/comment/changeall', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'comment',
    'action'     => 'changeAll',
]);

// 批量删除留言.
$router->addPost('/admin/comment/deleteall', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'comment',
    'action'     => 'deleteAll',
]);

// 修改一条留言状态.
$router->addPost('/admin/comment/change', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'comment',
    'action'     => 'change',
]);

// 删除一条留言.
$router->addPost('/admin/comment/delete', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'comment',
    'action'     => 'delete',
]);

/**
 * 告示管理.
 */

// 告示列表.
$router->addGet('/admin/notice', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'notice',
    'action'     => 'index',
]);

// 添加告示.
$router->addGet('/admin/notice/create', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'notice',
    'action'     => 'create',
]);

// 保存告示.
$router->addPost('/admin/notice', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'notice',
    'action'     => 'save',
]);

// 编辑告示详情.
$router->addGet('/admin/notice/{id:[0-9]+}/edit', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'notice',
    'action'     => 'edit',
]);
$router->addPost('/admin/notice/update', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'notice',
    'action'     => 'update',
]);

// 编辑公告部门.
$router->addPost('/admin/notice/ajaxGetDepartments', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'notice',
    'action'     => 'ajaxGetDepartments',
]);

// 保存公告部门列表.
$router->addPost('/admin/notice/updateNoticeDepartment', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'notice',
    'action'     => 'updateNoticeDepartment',
]);

// 删除公告.
$router->addPost('/admin/notice/delete', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'notice',
    'action'     => 'delete',
]);

// 显示详情.
$router->addPost('/admin/notice/show', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'notice',
    'action'     => 'show',
]);

// 告示状态变更.
$router->addPost('/admin/notice/changestatus', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'notice',
    'action'     => 'changeStatus',
]);

/**
 * 事件管理.
 */

// 事件列表.
$router->addGet('/admin/status', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'status',
    'action'     => 'index',
]);

// 添加事件.
$router->addGet('/admin/status/create', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'status',
    'action'     => 'create',
]);

// 保存事件.
$router->addPost('/admin/status', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'status',
    'action'     => 'save',
]);

// 修改事件信息.
$router->addGet('/admin/status/{id:[0-9]+}/edit', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'status',
    'action'     => 'edit',
]);
$router->addPost('/admin/status/update', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'status',
    'action'     => 'update',
]);

// 删除事件.
$router->addPost('/admin/status/delete', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'status',
    'action'     => 'delete',
]);

// 修改排序.
$router->addPost('/admin/status/changeOrder', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'status',
    'action'     => 'changeOrder',
]);

// 设置默认事件.
$router->addPost('/admin/status/setDefault', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'status',
    'action'     => 'setDefault',
]);

// 验证事件名称.
$router->addPost('/admin/status/validName', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'status',
    'action'     => 'validName',
]);

// 设置工作时间表.
$router->addGet('/admin/status/settingworktimelist', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'status',
    'action'     => 'settingWorkTimeList',
]);

// 人员(员工)状态列表.
$router->addGet('/admin/status/workerStatusList', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'status',
    'action'     => 'workerStatusList',
]);

// 得到用户可选的事件选项.
$router->addPost('/admin/status/ajaxGetStatusOptionByUser', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'status',
    'action'     => 'ajaxGetStatusOptionByUser',
]);

// 保存用户事件.
$router->addPost('/admin/status/saveUserStatus', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'status',
    'action'     => 'saveUserStatus',
]);

/**
 * 部门管理.
 */

// 部门列表.
$router->addGet('/admin/section', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'section',
    'action'     => 'index',
]);

// 添加部门页面.
$router->addGet('/admin/section/create', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'section',
    'action'     => 'create',
]);

// 保存部门.
$router->addPost('/admin/section', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'section',
    'action'     => 'save',
]);

// 修改部门信息.
$router->addGet('/admin/section/{id:[0-9]+}/edit', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'section',
    'action'     => 'edit',
]);
$router->addPost('/admin/section/update', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'section',
    'action'     => 'update',
]);

// 获取部门人员.
$router->addPost('/admin/section/ajaxGetUsersBySectionOrOthers', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'section',
    'action'     => 'ajaxGetUsersBySectionOrOthers',
]);

// 修改部门人员.
$router->addPost('/admin/section/updateUsersSection', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'section',
    'action'     => 'updateUsersSection',
]);

// 删除部门.
$router->addPost('/admin/section/delete', [
    'namespace'  => 'app\Controllers\Admin',
    'controller' => 'section',
    'action'     => 'delete',
]);