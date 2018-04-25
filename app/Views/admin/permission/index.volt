{% extends "layout/main.volt" %} {% block content %}
    <!-- Main content -->
    <div class="crumb_warp">
        <!--<i class="fa fa-bell"></i> 欢迎使用登陆网站后台，建站的首选工具。-->
        <i class="fa fa-home"></i> <a href="{{url('admin/home')}}">首页</a> &raquo;权限列表
    </div>
    <div class="result_wrap">
        <div class="result_wrap">
            <!--快捷导航 开始-->
            <div class="result_content">
                <div class="short_wrap">
                    <a href="{{url('admin/permissions/create')}}"><i class="fa fa-plus"></i>添加权限</a>
                    <a href="{{url('admin/permissions')}}"><i class="fa fa-recycle"></i>全部权限</a>
                </div>
            </div>
            <!--快捷导航 结束-->
        </div>
        <div class="result_content">
            <table class="list_tab">
                <tbody>
                <tr>
                    {#<th style="width: 10px">#</th>#}
                    <th>权限名称</th>
                    <th>描述</th>
                    <th>操作</th>
                </tr>
                {% for permission in permissions %}
                    <tr>
                        {#td>{{$permission->id}}.</td>#}
                        <td>{{permission.name}}</td>
                        <td>{{permission.description}}</td>
                        <td>
                            <a href="{{url('admin/permissions/' ~ permission.id ~ '/edit')}}">编辑</a>
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
        {{permissions.links()}}
    </div>
{% endblock %}