{% extends "layout/main.volt" %}

{% block content %}

    <!--面包屑导航 开始-->
    <div class="crumb_warp">
        <!--<i class="fa fa-bell"></i> 欢迎使用登陆网站后台，建站的首选工具。-->
        <i class="fa fa-home"></i> <a href="{{url('admin/home')}}">首页</a> &raquo; 科室管理
    </div>
    <!--面包屑导航 结束-->
    <!--结果集标题与导航组件 开始-->
    <div class="result_wrap">
        <div class="result_title">
            <h3>添加科室</h3>
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
    <!--结果集标题与导航组件 结束-->
    <div class="result_wrap">
        <form action="{{url('admin/department')}}" method="post" name="add-form" id="add-form">
            <input type="hidden" name="{{ _csrfKey }}" value="{{ _csrf }}"/>
            <table class="add_tab">
                <tbody>
                    {#{% if project is defined %}#}
                    <?php if(!is_object($project)) { ?>
                    <tr>
                        <th width="150"><i class="require">*</i>单位：</th>
                        <td>
                            <input type="hidden" name="project_id" value="{{ project['project_id'] }}">
                            <span><i class=""></i>{{ project['project_name'] }}</span>
                        </td>
                    </tr>
                    <?php } ?>
                    {#{% endif %} #}
                    {% Include 'layout/common_tr' with ['type': 0, 'cate_list_name':'父级科室','cate_name':'parent_id'] %}
                    <tr>
                        <th><i class="require">*</i> 科室名称：</th>
                        <td>
                            <input type="text" class="lg" name="department_name" required>
                        </td>
                    </tr>

                    {#
                    <tr>#} {#
                        <th>描述：</th>#} {#
                        <td>#} {#
                            <textarea name="department_desc" value="" width="600px" height="500px;"></textarea>#} {#
                        </td>#} {#
                    </tr>#}
                    <tr>
                        <th>科室介绍：</th>
                        <td>
                            {{ javascript_include('admin/org/ueditor/ueditor.config.js') }}
                            {{ javascript_include('admin/org/ueditor/ueditor.all.min.js') }}
                            {{ javascript_include('admin/org/ueditor/lang/zh-cn/zh-cn.js') }}
                            <script id="editor" name="department_desc" type="text/plain" style="width: 600px;height: 380px;"></script>
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
        $(function () {
            $("#add-form").validate({
                submitHandler: function (form) {
                    form.submit();
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
        })
    </script>

{% endblock %}