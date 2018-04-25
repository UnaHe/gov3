{% extends "layout/main.volt" %} {% block content %}
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
                        <input type="text" name="keywords" placeholder="可搜索姓名，电话，科室" value="{{data['input']['keywords'] is defined ? data['input']['keywords'] : '' }}"><br>
                    </td>
                    <td><input type="submit" class="btn btn-info" value="查询"></td>
                </tr>
            </table>
        </form>
    </div>
    <!--搜索结果页面 列表 开始-->
    <form action="#" method="post">
        <input type="hidden" name="{{ _csrfKey }}" value="{{ _csrf }}"/>
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
                    {% for v in data['data_list'] %}
                        <tr>
                            <td class="tc">{{v.user_id}}</td>
                            <td>
                                {{v.user_name}}
                            </td>
                            <td>
                                {{v.user_phone}}
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
                               {{v.user_is_admin ? "是" : "否"}}
                            </td>
                            <td>
                                {% if v.user_sex == 1 %} 男 {% else %} 女 {% endif %}
                            </td>
                            <td>
                                {{v.user_age}}
                            </td>
{#                            <td>
                                <a href="#">
                                    {{str_limit($v->user_intro,'50','...')}}
                                </a>
                            </td>#}
                            <td>
                                {{v.user_job}}
                            </td>
                            <td>
                                <a href="{{url('admin/users/' ~ v.user_id ~ '/edit')}}">修改</a>
                                <a href="{{url('admin/users/addbelong?user_id=' ~ v.user_id ~ '')}}" target="_blank">新增归属</a>
                                <a href="javascript:void(0);" onclick="resetPass({{v.user_id}})">重置密码</a>
                                <a href="javascript:;" onclick="delArt({{v.user_id}})">删除</a>
                                @can('system')
                                <a href="{{url('admin/users/' ~ v.user_id ~ '/role')}}" >角色管理</a>
                                @endcan
                            </td>
                        </tr>
                    {% endfor %}
                </table>
                <div class="page_list clear " >
                    <label>共 {{data['data_list'].total()}} 条记录</label>
                    <div style="float: right">
                        {{data['data_list'].links()}}</div>
                </div>
            </div>
        </div>
    </form>
    <!--搜索结果页面 列表 结束-->

    <style>
        .result_content ul li span {
            font-size: 15px;
            padding: 6px 12px;
        }
    </style>
    <script src="{{asset('admin/style/js/jquery.md5.js')}}"></script>
    <script>
        //删除员工
        function delArt(id) {
            layer.confirm('删除此用户时，系统会删除他的一切相关信息，您确定要删除此用户吗？', {
                btn: ['确定','取消']
            }, function(){
                $.ajax({
                    url: "{{url('admin/users/')}}/"+id,
                    type: "post",
                    data: {
                        '_method':'delete',
                        "{{ _csrfKey }}": "{{ _csrf }}",
                    },
                    dataType:'json',
                    success: function(data){
                        if(data.state==0){
                            location.reload();
                            layer.msg(data.msg, {icon: 6});
                        }else{
                            layer.msg(data.msg, {icon: 5});
                        }
                    },
                    error: function(data) {
                        layer.msg("操作失败，请稍后重试！");
                    }
                });
            }, function(){
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
                type: 'post',
                dataType :'json',
                data: {
                    '_token':"{{csrf_token()}}",
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
                error: function(data) {
                    if (!!data.responseJSON && data.responseJSON.error == 'Unauthenticated.') {
                        location.href="/admin/login";
                    }else{
                        layer.msg("操作失败，请稍后重试！");
                    }
                }
            })
        }
        //重置密码
        function resetPass(id) {
            layer.confirm('重置密码为123456', {
                btn: ['确定','取消']
            }, function(){

                $.ajax({
                    url: '{{url('admin/project/reset')}}',
                    type: "post",
                    data: {
                        'id': id,
                        '_token': "{{csrf_token()}}",
                        'user_pass':$.md5('123456')
                    },
                    success: function(data){
                        if(data.status==0){
                            layer.msg(data.msg, {icon: 6});
                            location.reload();
                        }else{
                            layer.msg(data.msg, {icon: 5});
                        }
                    },
                    error: function(data) {
                        if (!!data.responseJSON && data.responseJSON.error == 'Unauthenticated.') {
                            location.href="/admin/login";
                        }else{
                            layer.msg("操作失败，请稍后重试！");
                        }
                    }
                });

            }, function(){

            });
        }

    </script>
{% endblock %}