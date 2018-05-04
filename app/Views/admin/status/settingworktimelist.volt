{% extends "layout/main.volt" %}

{% block content %}
    <!--面包屑导航 开始-->
    <div class="crumb_warp">
        <!--<i class="fa fa-bell"></i> 欢迎使用登陆网站后台，建站的首选工具。-->
        <i class="fa fa-home"></i> <a href="{{url('admin/home')}}">首页</a> &raquo;设置单位上下班时间
    </div>
    <!--面包屑导航 结束-->

    <div class="result_wrap">
        <!--快捷导航 开始-->
        <div class="result_title">
            <h3>设置单位上下班时间</h3>
            <div style="background-color: #e5e9ec;">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1.单位的上下班时间决定在某时刻查看员工状态时显示的上下班状态；<br/>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2.系统默认单位的上班时间为9:00，下班时间为18:00。
            </div>
        </div>
        <!--快捷导航 结束-->
    </div>

    <div class="result_wrap">
        <div class="result_content">
            <table class="list_tab">
                <tr>
                    <th class="tc">单位ID</th>
                    <th>单位名称</th>
                    <th>上班时间</th>
                    <th>下班时间</th>
                    <th>操作</th>
                </tr>
                {% for v in project_list.items %}
                <tr class="tr_{{v.project_id}}">
                    <td class="tc">{{v.project_id}}</td>
                    <td>
                        <a href="#"><?php echo mb_substr($v->project_name,0,30,'utf-8'); ?></a>
                    </td>
                    <td><input class="hunterTimePicker" type="text" value="{{v.work_start_time}}" id="{{v.project_id}}_work_start_time"></td>
                    <td><input class="hunterTimePicker" type="text" value="{{v.work_end_time}}" id="{{v.project_id}}_work_end_time"></td>
                    <td>
                        <a href="#" onclick="changetime({{v.project_id}})">提交</a>
                    </td>
                </tr>
                {% endfor %}
            </table>

            <div class="page_list clear" >
                <label>共 {{ project_list.total_items }} 条记录</label>
                {% if project_list.total_pages > 1 %}
                    <div style="float: right">
                        <ul class="paginate">
                            <li class="disabled"><span>总计: {{ project_list.total_pages }} 页</span></li>
                            <li class="active"><span>当前第: {{ project_list.current }} 页</span></li>
                            {% if project_list.current == 1 %}
                                <li class="disabled"><span>第一页</span></li>
                            {% else %}
                                <li><a href="/admin/status/settingworktimelist">第一页</a></li>
                            {% endif %}
                            {% if project_list.current == 1 %}
                                <li class="disabled"><span>上一页</span></li>
                            {% else %}
                                <li><a href="/admin/status/settingworktimelist?page={{ project_list.before }}">上一页</a></li>
                            {% endif %}
                            {% if project_list.current == project_list.last or project_list.last == 0 %}
                                <li class="disabled"><span>下一页</span></li>
                            {% else %}
                                <li><a href="/admin/status/settingworktimelist?page={{ project_list.next }}">下一页</a></li>
                            {% endif %}
                            {% if project_list.current == project_list.last or project_list.last == 0 %}
                                <li class="disabled"><span>最后一页</span></li>
                            {% else %}
                                <li><a href="/admin/status/settingworktimelist?page={{ project_list.last }}">最后一页</a></li>
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
        table.list_tab tr td input[type='text'] {
            width: 150px;
            text-align: center;
            display: inline;
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

    {{ stylesheet_link('org/TimePicker/css/timePicker.css') }}
    {{ javascript_include('org/TimePicker/js/timepicker.js') }}

    <script>
        $(function(){
            $(".hunterTimePicker").hunterTimePicker();
        });
        function changetime(project_id) {
            var i = 2;
            var flag = false;
            $.each($(".tr_"+project_id).find(".hunterTimePicker"),function(k,v){
                if($(this).val()==''){
                    layer.msg($('.list_tab th').eq(i).text()+'不能为空');
                    return false;
                }
                i++;
                if(k==1){
                    flag = true;
                }
            });
            if(!flag){
                return false;
            }
            var work_start_time = $("#"+project_id+"_work_start_time").val();
            var work_end_time = $("#"+project_id+"_work_end_time").val();
            if(work_start_time>work_end_time && work_end_time!='00：00'){
                layer.msg('上班时间不应大于下班时间');
                return false;
            }
            if(work_end_time<=work_start_time){
                layer.msg('下班时间应该大于大于时间');
                return false;
            }
            var data = {
                "{{ _csrfKey }}": "{{ _csrf }}",
                'work_start_time': work_start_time,
                'work_end_time': work_end_time,
                'project_id': project_id
            };
            $.ajax({
                url: '{{ url('admin/status/settingWorkTime') }}',
                data: data,
                type: "POST",
                dataType: 'JSON',
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
                error: function() {
                    layer.msg('操作失败，请稍后重试！', {icon: 2});
                }
            })

        }
    </script>

{% endblock %}
