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
$admin->addGet('/project', [
    'controller' => 'project',
    'action'     => 'index',
]);

// 添加单位页面.
$admin->addGet('/project/create', [
    'controller' => 'project',
    'action'     => 'create',
]);

// 保存单位.
$admin->addPost('/project', [
    'controller' => 'project',
    'action'     => 'save',
]);

// 编辑单位信息.
$admin->addGet('/project/{id:[0-9]+}/edit', [
    'controller' => 'project',
    'action'     => 'edit',
]);
$admin->addPost('/project/update', [
    'controller' => 'project',
    'action'     => 'update',
]);

// 关闭单位.
$admin->addPost('/project/delete', [
    'controller' => 'project',
    'action'     => 'delete',
]);

// 显示单位详情.
$admin->addPost('/project/show', [
    'controller' => 'project',
    'action'     => 'show',
]);

// 创建管理员.
$admin->addGet('/project/createadmin', [
    'controller' => 'project',
    'action'     => 'createAdmin',
]);

// 保存管理员.
$admin->addPost('/project/saveadmin', [
    'controller' => 'project',
    'action'     => 'saveAdmin',
]);

// 管理员列表.
$admin->addGet('/project/adminuserlist', [
    'controller' => 'project',
    'action'     => 'adminUserList',
]);

/**
 * 用户管理.
 */

// 人员列表.
$admin->addGet('/users', [
    'controller' => 'user',
    'action'     => 'index',
]);

// 新增用户.
$admin->addGet('/users/create', [
    'controller' => 'user',
    'action'     => 'create',
]);

// 保存用户.
$admin->addPost('/users', [
    'controller' => 'user',
    'action'     => 'save',
]);

// 修改用户信息.
$admin->addGet('/users/{id:[0-9]+}/edit', [
    'controller' => 'user',
    'action'     => 'edit',
]);
$admin->addPost('/users/update', [
    'controller' => 'user',
    'action'     => 'update',
]);

// 删除用户.
$admin->addPost('/users/delete', [
    'controller' => 'user',
    'action'     => 'delete',
]);

// 重置密码.
$admin->addPost('/users/resetpwd', [
    'controller' => 'user',
    'action'     => 'resetPwd',
]);

// 新增归属.
$admin->add('/users/addbelong', [
    'controller' => 'user',
    'action'     => 'addBelong',
]);

// 角色管理.
$admin->addGet('/users/{id:[0-9]+}/role', [
    'controller' => 'user',
    'action'     => 'role',
]);
$admin->addPost('/users/role', [
    'controller' => 'user',
    'action'     => 'roleSave',
]);

// 人员归属关系列表.
$admin->addGet('/users/belongs', [
    'controller' => 'user',
    'action'     => 'belongs',
]);

// 删除归属.
$admin->addPost('/users/belongs/delete', [
    'controller' => 'user',
    'action'     => 'belongsDelete',
]);

/**
 * 科室管理.
 */

// 科室列表.
$admin->addGet('/department', [
    'controller' => 'department',
    'action'     => 'index',
]);

// 添加科室.
$admin->addGet('/department/create', [
    'controller' => 'department',
    'action'     => 'create',
]);

// 保存科室.
$admin->addPost('/department', [
    'controller' => 'department',
    'action'     => 'save',
]);

// 显示科室详情.
$admin->addPost('/department/show', [
    'controller' => 'department',
    'action'     => 'show',
]);

// 编辑科室详情.
$admin->addGet('/department/{id:[0-9]+}/edit', [
    'controller' => 'department',
    'action'     => 'edit',
]);
$admin->addPost('/department/update', [
    'controller' => 'department',
    'action'     => 'update',
]);

// 删除科室.
$admin->addPost('/department/delete', [
    'controller' => 'department',
    'action'     => 'delete',
]);

// 获取单位所有人员.
$admin->addPost('/department/ajaxGetUsersByDepartmentOrOthers', [
    'controller' => 'department',
    'action'     => 'ajaxGetUsersByDepartmentOrOthers',
]);

// 更新人员所在科室.
$admin->addPost('/department/updateUsersDepartment', [
    'controller' => 'department',
    'action'     => 'updateUsersDepartment',
]);


/**
 * 二维码管理.
 */

