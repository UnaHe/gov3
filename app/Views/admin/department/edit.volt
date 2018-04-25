{% extends "layout/main.volt" %} {% block content %}
    <!--面包屑导航 开始-->
    <div class="crumb_warp">
        <i class="fa fa-home"></i> <a href="{{url('admin/home')}}">首页</a> &raquo; 科室管理
    </div>
    <!--面包屑导航 结束-->
    <!--结果集标题与导航组件 开始-->
    {% if _session['user_is_super'] or (_session['user_is_admin'] and _session['project_id'] == '') %}
    <div class="result_wrap">
        <div class="result_title">
            <h3>编辑</h3>
            {{ content() }}
            <p><?php $this->flashSession->output() ?></p>
        </div>
        <div class="result_content">
            <div class="short_wrap">
                <a href="{{url('admin/department/create')}}"><i class="fa fa-plus"></i>添加科室</a>
                <a href="{{url('admin/department')}}"><i class="fa fa-recycle"></i>全部科室</a>
            </div>
        </div>
    </div>
    {% endif %}
    <!--结果集标题与导航组件 结束-->
    <div class="result_wrap">
        <form action="{{url('admin/department/update')}}" method="POST" id="add-form" name="add-form">
            <input type="hidden" name="_method" value="put">
            <input type="hidden" name="{{ _csrfKey }}" value="{{ _csrf }}"/>
            <input type="hidden" name="department_id" value="{{unit.department_id}}">
            <input type="hidden" name="project_id" value="{{unit.project_id}}">
            <table class="add_tab">
                <tbody>
                    <tr>
                        <th width="120">父级科室：</th>
                        <td>
                            <select name="parent_id" class="multiselect">
                                <option value="0">==顶级科室==</option>
                                {% for v in data %}
                                <option value="{{v.department_id}}" {{ v.department_id == unit.parent_id ? 'selected' : '' }}>{{ v.department_name }}</option>
                                {% endfor %}
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><i class="require">*</i>科室名称：</th>
                        <td>
                            <input type="text" name="department_name" value="{{unit.department_name}}" required/>
                            <span><i class="fa fa-exclamation-circle yellow"></i>科室名称必须填写</span>
                        </td>
                    </tr>
                    {#
                    <tr>#} {#
                        <th>简介：</th>#} {#
                        <td>#} {#
                            <textarea name="department_desc">{{$field->department_desc}}</textarea>#} {#
                        </td>#} {#
                    </tr>#}
                    <tr>
                        <th>科室介绍：</th>
                        <td>
                            {{ javascript_include('admin/org/ueditor/ueditor.config.js') }}
                            {{ javascript_include('admin/org/ueditor/ueditor.all.min.js') }}
                            {{ javascript_include('admin/org/ueditor/lang/zh-cn/zh-cn.js') }}
                            <script id="editor" name="department_desc" type="text/plain" style="width: 600px;height: 380px;">
                                {{ unit.department_desc }}
                            </script>
                            <script type="text/javascript">
                                var ue = UE.getEditor('editor', ueditor_toolbars);
                            </script>
                            <style>
                                .edui-default {
                                    line-height: 28px;
                                }

                                div.edui-combox-body,
                                div.edui-button-body,
                                div.edui-splitbutton-body {
                                    overflow: hidden;
                                    height: 20px;
                                }

                                div.edui-box {
                                    overflow: hidden;
                                    height: 22px;
                                }
                            </style>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" class="btn btn-info" value="提交">
                            <input type="button" class="back btn btn-info" onclick="history.go(-1)" value="返回">
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>
<script>
$(function() {
    $('.multiselect').multiselect(multiselect_option);
    $("#add-form").validate({
        submitHandler: function(form) {
            var project_id = $("input[name='user_id']").val();
            if ($("input[name='user_id']").val() == 0) {
                $("input[name='user_pass']").val($.md5($("#user_pass").val()));
            }
            form.submit();
        },
        errorElement: "span",
        ignore: ".hide",
        rules: {},
        messages: {}
    });
})
</script>
{% endblock %}