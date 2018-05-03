{% extends "layout/main.volt" %}

{% block content %}

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
        {{ content() }}
        <p><?php $this->flashSession->output() ?></p>
        <div class="result_content">
            <table class="list_tab">
                <tbody>
                <tr>
                    {#<th style="width: 10px">#</th>#}
                    <th>权限名称</th>
                    <th>描述</th>
                    <th>操作</th>
                </tr>
                {% if permissions is not empty %}
                    {% for permission in permissions.items %}
                        <tr>
                            {#td>{{$permission->id}}.</td>#}
                            <td>{{permission.name}}</td>
                            <td>{{permission.description}}</td>
                            <td>
                                <a href="{{url('admin/permissions/' ~ permission.id ~ '/edit')}}">编辑</a>
                                <a href="javascript:;" onclick="delArt({{ permission.id }})">删除</a>
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
            <label>共 {{ permissions.total_items }} 条记录</label>
            {% if permissions.total_pages > 1 %}
                <div style="float: right">
                    <ul class="paginate">
                        <li class="disabled"><span>总计: {{ permissions.total_pages }} 页</span></li>
                        <li class="active"><span>当前第: {{ permissions.current }} 页</span></li>
                            {% if permissions.current == 1 %}
                                <li class="disabled"><span>第一页</span></li>
                            {% else %}
                                <li><a href="/admin/permissions">第一页</a></li>
                            {% endif %}
                            {% if permissions.current == 1 %}
                                <li class="disabled"><span>上一页</span></li>
                            {% else %}
                                <li><a href="/admin/permissions?page={{ permissions.before }}">上一页</a></li>
                            {% endif %}
                            {% if permissions.current == permissions.last or permissions.last == 0 %}
                                <li class="disabled"><span>下一页</span></li>
                            {% else %}
                                <li><a href="/admin/permissions?page={{ permissions.next }}">下一页</a></li>
                            {% endif %}
                            {% if permissions.current == permissions.last or permissions.last == 0 %}
                                <li class="disabled"><span>最后一页</span></li>
                            {% else %}
                                <li><a href="/admin/permissions?page={{ permissions.last }}">最后一页</a></li>
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
        //删除权限
        function delArt(permissionId) {
            layer.confirm('删除可能导致不可逆的权限问题,确认删除?', {
                btn: ['确定','取消']
            }, function(){
                $.ajax({
                    url: '{{ url('admin/permissions/delete') }}',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        "{{ _csrfKey }}": "{{ _csrf }}",
                        "id": permissionId,
                    },
                    success: function(data){
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
                    error: function() {
                        layer.msg('操作失败，请稍后重试！', {icon: 2});
                    }
                });
            });
        }
    </script>

{% endblock %}