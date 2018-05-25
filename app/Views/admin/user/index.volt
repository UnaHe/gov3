{% extends "layout/main.volt" %}

{% block content %}
    <div class="crumb_warp">
        <i class="fa fa-home"></i> <a href="{{url('admin/home')}}">首页</a> &raquo; 人员列表
    </div>
    <!--面包屑导航 结束-->
    <!--结果页快捷搜索框 开始-->
    <div class="search_wrap">
        <form action="" method="get" action="{{url('admin/users')}}">
            <table class="search_tab">
                <tr>
                    {#载入单位、部门、科室的查询#}
                    {% Include 'layout/search_list1' with ['type': 1] %}
                    <th width="70">关键字:</th>
                    <td>
                        <input type="text" name="keywords" placeholder="可搜索姓名，电话，科室" value="{{ input['keywords'] is defined ? input['keywords'] : '' }}"><br>
                    </td>
                    <td><input type="submit" class="btn btn-info" value="查询"></td>
                </tr>
            </table>
        </form>
    </div>
    {{ content() }}
    <p><?php $this->flashSession->output() ?></p>
    <div class="result_wrap">
        <div class="result_content">
            <div class="short_wrap">
                <a href="{{url('admin/users/create')}}"><i class="fa fa-plus"></i>添加员工</a>
                <a href="{{url('admin/users')}}"><i class="fa fa-recycle"></i>全部员工</a>
            </div>
        </div>
        <!--快捷导航 结束-->
    </div>

    <div class="result_wrap">
        <div class="result_content">
            <table class="list_tab">
                <tr>
                    <th class="tc" width="3%">ID</th>
                    <th>姓名</th>
                    <th>电话</th>
                    <th>所属单位</th>
                    <th>所属部门</th>
                    <th>所在科室</th>
                    <th>是否单位管理员</th>
                    <th>性别</th>
                    <th>年龄</th>
                    {#<th width="30%">简介</th>#}
                    <th>职务</th>
                    <th width="18%">操作</th>
                </tr>
                {% for v in data['list'].items %}
                    <tr>
                        <td class="tc">{{v.users.user_id}}</td>
                        <td>
                            {{v.users.user_name}}
                        </td>
                        <td>
                            {{v.users.user_phone}}
                        </td>
                        <td>
                            {{v.project_name}}
                        </td>
                        <td>
                            {{v.section_name}}
                        </td>
                        <td>
                            {{v.cate_name}}
                        </td>
                        <td>
                           {{v.users.user_is_admin ? "是" : "否"}}
                        </td>
                        <td>
                            {% if v.users.user_sex == 1 %} 男 {% else %} 女 {% endif %}
                        </td>
                        <td>
                            {{v.users.user_age}}
                        </td>
{#                            <td>
                            <a href="#">
                                {{str_limit($v->user_intro,'50','...')}}
                            </a>
                        </td>#}
                        <td>
                            {{v.users.user_job}}
                        </td>
                        <td>
                            <a href="{{url('admin/users/' ~ v.users.user_id ~ '/edit')}}">修改</a>
                            <a href="{{url('admin/users/addbelong?user_id=' ~ v.users.user_id)}}" target="_blank">新增归属</a>
                            <a href="javascript:void(0);" onclick="resetPwd({{v.users.user_id}})">重置密码</a>
                            <a href="javascript:;" onclick="delArt({{v.users.user_id}})">删除</a>
                            {% if _session['user_is_super'] or (_session['user_is_admin'] and _session['project_id'] == '') %}
                            <a href="{{url('admin/users/' ~ v.users.user_id ~ '/role')}}" >角色管理</a>
                            {% endif %}
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
                            <li class="active"><span>当前第: <input class="page_input" onchange="changePage(this.value)" onfocus="this.select()" value='{{ data['list'].current }}' /> 页</span></li>
                            {% if input['project_id'] is defined or input['section_id'] is defined or input['department_id'] is defined or input['keywords'] is defined %}
                                {% if data['list'].current == 1 %}
                                    <li class="disabled"><span>第一页</span></li>
                                {% else %}
                                    <li><a href="/admin/users?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&section_id={{ input['section_id'] is defined ? input['section_id'] : '' }}&department_id={{ input['department_id'] is defined ? input['department_id'] : '' }}&keywords={{ input['keywords'] is defined ? input['keywords'] : '' }}&page=1">第一页</a></li>
                                {% endif %}
                                {% if data['list'].current == 1 %}
                                    <li class="disabled"><span>上一页</span></li>
                                {% else %}
                                    <li><a href="/admin/users?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&section_id={{ input['section_id'] is defined ? input['section_id'] : '' }}&department_id={{ input['department_id'] is defined ? input['department_id'] : '' }}&keywords={{ input['keywords'] is defined ? input['keywords'] : '' }}&page={{ data['list'].before }}">上一页</a></li>
                                {% endif %}
                                {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                    <li class="disabled"><span>下一页</span></li>
                                {% else %}
                                    <li><a href="/admin/users?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&section_id={{ input['section_id'] is defined ? input['section_id'] : '' }}&department_id={{ input['department_id'] is defined ? input['department_id'] : '' }}&keywords={{ input['keywords'] is defined ? input['keywords'] : '' }}&page={{ data['list'].next }}">下一页</a></li>
                                {% endif %}
                                {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                    <li class="disabled"><span>最后一页</span></li>
                                {% else %}
                                    <li><a href="/admin/users?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&section_id={{ input['section_id'] is defined ? input['section_id'] : '' }}&department_id={{ input['department_id'] is defined ? input['department_id'] : '' }}&keywords={{ input['keywords'] is defined ? input['keywords'] : '' }}&page={{ data['list'].last }}">最后一页</a></li>
                                {% endif %}
                            {% else %}
                                {% if data['list'].current == 1 %}
                                    <li class="disabled"><span>第一页</span></li>
                                {% else %}
                                    <li><a href="/admin/users">第一页</a></li>
                                {% endif %}
                                {% if data['list'].current == 1 %}
                                    <li class="disabled"><span>上一页</span></li>
                                {% else %}
                                    <li><a href="/admin/users?page={{ data['list'].before }}">上一页</a></li>
                                {% endif %}
                                {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                    <li class="disabled"><span>下一页</span></li>
                                {% else %}
                                    <li><a href="/admin/users?page={{ data['list'].next }}">下一页</a></li>
                                {% endif %}
                                {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                    <li class="disabled"><span>最后一页</span></li>
                                {% else %}
                                    <li><a href="/admin/users?page={{ data['list'].last }}">最后一页</a></li>
                                {% endif %}
                            {% endif %}
                        </ul>
                    </div>
                {% endif %}
            </div>

        </div>
    </div>

    {{ javascript_include('admin/style/js/jquery.md5.js') }}

    <script>
        // 修改页码.
        function changePage(page) {
            var total_pages = {{ data['list'].total_pages }};
            if (page > total_pages) {
                layer.msg('不能大于总'+total_pages+'页', {icon: 5});
                return;
            }
            location.href = "/admin/users?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&section_id={{ input['section_id'] is defined ? input['section_id'] : '' }}&department_id={{ input['department_id'] is defined ? input['department_id'] : '' }}&keywords={{ input['keywords'] is defined ? input['keywords'] : '' }}&page=" + page;
        }

        // 删除员工.
        function delArt(user_id) {
            layer.confirm('删除此用户时，系统会删除他的一切相关信息，您确定要删除此用户吗？', {
                btn: ['确定','取消']
            }, function(){
                $.ajax({
                    url: '{{ url('admin/users/delete') }}',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        "{{ _csrfKey }}": "{{ _csrf }}",
                        "user_id": user_id,
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

        //根据部门搜索对应的用户
        $('#departments_list').change(function () {
            var val = $(this).find("option:selected").val();
            location.href = '/admin/users/filteruser/'+val;
        });

        function get_list_by_project(obj){
            var project_id = $(obj).val();
            $('.department_list').html('<option value=""> 请先选择单位</option>');
            if(project_id == ''){
                return false;
            }
            $.ajax({
                url: '{{url('admin/ajaxGetDepartmentsByProject')}}',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    "{{ _csrfKey }}": "{{ _csrf }}",
                    'project_id':project_id,
                },
                success: function(data){
                    if(data.state == 0){
                        var department_list = status_list = '<option value="">请选择</option>';
                        //部门列表
                        $.each(data.list.department_list,function(k,v){
                            department_list += '<option value="'+v.department_id+'"> '+v.department_name+'</option>';
                        });
                        $('.department_list').html(department_list);
                    }
                },
                error: function() {
                    layer.msg('操作失败，请稍后重试！', {icon: 2});
                }
            })
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