{% extends "layout/main.volt" %}

{% block content %}

    <!--面包屑导航 开始-->
    <div class="crumb_warp">
        <i class="fa fa-home"></i> <a href="{{ url('admin/home') }}">首页</a> &raquo; 人员状态管理
    </div>

    <!--面包屑导航 结束-->

    <!--结果页快捷搜索框 开始-->
    <div class="search_wrap">
        <form action="{{ url('admin/status/workerStatusList') }}" method="get" name="search_form">
            <table class="search_tab">
                <tr>
                    {#载入单位、部门、科室的查询#}
                    {% Include 'layout/search_list1' with ['type': 1] %}
                    <th width="70">姓名:</th>
                    <td>
                        <input type="text" name="user_name" value="{{ input['user_name'] is not empty ? input['user_name'] : '' }}">
                    </td>
                    {% if input['type'] is defined %}
                        <input type="hidden" name="type" value="{{ input['type'] is not empty ? input['type'] : '' }}">
                    {% endif %}
                    <td><input type="submit" class="btn btn-info" value="查询"></td>
                </tr>
            </table>
        </form>
    </div>
    <!--结果页快捷搜索框 结束-->

    <!--搜索结果页面 列表 开始-->
    <div class="result_wrap">
        <div class="result_content">
            <table class="list_tab">
                <tr>
                    <th class="tc" width="5%">ID</th>
                    <th>用户名</th>
                    <th>电话</th>
                    <th>单位</th>
                    <th>部门</th>
                    <th>科室</th>
                    <th>状态</th>
                    <th>说明</th>
                    <th>操作</th>
                </tr>
                {% if data is not empty %}
                    {% for v in data['list'].items %}
                        <tr>
                            <td class="tc">{{ v.a.user_id }}
                            </td>
                            <td class="">{{ v.a.user_name }}
                            </td>
                            <td class="">{{ v.a.user_phone }}
                            </td>
                            <td>
                                <a href="#">{{ v.a.project_name }}</a>
                            </td>
                            <td>
                                <a href="#">{{ v.a.section_name }}</a>
                            </td>
                            <td>
                                <a href="#">{{ v.a.department_name }}</a>
                            </td>
                            <td>
                                <span class="status_color" id="status_color" style="display: inline-block;width: 10px;height: 10px;border-radius: 100%;margin-top: 14px;background: {{ params[v.a.user_id]['status_color'] }}"></span>
                                {{ params[v.a.user_id]['status_name'] }}
                            </td>
                            <td>
                                {{ params[v.a.user_id]['user_status_desc'] }}
                            </td>
                            <td>
                                {#@if(!empty($data['input']['type']) && $data['input']['type'] == 'cate')#}
                                    {#<a href="javascript:;"#}
                                       {#onclick="remove_user({{$v->user_id}},'{{$v->user_name}}','{{$v->department_name}}')">移出部门</a>#}
                                {#@endif#}
                                {% if _session['user_is_super'] or _session['user_is_admin'] %}
                                    <a href="javascript:;"
                                       onclick="add_user_status({{ v.a.project_id }},{{ v.a.user_id }},{{ params[v.a.user_id]['status_id'] }},'{{ v.a.project_name }}','{{ v.a.department_name }}','{{ v.a.user_name }}')">新增计划</a>
                                {% endif %}

                            </td>
                        </tr>
                    {% endfor %}
                {% else %}
                    <tr>
                        <td col="6">暂无数据</td>
                    </tr>
                {% endif %}
            </table>

            <div class="page_list clear" >
                <label>共 {{ data['list'].total_items }} 条记录</label>
                {% if data['list'].total_pages > 1 %}
                    <div style="float: right">
                        <ul class="paginate">
                            <li class="disabled"><span>总计: {{ data['list'].total_pages }} 页</span></li>
                            <li class="active"><span>当前第: <input class="page_input" onchange="changePage(this.value)" onfocus="this.select()" value='{{ data['list'].current }}' /> 页</span></li>
                            {% if input['project_id'] is defined or input['department_id'] is defined or input['section_id'] is defined or input['user_name'] is defined %}
                                {% if data['list'].current == 1 %}
                                    <li class="disabled"><span>第一页</span></li>
                                {% else %}
                                    <li><a href="/admin/status/workerStatusList?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&section_id={{ input['section_id'] is defined ? input['section_id'] : '' }}&department_id={{ input['department_id'] is defined ? input['department_id'] : '' }}&user_name={{ input['user_name'] is defined ? input['user_name'] : '' }}&page=1">第一页</a></li>
                                {% endif %}
                                {% if data['list'].current == 1 %}
                                    <li class="disabled"><span>上一页</span></li>
                                {% else %}
                                    <li><a href="/admin/status/workerStatusList?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&section_id={{ input['section_id'] is defined ? input['section_id'] : '' }}&department_id={{ input['department_id'] is defined ? input['department_id'] : '' }}&user_name={{ input['user_name'] is defined ? input['user_name'] : '' }}&page={{ data['list'].before }}">上一页</a></li>
                                {% endif %}
                                {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                    <li class="disabled"><span>下一页</span></li>
                                {% else %}
                                    <li><a href="/admin/status/workerStatusList?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&section_id={{ input['section_id'] is defined ? input['section_id'] : '' }}&department_id={{ input['department_id'] is defined ? input['department_id'] : '' }}&user_name={{ input['user_name'] is defined ? input['user_name'] : '' }}&page={{ data['list'].next }}">下一页</a></li>
                                {% endif %}
                                {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                    <li class="disabled"><span>最后一页</span></li>
                                {% else %}
                                    <li><a href="/admin/status/workerStatusList?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&section_id={{ input['section_id'] is defined ? input['section_id'] : '' }}&department_id={{ input['department_id'] is defined ? input['department_id'] : '' }}&user_name={{ input['user_name'] is defined ? input['user_name'] : '' }}&page={{ data['list'].last }}">最后一页</a></li>
                                {% endif %}
                            {% else %}
                                {% if data['list'].current == 1 %}
                                    <li class="disabled"><span>第一页</span></li>
                                {% else %}
                                    <li><a href="/admin/status/workerStatusList">第一页</a></li>
                                {% endif %}
                                {% if data['list'].current == 1 %}
                                    <li class="disabled"><span>上一页</span></li>
                                {% else %}
                                    <li><a href="/admin/status/workerStatusList?page={{ data['list'].before }}">上一页</a></li>
                                {% endif %}
                                {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                    <li class="disabled"><span>下一页</span></li>
                                {% else %}
                                    <li><a href="/admin/status/workerStatusList?page={{ data['list'].next }}">下一页</a></li>
                                {% endif %}
                                {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                    <li class="disabled"><span>最后一页</span></li>
                                {% else %}
                                    <li><a href="/admin/status/workerStatusList?page={{ data['list'].last }}">最后一页</a></li>
                                {% endif %}
                            {% endif %}
                        </ul>
                    </div>
                {% endif %}
            </div>

        </div>
    </div>
    <!--搜索结果页面 列表 结束-->

    {{ stylesheet_link('org/datetimepicker/css/bootstrap-datetimepicker.min.css') }}
    {{ javascript_include('org/datetimepicker/js/bootstrap-datetimepicker.js') }}
    {{ javascript_include('org/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js') }}
    <script>
        // 修改页码.
        function changePage(page) {
            var total_pages = {{ data['list'].total_pages }};
            if (page > total_pages) {
                layer.msg('不能大于总'+total_pages+'页', {icon: 5});
                return;
            }
            location.href = "/admin/status/workerStatusList?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&section_id={{ input['section_id'] is defined ? input['section_id'] : '' }}&department_id={{ input['department_id'] is defined ? input['department_id'] : '' }}&user_name={{ input['user_name'] is defined ? input['user_name'] : '' }}&page=" + page;
        }

        function add_user_status(project_id, user_id, status_id, project_name, departmanet_name, user_name) {
            $.ajax({
                url: '{{ url('admin/status/ajaxGetStatusOptionByUser') }}',
                type: "POST",
                dataType: 'JSON',
                data: {
                    "{{ _csrfKey }}": "{{ _csrf }}",
                    'project_id': project_id,
                },
                success: function (data) {
                    if (data.status == 200) {
                        var status_option = '';
                        {#var _token = "{{csrf_token()}}";#}
                        $.each(data.msg, function (k, v) {
                            status_option += '<option value="' + v.status_id + '"><span style="display: inline-block;width: 10px;height: 10px;border-radius: 100%;background: ' + v.status_color + '"></span>' + v.status_name + '</option>';
                        });
                        var content = '<div style="padding: 10px">' +
                            '<div style="margin: 10px 20px;">' +
                            '<span>用户：' + project_name + '&nbsp;&raquo;&nbsp;' + departmanet_name + '&nbsp;&raquo;&nbsp;' + user_name + '</span>' +
                            '</div>' +
                            '<form action="" method="" id="status_form">' +
                            '<input type="hidden" name="user_id"  value="' + user_id + '">' +
                            // '<input type="hidden" name="_token" value="' + _token + '">' +
                            '<table class="add_tab">' +
                            '<tbody>' +
                            '<tr>' +
                            '<th width="150"><i class="require">*</i>事件名称：</th>' +
                            '<td>' +
                            '<select name="status_id" class="status_id">' +
                            '<option value="">==请选择==</option>' +
                            status_option +
                            '</select>' +
                            '<span><i class="fa fa-exclamation-circle yellow"></i>必须选择一个事件</span>' +
                            '</td>' +
                            '</tr>' +
                            '<tr>' +
                            '<th><i class="require">*</i>开始时间：</th>' +
                            '<td>' +
                            '<input type="text" name="start_time" class="form_datetime" value="">' +
                            '<span><i class="fa fa-exclamation-circle yellow"></i>事件开始时间必须填写</span>' +
                            '</td>' +
                            '</tr>' +
                            '<tr>' +
                            '<th><i class="require">*</i>结束时间：</th>' +
                            '<td>' +
                            '<input type="text" name="end_time" class="form_datetime" value="">' +
                            '<span><i class="fa fa-exclamation-circle yellow"></i>事件结束时间必须填写</span>' +
                            '</td>' +
                            '</tr>' +
                            '<tr>' +
                            '<th>说明：</th>' +
                            '<td>' +
                            '<textarea type="text" name="user_status_desc" ></textarea>' +
                            '</td>' +
                            '</tr>' +
                            '</table></form></div>';
                        layer.open({
                            title: '新增状态',
                            type: 1,
                            btn: ['确定', '取消'],
                            area: ['850px', 'auto'],
                            skin: 'layui-layer-rim', //加上边框
                            content: content,
                            success: function () {
                                $('.form_datetime').datetimepicker({
                                    todayBtn: 1,
                                    autoclose: 1,
                                    todayHighlight: 1,
                                    startView: 2,
                                    forceParse: 0,
                                    showMeridian: 1,
                                    language: 'zh-CN',
                                });
                            },
                            yes: function () {
                                if ($('.status_id').val() == '') {
                                    layer.msg('请选择一个事件！');
                                    return false;
                                }
                                if ($("input[name='start_time']").val() == '') {
                                    layer.msg('请选择开始时间！');
                                    return false;
                                }
                                if ($("input[name='end_time']").val() == '') {
                                    layer.msg('请选择结束时间！');
                                    return false;
                                }
                                var start_time = Date.parse($("input[name='start_time']").val());
                                var end_time = Date.parse($("input[name='end_time']").val());
                                if (start_time > end_time) {
                                    layer.msg('开始时间不能大于结束时间');
                                    return false;
                                }
                                var data = $("#status_form").serialize();
                                $.ajax({
                                    url: '{{ url('admin/status/saveUserStatus') }}',
                                    type: 'POST',
                                    dataType: 'JSON',
                                    async: false,
                                    data: data,
                                    success: function (data) {
                                        if(data.status == 201) {
                                            layer.msg(data.msg, {
                                                icon: 6,
                                                time: 2000, //2s后自动关闭
                                            },function (){
                                                location.href = '{{ url('admin/status/userStatus?project_id=') }}' + project_id;
                                            });
                                        }else{
                                            layer.msg(data.msg, {icon: 5});
                                        }
                                    },
                                    error: function() {
                                        layer.msg('操作失败，请稍后重试！', {icon: 2});
                                    }
                                })
                            },
                            error: function() {
                                layer.msg('操作失败，请稍后重试！', {icon: 2});
                            }
                        });
                    }
                },
                error: function() {
                    layer.msg('操作失败，请稍后重试！', {icon: 2});
                }
            })
        }

        function remove_user(user_id, user_name, department_name) {
            layer.confirm('确定要将员工' + user_name + '移出部门' + department_name + '吗？', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.ajax({
                    url: '{{ url('admin/category/removeUserByCate') }}',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        "{{ _csrfKey }}": "{{ _csrf }}",
                        'user_id': user_id
                    },
                    success: function (data) {
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
                })
            });
        }

    </script>

{% endblock %}
