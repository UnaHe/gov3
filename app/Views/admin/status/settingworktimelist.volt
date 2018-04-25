{% extends "layout/main.volt" %} {% block content %}
        <!--面包屑导航 开始-->
<div class="crumb_warp">
    <!--<i class="fa fa-bell"></i> 欢迎使用登陆网站后台，建站的首选工具。-->
    <i class="fa fa-home"></i> <a href="{{url('admin/home')}}">首页</a> &raquo;设置单位上下班时间
</div>
<!--面包屑导航 结束-->

<!--搜索结果页面 列表 开始-->
<form action="#" method="post">
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
                {% for v in project_list %}
                <tr class="tr_{{v.project_id}}">
                    {{csrf_field()}}
                    <td class="tc">{{v.project_id}}</td>
                    <td>
                        <a href="#">{{v.project_name}}</a>
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
                <label>共 {{project_list.total()}} 条记录</label>
                <div style="float: right">
                    {{project_list.links()}}</div>
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
    table.list_tab tr td input[type='text'] {
        width: 150px;
        text-align: center;
        display: inline;
    }
</style>



<link rel="stylesheet" href="{{asset('org/TimePicker/css/timePicker.css')}}">
<script type="text/javascript" src="{{asset('org/TimePicker/js/timepicker.js')}}"></script>

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
        })
        if(!flag){
            return false;
        }
        var work_start_time = $("#"+project_id+"_work_start_time").val();
        var work_end_time = $("#"+project_id+"_work_end_time").val();
        if(work_start_time>work_end_time && work_end_time!='00：00'){
            layer.msg('上班时间不应大于下班时间');
            return false;
        }
        var data = {
            '_token': "{{csrf_token()}}",
            'work_start_time':work_start_time,
            'work_end_time':work_end_time
        }
        $.ajax({
            url: "settingworktime/"+project_id,
            data: data,
            type: "POST",
            dataType: "json",
            success:function(data){
                layer.msg(data.msg);
                if(data.state){
                    location.reload();
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
</script>

{% endblock %}
