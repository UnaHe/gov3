{% extends "layout/main.volt" %}

{% block content %}

    <!--面包屑导航 开始-->
    <style>
        .sele_lo{
            position: absolute;
            width: 15px;
            left: .1rem;
            top: 0.2rem;
        }
        .status_color{
            display: inline-block;
            width: 20px;
            height: 20px;
            border:1px solid;
            position: relative
        }

    </style>
    {#<link rel="stylesheet" href="{{asset('admin/org/bigcolorpicker/css/jquery.bigcolorpicker.css')}}">#}
    <div class="crumb_warp">
        <!--<i class="fa fa-bell"></i> 欢迎使用登陆网站后台，建站的首选工具。-->
        <i class="fa fa-home"></i> <a href="{{url('admin/home')}}">首页</a> &raquo; 事件管理
    </div>
    <!--面包屑导航 结束-->

    <!--结果集标题与导航组件 开始-->
    <div class="result_wrap">
        <div class="result_title">
            <h3>添加事件</h3>
            {{ content() }}
            <p><?php $this->flashSession->output() ?></p>
        </div>
        <div class="result_content">
            <div class="short_wrap">
                <a href="{{url('admin/status/create')}}"><i class="fa fa-plus"></i>添加事件</a>
                <a href="{{url('admin/status')}}"><i class="fa fa-recycle"></i>全部事件</a>
            </div>
        </div>
    </div>
    <!--结果集标题与导航组件 结束-->

    <div class="result_wrap">
        <form action="{{url('admin/status')}}" method="post" id="add-form" name="add-form">
            <input type="hidden" name="{{ _csrfKey }}" value="{{ _csrf }}"/>
            <table class="add_tab">
                <tbody>
                {% if project is not scalar %}
                {% else %}
                    <tr>
                        <th width="150"><i class="require">*</i>归属单位：</th>
                        <td>
                            <input type="hidden" name="project_id" class="project_id" value="{{ _session['project_id'] }}">
                            <span><i class=""></i>{{project}}</span>
                        </td>
                    </tr>
                {% endif %}
                {% Include 'layout/common_tr' with ['type': -1] %}
                <tr>
                    <th><i class="require">*</i>事件名称：</th>
                    <td>
                        <input type="text" name="status_name" onchange="valid_name()" required/>
                    </td>
                </tr>
                <tr>
                    <th><i class="require">*</i>事件颜色：</th>
                    <td>
                        <input type="hidden"  name="status_color" value="#1F73C2">
                        <span class="status_color"  href="javasript:;" data-value="#1F73C2" style="background: #1F73C2;"></span>
                        <span class="status_color"  href="javasript:;" data-value="#FF952D" style="background: #FF952D;"></span>
                        <span class="status_color"  href="javasript:;" data-value="#33be17" style="background: #33be17;"></span>
                        <span class="status_color"  href="javasript:;" data-value="#df4a4a" style="background: #df4a4a;"></span>
                        {#<span><i class="fa fa-exclamation-circle yellow"></i>点击可修改事件颜色</span>#}
                    </td>
                </tr>
                <tr>
                    <th><i class="require">*</i>设为默认：</th>
                    <td>
                        <input type="radio"  name="status_is_default" value="1">上班默认事件&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="radio"  name="status_is_default" value="2" >下班默认事件&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="radio"  name="status_is_default" value="0" checked>否&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span><i class="fa fa-exclamation-circle yellow"></i>上班默认事件是指在上班时间内默认的事件</span>
                    </td>
                </tr>
                <tr>
                    <th>排序：

                    <td>
                        <input type="text" class="sm" name="status_order">
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <input type="submit" id="" class="btn btn-info" value="提交">
                        <input type="button" class="back btn btn-info" onclick="history.go(-1)" value="返回">
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
    <script>
        $(function(){
            $("#add-form").validate({
                submitHandler: function(form){
                    if(valid_name()){
                        form.submit();
                    }
                },
                errorElement: "span",
                ignore: ".hide",
                rules: {
                    project_id: {
                        required:true,
                        min: 1
                    }
                },
                messages: {
                    project_id: {
                        required: "请选择单位",
                        min: "请选择单位"
                    }
                }
            });

//            $("#submit_btn").click(function(){
//                if(valid_name()){
//                    $('#status_form').submit();
//                }
//            })

            $('.status_color').eq(0).append('<img class="sele_lo" src="{{url('admin/style/img/select.png')}}" />')
            $('.status_color').click(function(){
                $("input[name='status_color']").val($(this).data('value'));
                $('.status_color').empty('img.sele_lo');
                $(this).append('<img class="sele_lo" src="{{url('admin/style/img/select.png')}}" />');
            })
        });

        function valid_name(){
            var flag = false;
            var project_id = $('.project_id') .val();
            var status_name = $("input[name='status_name']").val();
            if(project_id==''){
//                layer.msg('请选择一个单位');
                return false;
            }
            if(status_name==''){
//                layer.msg('请填写事件名');
                return false;
            }

            $.ajax({
                url: '{{ url('admin/status/validName') }}',
                type: "POST",
                dataType: 'JSON',
                async: false,
                data: {
                    "{{ _csrfKey }}": "{{ _csrf }}",
                    'project_id': project_id,
                    'status_name': status_name
                },
                success: function(data){
                    if(data.status == 200){
                        flag = true;
                    }else{
                        layer.msg(data.msg, {icon: 5});
                        $("input[name='status_name']").focus();
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