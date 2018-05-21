{% extends "layout/main.volt" %}

{% block content %}
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
        <!--面包屑导航 开始-->
    {#<link rel="stylesheet" href="{{asset('admin/org/bigcolorpicker/css/jquery.bigcolorpicker.css')}}">#}
    <div class="crumb_warp">
        <!--<i class="fa fa-bell"></i> 欢迎使用登陆网站后台，建站的首选工具。-->
        <i class="fa fa-home"></i> <a href="{{url('admin/home')}}">首页</a> &raquo; 事件管理
    </div>
    <!--面包屑导航 结束-->

    <!--结果集标题与导航组件 开始-->
    <div class="result_wrap">
        <div class="result_title">
            <h3>编辑事件</h3>
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
        <form action="{{ url('admin/status/update') }}" method="post" id="add-form" name="add-form">
            <input type="hidden" name="_method" value="put">
            <input type="hidden" name="{{ _csrfKey }}" value="{{ _csrf }}"/>
            <input type="hidden" name="status_id" value="{{ status.status_id }}"/>
            <table class="add_tab">
                <tbody>
                <?php if(is_object($project)) { ?>
                    {% Include 'layout/common_tr' with ['type': -1,'project_id': status.project_id] %}
                <?php } else { ?>
                    <tr>
                        <th width="150"><i class="require">*</i>归属单位：</th>
                        <td>
                            <input type="hidden" name="project_id" class="project_id" value="{{ _session['project_id'] }}">
                            <span><i class=""></i>{{ project }}</span>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <th><i class="require">*</i>事件名称：</th>
                    <td>
                        <input type="text" name="status_name" onchange="valid_name()" value="{{ status.status_name }}" required/>
                    </td>
                </tr>
                <tr>
                    <th><i class="require">*</i>事件颜色：</th>
                    <td>
                        <input type="hidden"  name="status_color" value="{{ status.status_color }}">
                        <span class="status_color"  href="javasript:;" data-value="#1F73C2" data-name="blue" style="background: #1F73C2;">
                            {% if status.status_color == '#1F73C2' %}
                                <img class="sele_lo" src="{{ url('admin/style/img/select.png') }}" />
                            {% endif %}
                        </span>
                        <span class="status_color"  href="javasript:;" data-value="#FF952D" data-name="yellow" style="background: #FF952D;">
                            {% if status.status_color == '#FF952D' %}
                                <img class="sele_lo" src="{{ url('admin/style/img/select.png') }}" />
                            {% endif %}
                        </span>
                        <span class="status_color"  href="javasript:;" data-value="#33be17" data-name="green" style="background: #33be17;">
                            {% if status.status_color == '#33be17' %}
                                <img class="sele_lo" src="{{ url('admin/style/img/select.png') }}" />

                            {% endif %}
                        </span>

                        <span class="status_color"  href="javasript:;" data-value="#df4a4a" data-name="red" style="background: #df4a4a;">
                            {% if status.status_color == '#df4a4a' %}
                                <img class="sele_lo" src="{{ url('admin/style/img/select.png') }}" />
                            {% endif %}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th><i class="require">*</i>设为默认：</th>
                    <td>
                        <input type="radio"  name="status_is_default" value="1" {{ status.status_is_default == 1 ? 'checked' : '' }}>上班默认事件&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="radio"  name="status_is_default" value="2" {{ status.status_is_default == 2 ? 'checked' : '' }}>下班默认事件&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="radio"  name="status_is_default" value="0" {{ status.status_is_default == 0 ? 'checked' : '' }}>否&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span><i class="fa fa-exclamation-circle yellow"></i>上班默认事件是指在上班时间内默认的事件</span>
                    </td>
                </tr>
                <tr>
                    <th><i class="require">*</i>排序：</th>
                    <td>
                        <input type="text" class="sm" name="status_order" value="{{ status.status_order }}">
                    </td>
                </tr>
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
    {#<script type="text/javascript" src="{{asset('admin/org/bigcolorpicker/js/jquery.bigcolorpicker.js')}}"></script>#}
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

            if('{{ status.status_color }}' == ''){
                $('.status_color').eq(0).append('<img class="sele_lo" src="{{ url('admin/style/img/select.png') }}" />')
            }
            $('.status_color').click(function(){
                $("input[name='status_color']").val($(this).data('value'));
                $('.status_color').empty('img.sele_lo');
                $(this).append('<img class="sele_lo" src="{{ url('admin/style/img/select.png') }}" />');
            })
        });

        function valid_name(){
            var flag = false;
            var project_id = $('.project_id') .val();
            var status_name = $("input[name='status_name']").val();
            var old_status_name = '{{ status.status_name is not empty ? status.status_name : '' }}';
            if(status_name == old_status_name){
                return true;
            }

            if(project_id==''){
    //                    layer.msg('请选择一个单位');
                return false;
            }
            if(status_name==''){
    //                    layer.msg('请填写事件名');
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