// 二维码列表.
$admin->addGet('/qrcode', [
    'controller' => 'qrcode',
    'action'     => 'index',
]);

// 编辑二维码信息.
$admin->addGet('/qrcode/{id:[0-9]+}/edit', [
    'controller' => 'qrcode',
    'action'     => 'edit',
]);
$admin->addPost('/qrcode/update', [
    'controller' => 'qrcode',
    'action'     => 'update',
]);

// 验证二维码ID.
$admin->addPost('/qrcode/valid', [
    'controller' => 'qrcode',
    'action'     => 'valid',
]);

// 删除绑定.
$admin->addPost('/qrcode/delete', [
    'controller' => 'qrcode',
    'action'     => 'delete',
]);

// 获取二维码.
$admin->addPost('/qrcode/ajaxGetForwardQrCode', [
    'controller' => 'qrcode',
    'action'     => 'ajaxGetForwardQrCode',
]);

/**
 * 留言管理.
 */

// 留言列表.
$admin->addGet('/comment', [
    'controller' => 'comment',
    'action'     => 'index',
]);

// 批量修改留言状态.
$admin->addPost('/comment/changeall', [
    'controller' => 'comment',
    'action'     => 'changeAll',
]);

// 批量删除留言.
$admin->addPost('/comment/deleteall', [
    'controller' => 'comment',
    'action'     => 'deleteAll',
]);

// 修改一条留言状态.
$admin->addPost('/comment/change', [
    'controller' => 'comment',
    'action'     => 'change',
]);

// 删除一条留言.
$admin->addPost('/comment/delete', [
    'controller' => 'comment',
    'action'     => 'delete',
]);

/**
 * 告示管理.
 */

// 告示列表.
$admin->addGet('/notice', [
    'controller' => 'notice',
    'action'     => 'index',
]);

// 添加告示.
$admin->addGet('/notice/create', [
    'controller' => 'notice',
    'action'     => 'create',
]);

// 保存告示.
$admin->addPost('/notice', [
    'controller' => 'notice',
    'action'     => 'save',
]);

// 编辑告示详情.
$admin->addGet('/notice/{id:[0-9]+}/edit', [
    'controller' => 'notice',
    'action'     => 'edit',
]);
$admin->addPost('/notice/update', [
    'controller' => 'notice',
    'action'     => 'update',
]);

// 编辑公告部门.
$admin->addPost('/notice/ajaxGetDepartments', [
    'controller' => 'notice',
    'action'     => 'ajaxGetDepartments',
]);

// 保存公告部门列表.
$admin->addPost('/notice/updateNoticeDepartment', [
    'controller' => 'notice',
    'action'     => 'updateNoticeDepartment',
]);

// 删除公告.
$admin->addPost('/notice/delete', [
    'controller' => 'notice',
    'action'     => 'delete',
]);

// 显示详情.
$admin->addPost('/notice/show', [
    'controller' => 'notice',
    'action'     => 'show',
]);

// 告示状态变更.
$admin->addPost('/notice/changestatus', [
    'controller' => 'notice',
    'action'     => 'changeStatus',
]);

/**
 * 事件管理.
 */

// 事件列表.
$admin->addGet('/status', [
    'controller' => 'status',
    'action'     => 'index',
]);

// 添加事件.
$admin->addGet('/status/create', [
    'controller' => 'status',
    'action'     => 'create',
]);

// 保存事件.
$admin->addPost('/status', [
    'controller' => 'status',
    'action'     => 'save',
]);

// 修改事件信息.
$admin->addGet('/status/{id:[0-9]+}/edit', [
    'controller' => 'status',
    'action'     => 'edit',
]);
$admin->addPost('/status/update', [
    'controller' => 'status',
    'action'     => 'update',
]);

// 删除事件.
$admin->addPost('/status/delete', [
    'controller' => 'status',
    'action'     => 'delete',
]);

// 修改排序.
$admin->addPost('/status/changeOrder', [
    'controller' => 'status',
    'action'     => 'changeOrder',
]);

// 设置默认事件.
$admin->addPost('/status/setDefault', [
    'controller' => 'status',
    'action'     => 'setDefault',
]);

// 验证事件名称.
$admin->addPost('/status/validName', [
    'controller' => 'status',
    'action'     => 'validName',
]);

