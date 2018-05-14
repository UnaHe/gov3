{% extends "layout/main.volt" %}

{% block content %}
    <style>
        .wrap {
            width: 700px;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }
    </style>
    <!--面包屑导航 开始-->
    {{ stylesheet_link('admin/org/bigcolorpicker/css/jquery.bigcolorpicker.css') }}
    <div class="crumb_warp">
        <i class="fa fa-home"></i> <a href="{{ url('admin/home') }}">首页</a> &raquo; 告示管理
    </div>

    <!--面包屑导航 结束-->
    <div class="result_wrap">
        <div class="result_title">
            <h3>告示列表</h3>
            {{ content() }}
            <p><?php $this->flashSession->output() ?></p>
        </div>
        <div class="result_content">
            <div class="short_wrap">
                <a href="{{url('admin/notice/create')}}"><i class="fa fa-plus"></i>添加告示</a>
                <a href="{{url('admin/notice')}}"><i class="fa fa-recycle"></i>全部告示</a>
            </div>
        </div>
    </div>

    <!--搜索结果页面 列表 开始-->
    <!--结果页快捷搜索框 开始-->
    {% if _session['project_id'] is empty or _session['user_is_admin'] %}
    <div class="search_wrap">
        <form action="{{url('admin/notice')}}" method="get" name="search_form">
            <table class="search_tab">
                <tr>
                    {#载入单位、部门、科室的查询#}
                    {% Include 'layout/search_list1' with ['type': 0] %}
                    <td><input type="submit" class="btn btn-info" value="查询"></td>
                </tr>
            </table>
        </form>
    </div>
    {% endif %}

    {{ content() }}
    <p><?php $this->flashSession->output() ?></p>

    <div class="result_wrap">
        <div class="result_content">
            <table class="list_tab">
                <tr>
                    <th class="tc" width="5%">ID</th>
                    <th>单位</th>
                    <th>科室</th>
                    <th>告示</th>
                    <th>状态</th>
                    <th>添加时间</th>
                    <th>操作</th>
                </tr>
                {% if data['list'] is not empty %}
                    {% for v in data['list'].items %}
                        <tr>
                            <td class="tc">{{v.notice_id}}</td>
                            <td>{{v.project_name}}</td>
                            <td><?php echo strlen($v->department_name) > 20 ? mb_substr(rtrim(ltrim($v->department_name,'{'),'}'),0,20,'utf-8').'...' : rtrim(ltrim($v->department_name,'{'),'}'); ?></td>
                            <td>
                                <a href="#" onclick="showdetail({{v.notice_id}})">
                                    <?php echo strlen($v->notice_title) > 20 ? mb_substr($v->notice_title,0,20,'utf-8').'...' : $v->notice_title; ?>
                                </a>
                            </td>
                            <td>
                                {% if  _session['user_is_super'] or (_session['user_is_admin'] and _session['project_id'] == '') or v.created_user == _session['user_id'] %}
                                <select class="notice_status" data-id = "{{v.notice_id}}"
                                        {% if v.notice_status == 0 %}
                                            style="color: red"
                                        {% else %}
                                            style="color: green"
                                        {% endif %}
                                >
                                    <option value="0" {{ v.notice_status == 0 ? 'selected' : '' }} style="color: red">
                                        未发布
                                    </option>
                                    <option value="1" {{ v.notice_status == 1 ? 'selected' : '' }}  style="color: green">
                                        已发布
                                    </option>
                                </select>
                                {% else %}
                                    {{v.notice_status == 1 ? '已发布' : '未发布'}}
                                {% endif %}
                            </td>
                            <td>{{ date("Y-m-d H:i:s", v.created_at) }}</td>
                            <td>
                                {% if  _session['user_is_super'] or (_session['user_is_admin'] and _session['project_id'] == '') or v.created_user == _session['user_id'] %}
                                <a href="{{ url('admin/notice/' ~ v.notice_id ~ '/edit') }}">编辑</a>
                                <a href="javascript:;"
                                   onclick="setting_department({{v.notice_id}},{{v.project_id}})">设置部门</a>
                                <a href="javascript:;" onclick="del({{v.notice_id}})">删除</a>
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
                        <li class="active"><span>当前第: {{ data['list'].current }} 页</span></li>
                        {% if input['project_id'] is defined or input['department_id'] is defined %}
                            {% if data['list'].current == 1 %}
                                <li class="disabled"><span>第一页</span></li>
                            {% else %}
                                <li><a href="/admin/notice?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&department_id={{ input['department_id'] is defined ? input['department_id'] : '' }}&page=1">第一页</a></li>
                            {% endif %}
                            {% if data['list'].current == 1 %}
                                <li class="disabled"><span>上一页</span></li>
                            {% else %}
                                <li><a href="/admin/notice?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&department_id={{ input['department_id'] is defined ? input['department_id'] : '' }}&page={{ data['list'].before }}">上一页</a></li>
                            {% endif %}
                            {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                <li class="disabled"><span>下一页</span></li>
                            {% else %}
                                <li><a href="/admin/notice?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&department_id={{ input['department_id'] is defined ? input['department_id'] : '' }}&page={{ data['list'].next }}">下一页</a></li>
                            {% endif %}
                            {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                <li class="disabled"><span>最后一页</span></li>
                            {% else %}
                                <li><a href="/admin/notice?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&department_id={{ input['department_id'] is defined ? input['department_id'] : '' }}&page={{ data['list'].last }}">最后一页</a></li>
                            {% endif %}
                        {% else %}
                            {% if data['list'].current == 1 %}
                                <li class="disabled"><span>第一页</span></li>
                            {% else %}
                                <li><a href="/admin/notice">第一页</a></li>
                            {% endif %}
                            {% if data['list'].current == 1 %}
                                <li class="disabled"><span>上一页</span></li>
                            {% else %}
                                <li><a href="/admin/notice?page={{ data['list'].before }}">上一页</a></li>
                            {% endif %}
                            {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                <li class="disabled"><span>下一页</span></li>
                            {% else %}
                                <li><a href="/admin/notice?page={{ data['list'].next }}">下一页</a></li>
                            {% endif %}
                            {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                <li class="disabled"><span>最后一页</span></li>
                            {% else %}
                                <li><a href="/admin/notice?page={{ data['list'].last }}">最后一页</a></li>
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

    <script>

        //设置部门
        function setting_department(notice_id, project_id) {
            $.ajax({
                url: '{{url('admin/notice/ajaxGetDepartments')}}',
                type: "POST",
                dataType: 'JSON',
                data: {
                    "{{ _csrfKey }}": "{{ _csrf }}",
                    'notice_id': notice_id,
                    'project_id': project_id
                },
                success: function (data) {
                    if (data.status == 200) {
                        var used_department_list = JSON.stringify(data.msg.used_department_list);
                        content = '<div padding="10px">' +
                            '<div style="background-color: #e5e9ec;margin:10px;" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1.选中部门的多选框则将公告分发到部门;<br>' +
                            '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2.所有的操作确认后生效' +
                            '</div>' +
                            '<form name="user_form" id="user_form">' +
                            '<input type="hidden" name="notice_id" value="' + notice_id + '">' +
                            '<div style="padding-left: 20px">全选<input type="checkbox" class="checkAll"></div>' +
                            '<div class="checkbox_list" style="">';
                        $.each(data.msg.department_list, function (k, v) {
                            var checked = '';
                            if (used_department_list.indexOf(v.department_id) > -1) {
                                checked = 'checked="checked"';
                            }
                            content += '<div style="display: inline-block;margin:10px 30px;width: 250px;">' +
                                '<input type="checkbox" class="departmentli" name="departments[]" value="' + v.department_id + '" ' + checked + '>' +
                                v.department_name +
                                '</div>';
                        });
                        content += '</div></from>' +
                            '</div>';
                        layer.open({
                            title: '部门分配',
                            btn: ['确定', '取消'],
                            type: 1,
                            skin: 'layui-layer-rim', //加上边框
                            area: ['720px', 'auto'], //宽高
                            content: content,
                            success: function () {
                                //全选
                                $(".checkAll").click(function () {
                                    if ($(this).is(':checked')) {
                                        $('.departmentli').prop("checked", true);
                                    } else {
                                        $('.departmentli').prop("checked", false);
                                    }
                                })
////                                //单选用户
//                                $(".department").click(function () {
//                                    if ($(this).is(':checked')) {
//                                        $(this).next('.leaderli').prop("disabled", false);
//                                    } else {
//                                        $(this).next('.leaderli').prop("disabled", true).prop("checked", false);
//                                    }
//                                })
                            },
                            yes: function () {
                                var data = $("#user_form").serialize();
                                $.ajax({
                                    url: '{{url('admin/notice/updateNoticeDepartment')}}',
                                    type: 'post',
                                    dataType: 'json',
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
                        layer.msg(data.msg, {icon: 5});
                    }
                },
                error: function() {
                    layer.msg('操作失败，请稍后重试！', {icon: 2});
                }
            })
        }

        //删除
        function del(notice_id) {
            layer.confirm('您确定要删除吗？', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.ajax({
                    url: "{{url('admin/notice/delete')}}",
                    type: "POST",
                    dataType: 'JSON',
                    data: {
                        '_method': 'delete',
                        "{{ _csrfKey }}": "{{ _csrf }}",
                        'notice_id': notice_id
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
                url: '{{url('admin/notice/show')}}',
                type: "POST",
                dataType: 'JSON',
                data: {
                    'notice_id': id,
                    "{{ _csrfKey }}": "{{ _csrf }}",
                },
                success: function (data) {
                    var content = '<div style="padding: 10px">' + data.msg +
                        '</div>';
                    layer.open({
                        title: '详情',
                        type: 1,
                        skin: 'layui-layer-rim', //加上边框
                        area: ['720px', '300px'], //宽高
                        content: content
                    });
                },
                error: function() {
                    layer.msg('操作失败，请稍后重试！', {icon: 2});
                }
            });
        }

        $(function(){
            //发布
            $(".notice_status").change(function(){
                var notice_id = $(this).data("id");
                var status = $(this).find("option:selected").val();
                var status_name = status == 1 ? '发布': '取消发布';
                layer.confirm('您确定要'+status_name+'吗？', {
                    btn: ['确定', '取消'] //按钮
                }, function () {
                    $.ajax({
                        url: "{{url('admin/notice/changestatus')}}",
                        type: "POST",
                        dataType: 'JSON',
                        data: {
                            "{{ _csrfKey }}": "{{ _csrf }}",
                            'notice_id': notice_id,
                            'notice_status': status
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
            })
        })

    </script>

{% endblock %}
