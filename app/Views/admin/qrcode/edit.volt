{% extends "layout/main.volt" %}
{% block content %}

    <style>
        .sele_lo{
            position: absolute;
            width: 15px;
            left: .1rem;
            top: 0.2rem;
        }
    </style>
        <!--面包屑导航 开始-->
    {#<link rel="stylesheet" href="{{asset('admin/org/bigcolorpicker/css/jquery.bigcolorpicker.css')}}">#}
    <div class="crumb_warp">
        <!--<i class="fa fa-bell"></i> 欢迎使用登陆网站后台，建站的首选工具。-->
        <i class="fa fa-home"></i> <a href="{{url('admin/home')}}">首页</a> &raquo; 科室二维码绑定
    </div>
    <!--面包屑导航 结束-->

    <!--结果集标题与导航组件 开始-->
    <div class="result_wrap">
        <div class="result_title">
            <h3>科室二维码绑定</h3>
            {{ content() }}
            <p><?php $this->flashSession->output() ?></p>
        </div>
        <div class="result_content">
            <div class="short_wrap">
                <a href="{{url('/admin/qrcode/0/edit')}}"><i class="fa fa-plus"></i>绑定二维码</a>
                <a href="{{url('/admin/qrcode')}}"><i class="fa fa-recycle"></i>二维码列表</a>
            </div>
        </div>
    </div>
    <!--结果集标题与导航组件 结束-->

    <div class="result_wrap">
        <form action="{{ url('admin/qrcode/update') }}" method="POST" id="edit_form">
            <input type="hidden" name="_method" value="put">
            <input type="hidden" name="{{ _csrfKey }}" value="{{ _csrf }}"/>
            <input type="hidden" name="id" value="{{ id }}"/>
            <table class="add_tab">
                <tbody>
                <tr>
                    <?php if(!is_object($project)) { ?>
                        <input type="hidden" name="project_id" class="project_id" value="{{ _session['project_id'] }}">
                        <span><i class=""></i>{{_session['project_name']}}</span>
                    <?php } ?>
                    {% Include 'layout/common_tr' with ['type': 0, 'project_id':forward is not empty and forward.project_id ? forward.project_id : 0 , 'department_id' :forward is not empty and  forward.department_id ? forward.department_id : 0 ] %}
                <tr>
                    <th><i class="require">*</i>二维码编号：</th>
                    <td>
                        <input type="number" name="forward_id" onchange="valid_id()" value="{{forward.forward_id is not empty ? forward.forward_id : ''}}" style="height: 30px" required   min="1"/>
                        <span for="forward_id" class="error"></span>
                    </td>
                </tr>
                <tr>
                    <th>二维码说明：</th>
                    <td>
                        <input type="text" name="forward_introduction" onchange="" value="{{forward.forward_introduction is not empty ? forward.forward_introduction : ''}}" style="width: 70% !important;">
                    </td>
                </tr>
                {#<tr>#}
                    {#<th>更多参数：</th>#}
                    {#<td>#}
                        {#<input type="text" name="forward_query" onchange="" value="{{$forward['forward_query'] or ''}}" style="width: 70% !important;">#}
                        {#<span><i class="fa fa-exclamation-circle yellow"></i>冒号赋值，逗号分隔</span>#}
                    {#</td>#}
                {#</tr>#}
                <tr>
                    <th></th>
                    <td>
                        <input type="submit" id="submit_btn" class="btn btn-info" value="提交">
                        <input type="button" class="back btn btn-info" onclick="history.go(-1)" value="返回">
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
    <script>
        $(function(){
            $("#edit_form").validate({
                submitHandler: function(form){
                    if(valid_id()){
                        form.submit();
                    }
                },
                errorElement: "span",
                ignore: ".hide",
                rules: {
                    project_id: {
                        min: 1
                    },
                    department_id: {
                        required: true,
                        min: 1
                    },
                    forward_id: {
                        required: true,
                        min: 1
                    }
                },
                messages: {
                    project_id: {
                        required: "<i class='fa fa-exclamation-circle yellow'></i>请选择单位",
                        min: "<i class='fa fa-exclamation-circle yellow'></i>请选择单位"
                    },
                    department_id: {
                        required: "<i class='fa fa-exclamation-circle yellow'></i>请选择科室",
                        min:"<i class='fa fa-exclamation-circle yellow'></i>请选择科室"
                    }
                }
            });
        });
        function valid_id(){
            var flag = false;
            var project_id = $('.project_id') .val();
            var department_id = $("#department_id").val();
            var forward_id = $("input[name='forward_id']").val();
            var old_forward = '{{ forward.forward_id is not empty ? forward.forward_id : 0 }}';

            if(old_forward == forward_id){
                return true;
            }
            var valid_data = [];
            $.ajax({
                url: '{{ url('admin/qrcode/valid') }}',
                type: 'POST',
                dataType: 'JSON',
                async: false,
                data: {
                    "{{ _csrfKey }}": "{{ _csrf }}",
                    'old_id': old_forward,
                    'forward_id': forward_id,
                    'project_id': project_id,
                    'department_id': department_id
                },
                success: function(data){
                    valid_data = data;
                    if(data.status == 200){
                        flag = true;
                    }else if(data.status == 405){
                        layer.msg(data.msg);
                        flag = true
                    }else{
                        layer.msg(data.msg, {icon: 5});
                    }
                },
                error: function() {
                    layer.msg('操作失败，请稍后重试！', {icon: 2});
                }
            });
            return flag;
        }
    </script>

{% endblock %}