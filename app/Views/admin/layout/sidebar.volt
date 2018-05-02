<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu">
            {% if _session['user_is_super'] or (_session['user_is_admin'] and _session['project_id'] == '') %}
            <li class="treeview {{ _Controller == 'ProjectController' or _Controller == 'HomeController' ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-university"></i> <span>单位管理</span>
                    <span class="pull-right-container"></span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/admin/project/create"><i class="fa fa-circle-o"></i> 添加单位</a></li>
                    <li><a href="/admin/project"><i class="fa fa-circle-o"></i> 单位列表</a></li>
                    <li><a href="/admin/project/createadmin"><i class="fa fa-circle-o"></i> 创建管理员</a></li>
                    <li><a href="/admin/project/adminuserlist"><i class="fa fa-circle-o"></i> 管理员列表</a></li>
                </ul>
            </li>
            {% else %}
            <li class="treeview {{ _Controller == 'ProjectController' or _Controller == 'HomeController' ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-university"></i> <span>单位详情</span>
                    <span class="pull-right-container"></span>
                </a>
                <ul class="treeview-menu">
                    {#{{dd(session('user')->project_id)}}#}
                    <li><a href="/admin/project/{{ _session['project_id'] }}/edit"><i class="fa fa-circle-o"></i> 编辑单位详情</a></li>
                </ul>
            </li>
            {% endif %}

            {% if _session['user_is_super'] or _session['user_is_admin'] %}
            <li class="treeview {{ (_Controller == 'DepartmentController' or _Controller == 'QrcodeController' or _Controller == 'NoticeController') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-th-list"></i> <span>科室管理</span>
                    <span class="pull-right-container"></span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/admin/department/create"><i class="fa fa-circle-o"></i> 添加科室</a></li>
                    <li><a href="/admin/department"><i class="fa fa-circle-o"></i> 科室列表</a></li>
                    {% if _session['user_is_super'] or (_session['user_is_admin'] and _session['project_id'] == '') %}
                        <li><a href="/admin/qrcode/0/edit"><i class="fa fa-qrcode"></i> 二维码绑定/编辑</a></li>
                    {% endif %}
                    <li><a href="/admin/qrcode"><i class="fa fa-qrcode"></i> 二维码列表</a></li>
                    <li><a href="/admin/notice/create"><i class="fa fa-circle-o"></i> 添加告示</a></li>
                    <li><a href="/admin/notice"><i class="fa fa-circle-o"></i> 告示列表</a></li>
                </ul>
            </li>
            {% endif %}

            {% if _session['user_is_super'] or _session['user_is_admin'] %}
            <li class="treeview {{ _Controller == 'SectionController' ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-list-alt"></i> <span>部门管理</span>
                    <span class="pull-right-container"></span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/admin/section/create"><i class="fa fa-circle-o"></i> 添加部门</a></li>
                    <li><a href="/admin/section"><i class="fa fa-circle-o"></i> 部门列表</a></li>
                </ul>
            </li>
            {% endif %}

            {% if _session['user_is_super'] or _session['user_is_admin'] %}
            <li class="treeview {{ _Controller == 'StatusController' ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-calendar"></i> <span>事件管理</span>
                    <span class="pull-right-container"></span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/admin/status/settingworktimelist"><i class="fa fa-circle-o"></i> 设置工作时间</a>
                    </li>
                    <li><a href="/admin/status/create"><i class="fa fa-circle-o"></i> 添加事件</a></li>
                    <li><a href="/admin/status"><i class="fa fa-circle-o"></i> 事件列表</a></li>
                    <li><a href="/admin/status/userStatus"><i class="fa fa-circle-o"></i> 已设事件</a></li>
                    <li><a href="/admin/status/workerStatusList"><i class="fa fa-circle-o"></i> 员工状态</a>
                    </li>
                </ul>
            </li>
            {% endif %}

            {% if _session['user_is_super'] or _session['user_is_admin'] %}
            <li class="treeview {{ _Controller == 'UserController' ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-male"></i> <span>人员管理</span>
                    <span class="pull-right-container"></span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/admin/users/create"><i class="fa fa-circle-o"></i> 添加人员</a></li>
                    <li><a href="/admin/users"><i class="fa fa-circle-o"></i> 人员列表</a></li>
                    <li><a href="/admin/users/belongs"><i class="fa fa-circle-o"></i> 归属管理</a></li>
                </ul>
            </li>
            {% endif %}

            {% if _session['user_is_super'] or _session['user_is_admin'] %}
            <li class="treeview {{ _Controller == 'CommentController' ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-envelope"></i> <span>留言管理</span>
                    <span class="pull-right-container"></span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/admin/comment"><i class="fa fa-circle-o"></i> 留言列表</a></li>
                </ul>
            </li>
            {% endif %}

            {% if _session['user_is_super'] or (_session['user_is_admin'] and _session['project_id'] == '') %}
            <li class="treeview {{ _Controller == 'PermissionsController' or _Controller == 'RolesController' ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-cog"></i> <span>系统管理</span>
                    <span class="pull-right-container"></span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/admin/permissions"><i class="fa fa-circle-o"></i> 权限管理</a></li>
                    {#<li><a href="/admin/users"><i class="fa fa-circle-o"></i> 人员管理</a></li>#}
                    <li><a href="/admin/roles"><i class="fa fa-circle-o"></i> 角色管理</a></li>
                </ul>
            </li>
            {% endif %}
        </ul>
    </section>
</aside>
