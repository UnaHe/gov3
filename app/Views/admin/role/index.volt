{% extends "layout/main.volt" %} 

{% block content %}
    <div class="crumb_warp">
        <!--<i class="fa fa-bell"></i> 欢迎使用登陆网站后台，建站的首选工具。-->
        <i class="fa fa-home"></i> <a href="{{url('admin/home')}}">首页</a> &raquo;角色列表
    </div>
    <div class="result_wrap">
        <div class="result_wrap">
            <!--快捷导航 开始-->
            <div class="result_content">
                <div class="short_wrap">
                    <a href="{{url('admin/roles/create')}}"><i class="fa fa-plus"></i>添加角色</a>
                    <a href="{{url('admin/roles')}}"><i class="fa fa-recycle"></i>全部角色</a>
                </div>
            </div>
            <!--快捷导航 结束-->
        </div>
        <div class="result_content">
            <table class="list_tab">
                <tbody>
                <tr>
                    <th style="width: 10px">#</th>
                    <th>角色名称</th>
                    <th>角色编码</th>
                    <th>角色描述</th>
                    <th>操作</th>
                </tr>
                {% for role in roles %}
                    <tr>
                        <td>{{role.id}}</td>
                        <td>{{role.name}}</td>
                        <td>{{role.code}}</td>
                        <td>{{role.description}}</td>
                        <td>
                            {% if(role.code !== 'administrator') %}
                                <a href="{{url('admin/roles/' ~ role.id ~ '/edit')}}">编辑</a>
                                <a type="button" class="btn" href="/admin/roles/{{role.id}}/permission">权限管理</a>
                                <a href="javascript:;" onclick="delArt({{role.id}})">删除</a>
                            {% endif %}
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
        {{roles.links()}}
    </div>
    <script>
        //删除
        function delArt(id) {
            layer.confirm('删除此角色时，使用该角色的用户权限将失效，您确定要删除此角色吗？', {
                btn: ['确定','取消']
            }, function(){
                $.ajax({
                    url: "{{url('admin/roles/')}}/"+id,
                    type: "POST",
                    dataType: 'JSON',
                    data: {
                        '_method': 'delete',
                        "{{ _csrfKey }}": "{{ _csrf }}",
                    },
                    success:function(data){
                        if(data.status==0){
                            location.reload();
                            layer.msg(data.msg, {icon: 6});
                        }else{
                            layer.msg(data.msg, {icon: 5});
                        }
                    },
                    error:function(){
                        layer.msg('操作失败，请稍后重试！', {icon: 2});
                    }
                })
            }, function(){

            });
        }
    </script>

{% endblock %}