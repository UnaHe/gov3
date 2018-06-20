{% extends "layout/main.volt" %}

{% block content %}

    <!--面包屑导航 开始-->
    <div class="crumb_warp">
        <!--<i class="fa fa-bell"></i> 欢迎使用登陆网站后台，建站的首选工具。-->
        <i class="fa fa-home"></i> <a href="{{url('admin/home')}}">首页</a> &raquo; 科室列表
    </div>

    <!--面包屑导航 结束-->
    <!--搜索结果页面 列表 开始-->
    {% if _session['user_is_super'] or (_session['user_is_admin'] and _session['project_id'] == '') %}
        <div class="search_wrap">
            <form action="{{url('admin/department')}}" method="get" name="search_form">
                {#<input type="hidden" name="{{ _csrfKey }}" value="{{ _csrf }}"/>#}
                <table class="search_tab">
                    <tr>
                        {% Include 'layout/search_list1' with ['type': -1] %}
                        <td><input type="submit" class="btn btn-info" value="查询"></td>
                    </tr>
                </table>
            </form>
        </div>
    {% endif %}
    <div class="result_wrap">
        <!--快捷导航 开始-->
        <div class="result_content">
            <div class="short_wrap">
                <a href="{{url('admin/department/create')}}"><i class="fa fa-plus"></i>添加科室</a>
                {#<a href="{{url('admin/department')}}"><i class="fa fa-recycle"></i>全部科室</a>#}
            </div>
        </div>
        <!--快捷导航 结束-->
    </div>
    {{ content() }}
    <p><?php $this->flashSession->output() ?></p>

    <div class="result_wrap">
        <div class="result_content">
            <table class="list_tab">
                <tr>
                    <th class="tc" width="5%">ID</th>
                    <th width="">名称</th>
                    <th width="">单位</th>
                    <th width="">人员数量</th>
                    {#<th width="">留言数量</th>#}
                    {#<th width="">介绍</th>#}
                    <th>操作</th>
                </tr>
                {% for v in departments %}
                    <tr>
                        <td class="tc">{{v.departments.department_id}}</td>
                        <td>
                            <a href="#" onclick="showdetail({{v.departments.department_id}})">{{v.departments.department_name}}</a>
                        </td>
                        <td class="tc">{{v.project_name}}</td>
                        <td class="tc">{{v.user_count}}</td>
                        {#<td>{{str_limit(rtrim(ltrim($v->leaders,'{'),'}'),'50','...')}}</td>#}
                        {#<td class="tc"><a href="{{url("admin/comment")}}">{{$v->comment_count}}</a></td>#}
                        {#<td>#}
                        {#<a href="#">{{$v->desc}}</a>#}
                        {#<a href="#" onclick="showdetail({{$v->department_id}})">#}
                        {#{{str_limit($v->department_desc,'50','... ...')}}#}
                        {#</a>#}
                        {#</td>#}
                        <td>
                            <a href="{{url('admin/department/' ~ v.departments.department_id ~ '/edit')}}">修改</a>
                            <a href="javascript:;" onclick="assign_user({{v.departments.project_id}},{{v.departments.department_id}},'{{v.departments.department_name}}')">人员分配</a>
                            <a href="{{url('admin/comment?project_id=' ~ v.departments.project_id ~ '&department_id=' ~ v.departments.department_id)}}">留言（{{v.comment_count}}）</a>
                            <a href="{{url('admin/notice?project_id=' ~ v.departments.project_id ~ '&department_id=' ~ v.departments.department_id)}}">告示</a>
                            <a href="{{url('admin/status/workerStatusList?type=cate&project_id=' ~ v.departments.project_id ~ '&department_id=' ~ v.departments.department_id)}}">人员状态</a>
                            <a href="javascript:;" onclick="delCate({{v.departments.department_id}})">删除</a>
                        </td>
                    </tr>
                {% endfor %}
            </table>

        </div>
    </div>
    <!--搜索结果页面 列表 结束-->

    <script>
        //删除科室
        function delCate(department_id) {
            layer.confirm('您确定要删除此科室?', {
                btn: ['确定', '取消']
            }, function () {
                $.ajax({
                    url: "{{url('admin/department/delete')}}",
                    type: "POST",
                    dataType: 'JSON',
                    data: {
                        "{{ _csrfKey }}": "{{ _csrf }}",
                        "department_id": department_id,
                    },
                    success:function(data){
                        if (data.status == 201) {
                            layer.msg(data.msg, {
                                icon: 6,
                                time: 2000, //2s后自动关闭
                            },function (){
                                location.reload();
                            });
                        } else {
                            layer.msg(data.msg, {icon: 5});
                        }
                    },
                    error:function(){
                        layer.msg('操作失败，请稍后重试！', {icon: 2});
                    }
                })
            });
        }

        //显示详情
        function showdetail(id) {
            $.ajax({
                url: '{{url("admin/department/show")}}',
                type: "POST",
                dataType: 'JSON',
                data: {
                    'department_id': id,
                    "{{ _csrfKey }}": "{{ _csrf }}",
                },
                success: function (data) {
                    if (data.status == 200) {
                        data.msg = data.msg == null ? '' : data.msg;
                        var content = '<div style="padding: 10px">' + data.msg + '</div>';
                        layer.open({
                            title: '详情',
                            type: 1,
                            shadeClose: true,
                            skin: 'layui-layer-rim', //加上边框
                            area: ['720px', '500px'], //宽高
                            content: content
                        });
                    } else {
                        layer.msg(data.msg, {icon: 5});
                    }
                },
                error: function() {
                    layer.msg('操作失败，请稍后重试！', {icon: 2});
                }
            });
        }

        function assign_user(project_id, department_id, department_name) {
            $.ajax({
                url: '{{ url('admin/department/ajaxGetUsersByDepartmentOrOthers') }}',
                type: "POST",
                dataType: 'JSON',
                data: {
                    'project_id': project_id,
                    'department_id': department_id,
                    "{{ _csrfKey }}": "{{ _csrf }}",
                },
                success: function (data) {
                    if (data.status == 200) {
                        content = '<div padding="10px">' +
                            '<div style="background-color: #e5e9ec;margin:10px;" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1.选中用户前的多选框则将用户分配的本科室;<br>' +
                            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2.所有的操作确认后生效' +
                            '</div>' +
                            '<form name="user_form" id="user_form">' +
                            '<input type="hidden" name="department_id" value="' + department_id + '">' +
                            '<div style="padding-left: 20px"><label for="checkAll">全选</label><input type="checkbox" class="checkAll" id="checkAll"></div>' +
                            '<div class="checkbox_list" style="">';
                        $.each(data.msg, function (k, v) {
                            content += '' +
                                '<label for="u_' + v.user_id + '" style="width: 100px;margin:5px"><input type="checkbox" class="userli" name="users[]" id="u_' + v.user_id + '" value="' + v.user_id + '" ' + ((v.department_id == department_id) ? 'checked="checked"' : '') + '>' + v.user_name + '</label>' +
                                '';
                        });
                        content += '</div></from></div>';
                        layer.open({
                            title: '人员分配',
                            btn: ['确定', '取消'],
                            type: 1,
                            skin: 'layui-layer-rim', //加上边框
                            area: ['720px', '500px'], //宽高
                            content: content,
                            success: function () {
                                //全选
                                $(".checkAll").click(function () {
                                    if ($(this).is(':checked')) {
                                        $('.userli').prop("checked", true);
                                        $('.leaderli').prop("disabled", false);
                                    } else {
                                        $('.userli').prop("checked", false);
                                        $('.leaderli').prop("disabled", true).prop("checked", false);
                                    }
                                });
                                //单选用户
                                $(".userli").click(function () {
                                    if ($(this).is(':checked')) {
                                        $(this).next('.leaderli').prop("disabled", false);
                                    } else {
                                        $(this).next('.leaderli').prop("disabled", true).prop("checked", false);
                                    }
                                })
                            },
                            yes: function () {
                                var data = $("#user_form").serialize();
                                $.ajax({
                                    url: '{{ url('admin/department/updateUsersDepartment') }}',
                                    type: 'POST',
                                    dataType: 'JSON',
                                    data: data,
                                    success: function (data) {
                                        if (data.status == 201) {
                                            layer.msg(data.msg, {
                                                icon: 6,
                                                time: 2000, //2s后自动关闭
                                            },function (){
                                                location.reload();
                                            });
                                        } else {
                                            layer.msg(data.msg, {icon: 5});
                                        }
                                    },
                                    error: function() {
                                        layer.msg('操作失败，请稍后重试！', {icon: 2});
                                    }
                                })
                            }
                        });
                    } else {
                        layer.msg('操作失败，请稍后重试！', {icon: 2});
                    }
                },
                error: function() {
                    layer.msg('操作失败，请稍后重试！', {icon: 2});
                }
            });
        }

    </script>

{% endblock %}