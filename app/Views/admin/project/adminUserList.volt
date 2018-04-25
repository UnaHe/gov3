{% extends "layout/main.volt" %}

{% block content %}
    <!--结果页快捷搜索框 开始-->
    <div class="search_wrap">
        <form name="search_form" action="{{url('admin/project/adminuserlist')}}" method="get">
            <table class="search_tab">
                <tr>
                    <th width="120">管理员类型:</th>
                    <td>
                        <select name="admin_type" class="multiselect_no_search">
                            <option value=""> 请选择</option>
                            <option value="1" {{ input['admin_type'] is defined and input['admin_type'] == 1 ? 'selected' : '' }}> 单位管理员</option>
                            <option value="2" {{ input['admin_type'] is defined and input['admin_type'] == 2 ? 'selected' : '' }}> 系统管理员</option>
                        </select>
                    </td>
                    {% Include 'layout/search_list1' with ['type': -1] %}
                    <th width="70">关键字:</th>
                    <td>
                        <input type="text" name="keywords" placeholder="可搜索姓名,电话,项目名" value="{{ input['keywords'] is defined ? input['keywords'] : '' }}"><br>
                    </td>
                    <td><input type="submit"  class="btn btn-info" value="查询"></td>
                </tr>
            </table>
        </form>
    </div>
    <div class="result_wrap">
        <!--快捷导航 开始-->
        <div class="result_content">
            <div class="short_wrap">
                <a href="{{url('admin/project/createadmin')}}"><i class="fa fa-plus"></i>创建管理员</a>
                <a href="{{url('admin/project/adminuserlist')}}"><i class="fa fa-recycle"></i>全部管理员</a>
            </div>
        </div>
        <!--快捷导航 结束-->
    </div>
    <!--搜索结果页面 列表 开始-->
    {{ content() }}
    <p><?php $this->flashSession->output() ?></p>
    <div class="result_wrap">
        <div class="result_content">
            <table class="list_tab">
                <tr>
                    <th class="tc" width="3%">ID</th>
                    <th>姓名</th>
                    <th>电话</th>
                    <th>性别</th>
                    <th>年龄</th>
                    <th>所属单位</th>
                    <th>科室</th>
                    <th>职务</th>
                    <th width="15%">操作</th>
                </tr>
                {% for v in data['list'].items %}
                <tr>
                    <td class="tc">{{ v.users.user_id }}</td>
                    <td>{{ v.users.user_name }}</td>
                    <td>{{ v.users.user_phone }}</td>
                    <td>{{ v.users.user_sex == 1 ? '男' : '女' }}</td>
                    <td>{{ v.users.user_age }}</td>
                    <td>{{ v.project_name }}</td>
                    <td>{{ v.cate_name }}</td>
                    <td>{{ v.users.user_job }}</td>
                    <td>
                        <a href="{{ url('admin/project/createadmin?user_id=' ~ v.users.user_id) }}">编辑</a>
                        <a href="javascript:void(0);" onclick="delArt({{ v.users.user_id }})">删除</a>
                        <a href="javascript:void(0);" onclick="resetPwd({{ v.users.user_id }})">重置密码</a>
                        {#@can('system')#}
                        {#<a href="{{url('admin/users/'.v.user_id.'/role')}}" >角色管理</a>#}
                        {#@endcan#}
                    </td>
                </tr>
                {% endfor %}
            </table>

            <div class="page_list clear" >
                <label>共 {{ data['list'].total_items }} 条记录</label>
                {% if data['list'].total_pages > 1 %}
                <div style="float: right">
                    <ul class="paginate">
                        <li class="disabled"><span>总计: {{ data['list'].total_pages }} 页</span></li>
                        <li class="active"><span>当前第: {{ data['list'].current }} 页</span></li>
                        {% if input['admin_type'] is defined or input['keywords'] is defined or input['project_id'] is defined %}
                            {% if data['list'].current == 1 %}
                                <li class="disabled"><span>第一页</span></li>
                            {% else %}
                                <li><a href="/admin/project/adminuserlist?admin_type={{ input['admin_type'] is defined ? input['admin_type'] : '' }}&project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&keywords={{ input['keywords'] is defined ? input['keywords'] : '' }}&page=1">第一页</a></li>
                            {% endif %}
                            {% if data['list'].current == 1 %}
                                <li class="disabled"><span>上一页</span></li>
                            {% else %}
                                <li><a href="/admin/project/adminuserlist?admin_type={{ input['admin_type'] is defined ? input['admin_type'] : '' }}&project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&keywords={{ input['keywords'] is defined ? input['keywords'] : '' }}&page={{ data['list'].before }}">上一页</a></li>
                            {% endif %}
                            {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                <li class="disabled"><span>下一页</span></li>
                            {% else %}
                                <li><a href="/admin/project/adminuserlist?admin_type={{ input['admin_type'] is defined ? input['admin_type'] : '' }}&project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&keywords={{ input['keywords'] is defined ? input['keywords'] : '' }}&page={{ data['list'].next }}">下一页</a></li>
                            {% endif %}
                            {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                <li class="disabled"><span>最后一页</span></li>
                            {% else %}
                                <li><a href="/admin/project/adminuserlist?admin_type={{ input['admin_type'] is defined ? input['admin_type'] : '' }}&project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&keywords={{ input['keywords'] is defined ? input['keywords'] : '' }}&page={{ data['list'].last }}">最后一页</a></li>
                            {% endif %}
                        {% else %}
                            {% if data['list'].current == 1 %}
                                <li class="disabled"><span>第一页</span></li>
                            {% else %}
                                <li><a href="/admin/project/adminuserlist">第一页</a></li>
                            {% endif %}
                            {% if data['list'].current == 1 %}
                                <li class="disabled"><span>上一页</span></li>
                            {% else %}
                                <li><a href="/admin/project/adminuserlist?page={{ data['list'].before }}">上一页</a></li>
                            {% endif %}
                            {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                <li class="disabled"><span>下一页</span></li>
                            {% else %}
                                <li><a href="/admin/project/adminuserlist?page={{ data['list'].next }}">下一页</a></li>
                            {% endif %}
                            {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                <li class="disabled"><span>最后一页</span></li>
                            {% else %}
                                <li><a href="/admin/project/adminuserlist?page={{ data['list'].last }}">最后一页</a></li>
                            {% endif %}
                        {% endif %}
                    </ul>
                </div>
                {% endif %}
            </div>

        </div>
    </div>
    <!--搜索结果页面 列表 结束-->

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

    {{ javascript_include('js/lib/jquery.md5.js') }}
    <script type="text/javascript">
        $(function(){
            $(".multiselect_no_search").multiselect(multiselect_option_no_search);
            if('{{ input['admin_type'] is defined ? input['admin_type'] : 1 }}' == '2'){
                $(".project_type").addClass("hide");
            }
            $("select[name='admin_type']").change(function () {
                var type = $(this).val();
                if(type == 2){
                    $(".project_type").addClass("hide");
                    $("select[name='project_id']").val("");
                }else{
                    $(".project_type").removeClass("hide");
                }
            })
        });
        //删除员工
        function delArt(id) {
            layer.confirm('您确定要删除此员工吗？', {
                btn: ['确定','取消']
            }, function(){
                $.ajax({
                    url: '{{ url('admin/users/delete') }}',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        "user_id": id,
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

        //重置密码
        function resetPwd(user_id) {
            layer.confirm('重置密码为123456', {
                btn: ['确定','取消']
            }, function(){
                $.ajax({
                    url: '{{ url('admin/users/resetpwd') }}',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        'user_id': user_id,
                        "{{ _csrfKey }}": "{{ _csrf }}",
                    },
                    success: function(data){
                        if(data.status == 201){
                            layer.msg(data.msg, {
                                icon: 6,
                                time: 2000, //2s后自动关闭
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