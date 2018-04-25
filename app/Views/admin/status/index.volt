{% extends "layout/main.volt" %}

{% block content %}

    <!--面包屑导航 开始-->
    <div class="crumb_warp">
        <i class="fa fa-home"></i> <a href="{{url('admin/info')}}">首页</a> &raquo; 事件管理
    </div>

    <!--面包屑导航 结束-->
        {% if _session['user_is_super'] or (_session['user_is_admin'] and _session['project_id'] == '') %}
        <div class="search_wrap">
            <form action="{{url('admin/status')}}" method="get" name="search_form">
                <table class="search_tab">
                    <tr>
                         {% Include 'layout/search_list1' with ['type': -1] %}
                        <td><input type="submit" class="btn btn-info" value="查询"></td>
                    </tr>
                </table>
            </form>
        </div>
        {% endif %}
    {{ content() }}
    <p><?php $this->flashSession->output() ?></p>
    <div class="result_wrap">
        <div class="result_title">
                <div style="background-color: #e5e9ec;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1.上/下班默认事件是指在设置的上/下班期间内如果没有特别的事件则默认的事件;<br/>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2.系统会初始化上/下班默认事件，如单位已设置自己的上/下班事件，则会以单位设置的优先上
                </div>
        </div>
        <!--快捷导航 开始-->
        <div class="result_content">
            <div class="short_wrap">
                <a href="{{url('admin/status/create')}}"><i class="fa fa-plus"></i>添加事件</a>
            </div>
        </div>
        <!--快捷导航 结束-->
    </div>

    <div class="result_wrap">
        <div class="result_content">
            <table class="list_tab">
                <tr>
                    <th class="tc" width="5%">排序</th>
                    {#<th class="tc" width="5%">ID</th>#}
                    <th>事件名称</th>
                    <th>事件颜色</th>
                    <th>单位</th>
                    <th>默认事件</th>
                    {#<th>创建人</th>#}
                    <th>创建时间</th>
                    <th>操作</th>
                </tr>
                {% if status_list is not empty %}
                {% for v in status_list.items %}
                <tr>
                    <td class="tc">
                        <input type="text" onchange="changeOrder(this,{{v.status.status_id}})" value="{{v.status.status_order}}">
                    </td>
                    {#<td class="tc">{{$v->status_id}}</td>#}
                    <td>
                        <a href="#">{{v.status.status_name}}</a>
                        {#<span style="border-radius: 10%; background-color:{{$v->status_color}};display: inline-block"><span style="margin:  5px 20px">{{$v->status_name}}</span></span>#}
                    </td>
                    <td>
                        <span class="status_color"  data-id="{{v.status.status_id}}" style="display: inline-block;width: 10px;height: 10px;border-radius: 100%;background: {{v.status.status_color}}"></span>
                    </td>
                    <td>{{v.project_name}}</td>
                    <td>{{(v.status.status_is_default == 1 ) ? '上班默认事件' : ((v.status.status_is_default == 2 ) ? '下班默认事件' : '否')}}</td>
                    {#<td>{{$v->created_name}}</td>#}
                    <td>{{date("Y-m-d H:i:s",v.status.created_at)}}</td>
                    <td>
                        {% if v.status.project_id != 0 %}
                        <a href="{{url('admin/status/' ~ v.status.status_id ~ '/edit')}}">修改</a>
                        <a href="javascript:;" onclick="del({{v.status.status_id}})">删除</a>
                            {% if v.status.status_is_default == 0 %}
                            <a href="javascript:;" onclick="set_default({{v.status.project_id}},{{v.status.status_id}})">设为默认</a>
                            {% endif %}
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
                <label>共 {{ status_list.total_items }} 条记录</label>
                {% if status_list.total_pages > 1 %}
                    <div style="float: right">
                        <ul class="paginate">
                            <li class="disabled"><span>总计: {{ status_list.total_pages }} 页</span></li>
                            <li class="active"><span>当前第: {{ status_list.current }} 页</span></li>
                            {% if input['project_id'] is defined %}
                                {% if status_list.current == 1 %}
                                    <li class="disabled"><span>第一页</span></li>
                                {% else %}
                                    <li><a href="/admin/status?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&page=1">第一页</a></li>
                                {% endif %}
                                {% if status_list.current == 1 %}
                                    <li class="disabled"><span>上一页</span></li>
                                {% else %}
                                    <li><a href="/admin/status?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&page={{ status_list.before }}">上一页</a></li>
                                {% endif %}
                                {% if status_list.current == status_list.last or status_list.last == 0 %}
                                    <li class="disabled"><span>下一页</span></li>
                                {% else %}
                                    <li><a href="/admin/status?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&page={{ status_list.next }}">下一页</a></li>
                                {% endif %}
                                {% if status_list.current == status_list.last or status_list.last == 0 %}
                                    <li class="disabled"><span>最后一页</span></li>
                                {% else %}
                                    <li><a href="/admin/status?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&page={{ status_list.last }}">最后一页</a></li>
                                {% endif %}
                            {% else %}
                                {% if status_list.current == 1 %}
                                    <li class="disabled"><span>第一页</span></li>
                                {% else %}
                                    <li><a href="/admin/status">第一页</a></li>
                                {% endif %}
                                {% if status_list.current == 1 %}
                                    <li class="disabled"><span>上一页</span></li>
                                {% else %}
                                    <li><a href="/admin/status?page={{ status_list.before }}">上一页</a></li>
                                {% endif %}
                                {% if status_list.current == status_list.last or status_list.last == 0 %}
                                    <li class="disabled"><span>下一页</span></li>
                                {% else %}
                                    <li><a href="/admin/status?page={{ status_list.next }}">下一页</a></li>
                                {% endif %}
                                {% if status_list.current == status_list.last or status_list.last == 0 %}
                                    <li class="disabled"><span>最后一页</span></li>
                                {% else %}
                                    <li><a href="/admin/status?page={{ status_list.last }}">最后一页</a></li>
                                {% endif %}
                            {% endif %}
                        </ul>
                    </div>
                {% endif %}
            </div>

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
        function changeOrder(obj,status_id){
            var status_order = $(obj).val();
            $.ajax({
                url: "{{url('admin/status/changeOrder')}}",
                type: "POST",
                dataType: 'JSON',
                data: {
                    "{{ _csrfKey }}": "{{ _csrf }}",
                    'status_id': status_id,
                    'status_order': status_order
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
        }

        //删除事件
        function del(status_id) {
            layer.confirm('您确定要删除这个事件吗？', {
                btn: ['确定','取消'] //按钮
            }, function(){
                $.ajax({
                    url: "{{ url('admin/status/delete') }}",
                    type: "POST",
                    dataType: 'JSON',
                    data: {
                        '_method': 'delete',
                        "{{ _csrfKey }}": "{{ _csrf }}",
                        'status_id': status_id,
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

        //设置默认事件
        function set_default(project_id,status_id) {
            var content = '<div padding="10px"><form name="default_status"><table align="left">'+
                        '<th width="120">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;设为默认:</th>'+
                        ' <td><br>'+
                        '<input type="radio" name="status_is_default" value="1" checked>上班默认事件&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+
                        '<input type="radio" name="status_is_default" value="2">下班默认事件'+
                        '</td>'+
                        '</table>'+
                        '</div>';
            layer.open({
                type: 1,
                btn:['确定','取消'],
                area : ['450px','150px'],
                skin: 'layui-layer-rim', //加上边框
                content: content,
                yes:function(){
                    var status_is_default = $('input[name="status_is_default"]:checked').val();
                    $.ajax({
                        url: "{{ url('admin/status/setDefault') }}",
                        type: "POST",
                        dataType: 'JSON',
                        async: false,
                        data: {
                            "{{ _csrfKey }}": "{{ _csrf }}",
                            'project_id': project_id,
                            'status_id': status_id,
                            'status_is_default': status_is_default
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
                    })
                }
            });
        }

    </script>

{% endblock %}

