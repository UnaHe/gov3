{% extends "layout/main.volt" %}

{% block content %}

    <!--面包屑导航 开始-->
    {{ stylesheet_link('admin/org/bigcolorpicker/css/jquery.bigcolorpicker.css') }}
    <div class="crumb_warp">
        <i class="fa fa-home"></i> <a href="{{url('admin/home')}}">首页</a> &raquo; 人员已设事件
    </div>

    <!--面包屑导航 结束-->


    <!--搜索结果页面 列表 开始-->
    <!--结果页快捷搜索框 开始-->
    <div class="search_wrap">
        <form action="{{ url('admin/status/userStatus') }}" method="get" name="search_form">
            <table class="search_tab">
                <tr>
                    {% Include 'layout/search_list1' with ['type': 2] %}
                </tr>
                <tr>
                    <th width="80">事件:</th>
                    <td>
                        <select onchange="" class="status_list" name="status_id" class="multiselect">
                            <option value="">请选择</option>
                            {% if data['status_list'] is defined %}
                                {% for v in data['status_list'] %}
                                    <option value="{{v.status_id}}" {{ input['status_id'] is defined  and (input['status_id'] == v.status_id) ? 'selected' : '' }}>{{v.status_name}}</option>
                                {% endfor %}
                            {% endif %}
                        </select>
                    </td>
                    <th width="80">开始时间:</th>
                    <td><input type="text" class="form_datetime" name="start_time" value="{{ input['start_time'] is not empty ? input['start_time'] : '' }}">
                    </td>
                    <th width="80">结束时间:</th>
                    <td><input type="text"class="form_datetime"  name="end_time"value="{{ input['end_time'] is not empty ? input['end_time'] : '' }}">
                    </td>
                    <th width="">姓名:</th>
                    <td><input type="text" name="user_name" value="{{ input['user_name'] is not empty ? input['user_name'] : '' }}">
                    </td>
                    {% if input['type'] is defined %}
                        <input type="hidden" name="type"
                               value="{{ input['type'] is not empty ? input['type'] : '' }}">
                    {% endif %}
                    <td><input type="submit"  class="btn btn-info" value="查询"></td>
                </tr>
            </table>
        </form>
    </div>

    <div class="result_wrap">
        <div class="result_content">
            <table class="list_tab">
                <tr>
                    <th class="tc" width="5%">ID</th>
                    <th>用户名</th>
                    <th>电话</th>
                    <th>单位</th>
                    <th>部门</th>
                    <th>事件</th>
                    <th>说明</th>
                    <th>开始时间</th>
                    <th>结束时间</th>
                    <th>操作</th>
                </tr>
                {% if data['list'].items is not empty %}
                    {% for v in data['list'].items %}
                        <tr>
                            <td class="tc">
                                {{v.user_status.user_status_id}}
                            </td>
                            <td class="">{{v.user_name}}</td>
                            <td>
                                <a href="#">{{v.user_phone}}</a>
                            </td>
                            <td>{{v.project_name}}</td>
                            <td>{{v.department_name}}</td>
                            <td>
                                {#<span style="border-radius: 10%; background-color:{{$v->status_color}};display: inline-block"><span style="margin:  5px 15px">{{$v->status_name}}</span></span>#}
                                {{v.status_name}}
                                <span class="status_color" href="javasript:;"data-id="{{v.status_id}}" style="display: inline-block;width: 10px;height: 10px;border-radius: 100%;background: {{v.status_color}}"></span>
                            </td>
                            <td>{{v.user_status.user_status_desc}}</td>
                            <td>{{ date("Y-m-d H:i:s",v.user_status.start_time) }}</td>
                            <td>{{ date("Y-m-d H:i:s",v.user_status.end_time) }}</td>
                            <td>
                                <a href="javascript:;" onclick="change_status({{v.user_status.user_id}},{{v.user_status.user_status_id}},{{v.status_id}},{{v.project_id}},'{{v.project_name}}','{{v.department_name}}','{{date("Y-m-d H:i:s",v.user_status.start_time)}}','{{date("Y-m-d H:i:s",v.user_status.end_time)}}','{{v.user_status.user_status_desc}}','{{v.user_name}}')">修改</a>
                                <a href="javascript:;" onclick="del_status({{v.user_status.user_status_id}})">删除</a>
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
                            {% if input['project_id'] is defined or input['section_id'] is defined  or input['department_id'] is defined or input['status_id'] is defined or input['start_time'] is defined or input['end_time'] is defined or input['user_name'] is defined%}
                                {% if data['list'].current == 1 %}
                                    <li class="disabled"><span>第一页</span></li>
                                {% else %}
                                    <li><a href="/admin/status/userStatus?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&section_id={{ input['section_id'] is defined ? input['section_id'] : '' }}&department_id={{ input['department_id'] is defined ? input['department_id'] : '' }}&status_id={{ input['status_id'] is defined ? input['status_id'] : '' }}&start_time={{ input['start_time'] is defined ? input['start_time'] : '' }}&end_time={{ input['end_time'] is defined ? input['end_time'] : '' }}&user_name={{ input['user_name'] is defined ? input['user_name'] : '' }}&page=1">第一页</a></li>
                                {% endif %}
                                {% if data['list'].current == 1 %}
                                    <li class="disabled"><span>上一页</span></li>
                                {% else %}
                                    <li><a href="/admin/status/userStatus?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&section_id={{ input['section_id'] is defined ? input['section_id'] : '' }}&department_id={{ input['department_id'] is defined ? input['department_id'] : '' }}&status_id={{ input['status_id'] is defined ? input['status_id'] : '' }}&start_time={{ input['start_time'] is defined ? input['start_time'] : '' }}&end_time={{ input['end_time'] is defined ? input['end_time'] : '' }}&user_name={{ input['user_name'] is defined ? input['user_name'] : '' }}&page={{ data['list'].before }}">上一页</a></li>
                                {% endif %}
                                {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                    <li class="disabled"><span>下一页</span></li>
                                {% else %}
                                    <li><a href="/admin/status/userStatus?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&section_id={{ input['section_id'] is defined ? input['section_id'] : '' }}&department_id={{ input['department_id'] is defined ? input['department_id'] : '' }}&status_id={{ input['status_id'] is defined ? input['status_id'] : '' }}&start_time={{ input['start_time'] is defined ? input['start_time'] : '' }}&end_time={{ input['end_time'] is defined ? input['end_time'] : '' }}&user_name={{ input['user_name'] is defined ? input['user_name'] : '' }}&page={{ data['list'].next }}">下一页</a></li>
                                {% endif %}
                                {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                    <li class="disabled"><span>最后一页</span></li>
                                {% else %}
                                    <li><a href="/admin/status/userStatus?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&section_id={{ input['section_id'] is defined ? input['section_id'] : '' }}&department_id={{ input['department_id'] is defined ? input['department_id'] : '' }}&status_id={{ input['status_id'] is defined ? input['status_id'] : '' }}&start_time={{ input['start_time'] is defined ? input['start_time'] : '' }}&end_time={{ input['end_time'] is defined ? input['end_time'] : '' }}&user_name={{ input['user_name'] is defined ? input['user_name'] : '' }}&page={{ data['list'].last }}">最后一页</a></li>
                                {% endif %}
                            {% else %}
                                {% if data['list'].current == 1 %}
                                    <li class="disabled"><span>第一页</span></li>
                                {% else %}
                                    <li><a href="/admin/status/userStatus">第一页</a></li>
                                {% endif %}
                                {% if data['list'].current == 1 %}
                                    <li class="disabled"><span>上一页</span></li>
                                {% else %}
                                    <li><a href="/admin/status/userStatus?page={{ data['list'].before }}">上一页</a></li>
                                {% endif %}
                                {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                    <li class="disabled"><span>下一页</span></li>
                                {% else %}
                                    <li><a href="/admin/status/userStatus?page={{ data['list'].next }}">下一页</a></li>
                                {% endif %}
                                {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                    <li class="disabled"><span>最后一页</span></li>
                                {% else %}
                                    <li><a href="/admin/status/userStatus?page={{ data['list'].last }}">最后一页</a></li>
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
            location.href = "/admin/status/userStatus?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&section_id={{ input['section_id'] is defined ? input['section_id'] : '' }}&department_id={{ input['department_id'] is defined ? input['department_id'] : '' }}&status_id={{ input['status_id'] is defined ? input['status_id'] : '' }}&start_time={{ input['start_time'] is defined ? input['start_time'] : '' }}&end_time={{ input['end_time'] is defined ? input['end_time'] : '' }}&user_name={{ input['user_name'] is defined ? input['user_name'] : '' }}&page=" + page;
        }

        $(function () {
            $('.form_datetime').datetimepicker({
                todayBtn:  1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                forceParse: 0,
                showMeridian: 1,
                language:  'zh-CN',
            });
        });

        //删除事件
        function del(status_id) {
            layer.confirm('您确定要删除这个事件吗？', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.ajax({
                    url: "{{url('admin/status/')}}/" + status_id,
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                         '_method': 'delete',
                        "{{ _csrfKey }}": "{{ _csrf }}",
                    },
                    success: function(data){
                         if (data.state == 0) {
                            location.reload();
                            layer.msg(data.msg, {icon: 6});
                        } else {
                            layer.msg(data.msg, {icon: 5});
                        }
                    },
                    error: function() {
                        layer.msg('操作失败，请稍后重试！', {icon: 2});
                    }
                });
            });
        }

        function get_option_list_by_project(obj){
            var project_id = $(obj).val();
            $('.status_list').html('<option value=""> 请先选择单位</option>');
            $('.department_list').html('<option value=""> 请先选择单位</option>');
            $('.section_list').html('<option value=""> 请先选择单位</option>');
            if(project_id == ''){
                return false;
            }
            $.ajax({
                url: '{{url('admin/status/ajaxGetDepartmentAndStatusListByProject')}}',
                type: 'POST',
                dataType: 'JSON',
                data: {
                    "{{ _csrfKey }}": "{{ _csrf }}",
                    'project_id':project_id,
                },
                success: function(data){
                    if(data.state == 0){
                        var department_list = status_list  = section_list = '<option value="">请选择</option>';
                        //部门列表
                        $.each(data.list.department_list,function(k,v){
                            department_list += '<option value="'+v.department_id+'"> '+v.department_name+'</option>';
                        });
                        $.each(data.list.status_list,function(k,v){
                            status_list += '<option value="'+v.status_id+'"> '+v.status_name+'</option>';
                        });
                        $.each(data.list.section_list,function(k,v){
                            section_list += '<option value="'+v.section_id+'"> '+v.section_name+'</option>';
                        });
                        $('.section_list').html(section_list);
                        $('.department_list').html(department_list);
                        $('.status_list').html(status_list);
                    }
                },
                error: function() {
                    layer.msg('操作失败，请稍后重试！', {icon: 2});
                }
            })
        }

        function change_status(user_id,user_status_id, status_id, project_id, project_name, departmanet_name,start_time,end_time,desc,user_name){
            $.ajax({
                url: '{{ url('admin/status/ajaxGetStatusOptionByUser') }}',
                type: "POST",
                dataType: 'JSON',
                data: {
                    "{{ _csrfKey }}": "{{ _csrf }}",
                    'project_id': project_id,
                },
                success: function(data){
                    if(data.status == 200){
                        var status_option = '';
                        {#var _token = "{{csrf_token()}}";#}
                        $.each(data.msg,function(k,v){
                            status_option += '<option value="'+v.status_id+'" '+((status_id==v.status_id) ? 'selected' : '')+'><span style="display: inline-block;width: 10px;height: 10px;border-radius: 100%;background: '+v.status_color+'"></span>'+ v.status_name+'</option>';
                        });
                        var content = '<div><form action="" method="" id="status_form">'+
                            '<input type="hidden" name="user_status_id"  value="'+user_status_id+'">'+
                            '<input type="hidden" name="user_id"  value="'+user_id+'">'+
                            // '<input type="hidden" name="_token" value="'+_token+'">'+
                            '<table class="add_tab">'+
                            '<tbody>'+
                            '<tr>'+
                            '<th width="150"><i class="require">*</i>事件名称：</th>'+
                            '<td>'+
                            '<select name="status_id" class="status_id">'+
                            '<option value="">==请选择==</option>'+
                            status_option+
                            '</select>'+
                            '<span><i class="fa fa-exclamation-circle yellow"></i>必须选择一个事件</span>'+
                            '</td>'+
                            '</tr>'+
                            '<tr>'+
                            '<th><i class="require">*</i>开始时间：</th>'+
                            '<td>'+
                            '<input type="text" name="start_time" class="form_datetime" value="'+start_time+'">'+
                            '<span><i class="fa fa-exclamation-circle yellow"></i>事件开始时间必须填写</span>'+
                            '</td>'+
                            '</tr>'+
                            '<tr>'+
                            '<th><i class="require">*</i>结束时间：</th>'+
                            '<td>'+
                            '<input type="text" name="end_time" class="form_datetime" value="'+end_time+'">'+
                            '<span><i class="fa fa-exclamation-circle yellow"></i>事件结束时间必须填写</span>'+
                            '</td>'+
                            '</tr>'+
                            '<tr>'+
                            '<th>说明：</th>'+
                            '<td>'+
                            '<textarea type="text" name="user_status_desc" >'+desc+'</textarea>'+
                            '</td>'+
                            '</tr>'+
                            '</table></form></div>';
                        layer.open({
                            title:project_name+'&raquo;'+departmanet_name+'&raquo;'+user_name,
                            type: 1,
                            btn:['确定','取消'],
                            area : ['850px','auto'],
                            skin: 'layui-layer-rim', //加上边框
                            content: content,
                            success : function(){
                                $('.form_datetime').datetimepicker({
                                    todayBtn:  1,
                                    autoclose: 1,
                                    todayHighlight: 1,
                                    startView: 2,
                                    forceParse: 0,
                                    showMeridian: 1,
                                    language:  'zh-CN',
                                });
                            },
                            yes:function(){
                                if($('#status_form .status_id').val() ==''){
                                    layer.msg('请选择一个事件！');return false;
                                }
                                if($("#status_form input[name='start_time']").val()==''){
                                    layer.msg('请选择开始时间！');return false;
                                }
                                if($("#status_form input[name='start_time']").val()==''){
                                    layer.msg('请选择结束时间！');return false;
                                }
                                var start_time = Date.parse($("#status_form input[name='start_time']").val());
                                var end_time = Date.parse($("#status_form input[name='end_time']").val());
                                if(start_time>end_time){
                                    layer.msg('开始时间不能大于结束时间');return false;
                                }
                                var data = $("#status_form").serialize();
                                $.ajax({
                                    url: '{{ url('admin/status/changeStatus') }}',
                                    type: 'POST',
                                    dataType: 'JSON',
                                    async: false,
                                    data: data,
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
                },
                error: function() {
                    layer.msg('操作失败，请稍后重试！', {icon: 2});
                }
            })
        }

        //删除事件
        function del_status(user_status_id) {
            layer.confirm('您确定要删除此事件安排吗？', {
                btn: ['确定','取消']
            }, function(){
                $.ajax({
                    url: "{{ url('admin/status/delUserStatus') }}",
                    type: "POST",
                    dataType: 'JSON',
                    data: {
                        "{{ _csrfKey }}": "{{ _csrf }}",
                        'user_status_id': user_status_id,
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