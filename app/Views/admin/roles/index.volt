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
        {{ content() }}
        <p><?php $this->flashSession->output() ?></p>
        <div class="result_content">
            <table class="list_tab">
                <tbody>
                <tr>
                    <th style="width: 10px">ID</th>
                    <th>角色名称</th>
                    <th>角色编码</th>
                    <th>角色描述</th>
                    <th>操作</th>
                </tr>
                {% if roles is not empty %}
                    {% for role in roles.items %}
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
                {% else %}
                    <tr>
                        <td col="6">暂无数据</td>
                    </tr>
                {% endif %}
                </tbody>
            </table>
        </div>

        <div class="page_list clear" >
            <label>共 {{ roles.total_items }} 条记录</label>
            {% if roles.total_pages > 1 %}
                <div style="float: right">
                    <ul class="paginate">
                        <li class="disabled"><span>总计: {{ roles.total_pages }} 页</span></li>
                        <li class="active"><span>当前第: {{ roles.current }} 页</span></li>
                        {% if roles.current == 1 %}
                            <li class="disabled"><span>第一页</span></li>
                        {% else %}
                            <li><a href="/admin/roles">第一页</a></li>
                        {% endif %}
                        {% if roles.current == 1 %}
                            <li class="disabled"><span>上一页</span></li>
                        {% else %}
                            <li><a href="/admin/roles?page={{ roles.before }}">上一页</a></li>
                        {% endif %}
                        {% if roles.current == roles.last or roles.last == 0 %}
                            <li class="disabled"><span>下一页</span></li>
                        {% else %}
                            <li><a href="/admin/roles?page={{ roles.next }}">下一页</a></li>
                        {% endif %}
                        {% if roles.current == roles.last or roles.last == 0 %}
                            <li class="disabled"><span>最后一页</span></li>
                        {% else %}
                            <li><a href="/admin/roles?page={{ roles.last }}">最后一页</a></li>
                        {% endif %}
                    </ul>
                </div>
            {% endif %}
        </div>

    </div>

    <style>
        .result_content ul li span {
            font-size: 15px;
            padding: 6px 12px;
        }
        .active {
            color: #fff;
            cursor: default;
            background-color: #337ab7;
            border-color: #337ab7;
        }
        .disabled {
            color: #777;
            cursor: not-allowed;
            background-color: #fff;
            border-color: #ddd;
        }
    </style>

    <script>
        //删除
        function delArt(id) {
            layer.confirm('删除此角色时，使用该角色的用户权限将失效，您确定要删除此角色吗？', {
                btn: ['确定','取消']
            }, function(){
                $.ajax({
                    url: "{{url('admin/roles/delete')}}",
                    type: "POST",
                    dataType: 'JSON',
                    data: {
                        'id': id,
                        "{{ _csrfKey }}": "{{ _csrf }}",
                    },
                    success:function(data){
                        if(data.status == 201) {
                            layer.msg(data.msg, {
                                icon: 6,
                                time: 2000, //2s后自动关闭
                            },function (){
                                location.reload();
                            });
                        }else{
                            layer.msg(data.msg, {icon: 5});
                        }
                    },
                    error:function(){
                        layer.msg('操作失败，请稍后重试！', {icon: 2});
                    }
                })
            });
        }
    </script>

{% endblock %}