// 设置工作时间表.
$admin->addGet('/status/settingWorkTimeList', [
    'controller' => 'status',
    'action'     => 'settingWorkTimeList',
]);

// 设置工作时间.
$admin->addPost('/status/settingWorkTime', [
    'controller' => 'status',
    'action'     => 'settingWorkTime',
]);

// 已设事件.
$admin->addGet('/status/userStatus', [
    'controller' => 'status',
    'action'     => 'userStatus',
]);

// 修改状态.
$admin->addPost('/status/changeStatus', [
    'controller' => 'status',
    'action'     => 'changeStatus',
]);

// 人员(员工)状态列表.
$admin->addGet('/status/workerStatusList', [
    'controller' => 'status',
    'action'     => 'workerStatusList',
]);

// 得到用户可选的事件选项.
$admin->addPost('/status/ajaxGetStatusOptionByUser', [
    'controller' => 'status',
    'action'     => 'ajaxGetStatusOptionByUser',
]);

// 保存用户事件.
$admin->addPost('/status/saveUserStatus', [
    'controller' => 'status',
    'action'     => 'saveUserStatus',
]);

/**
 * 部门管理.
 */

// 部门列表.
$admin->addGet('/section', [
    'controller' => 'section',
    'action'     => 'index',
]);

// 添加部门页面.
$admin->addGet('/section/create', [
    'controller' => 'section',
    'action'     => 'create',
]);

// 保存部门.
$admin->addPost('/section', [
    'controller' => 'section',
    'action'     => 'save',
]);

// 修改部门信息.
$admin->addGet('/section/{id:[0-9]+}/edit', [
    'controller' => 'section',
    'action'     => 'edit',
]);
$admin->addPost('/section/update', [
    'controller' => 'section',
    'action'     => 'update',
]);

// 获取部门人员.
$admin->addPost('/section/ajaxGetUsersBySectionOrOthers', [
    'controller' => 'section',
    'action'     => 'ajaxGetUsersBySectionOrOthers',
]);

// 修改部门人员.
$admin->addPost('/section/updateUsersSection', [
    'controller' => 'section',
    'action'     => 'updateUsersSection',
]);

// 删除部门.
$admin->addPost('/section/delete', [
    'controller' => 'section',
    'action'     => 'delete',
]);

/**
 * 权限管理.
 */

// 权限列表.
$admin->addGet('/permissions', [
    'controller' => 'permissions',
    'action'     => 'index',
]);

// 添加权限.
$admin->addGet('/permissions/create', [
    'controller' => 'permissions',
    'action'     => 'create',
]);

// 保存.
$admin->addPost('/permissions/save', [
    'controller' => 'permissions',
    'action'     => 'save',
]);

// 编辑权限信息.
$admin->addGet('/permissions/{id:[0-9]+}/edit', [
    'controller' => 'permissions',
    'action'     => 'edit',
]);
$admin->addPost('/permissions/update', [
    'controller' => 'permissions',
    'action'     => 'update',
]);

// 删除权限.
$admin->addPost('/permissions/delete', [
    'controller' => 'permissions',
    'action'     => 'delete',
]);

/*
 * 角色管理.
 */

// 角色列表.
$admin->addGet('/roles', [
    'controller' => 'roles',
    'action'     => 'index',
]);

// 编辑角色信息.
$admin->addGet('/roles/{id:[0-9]+}/edit', [
    'controller' => 'roles',
    'action'     => 'edit',
]);
$admin->addPost('/roles/update', [
    'controller' => 'roles',
    'action'     => 'update',
]);

// 添加角色.
$admin->addGet('/roles/create', [
    'controller' => 'roles',
    'action'     => 'create',
]);

// 保存.
$admin->addPost('/roles/save', [
    'controller' => 'roles',
    'action'     => 'save',
]);

// 删除角色.
$admin->addPost('/roles/delete', [
    'controller' => 'roles',
    'action'     => 'delete',
]);

// 角色权限列表.
$admin->addGet('/roles/{id:[0-9]+}/permission', [
    'controller' => 'roles',
    'action'     => 'permission',
]);

// 保存角色权限.
$admin->addPost('/roles/permission', [
    'controller' => 'roles',
    'action'     => 'savePermission',
]);