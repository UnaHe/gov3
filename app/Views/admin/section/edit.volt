{% extends "layout/main.volt" %}

{% block content %}

    <!--面包屑导航 开始-->
    <div class="crumb_warp">
        <i class="fa fa-home"></i> <a href="{{url('admin/home')}}">首页</a> &raquo; 部门管理
    </div>
    <!--面包屑导航 结束-->

    <!--结果集标题与导航组件 开始-->
    <div class="result_wrap">
        <div class="result_title">
            <h3>编辑</h3>
            {{ content() }}
            <p><?php $this->flashSession->output() ?></p>
        </div>
        <div class="result_content">
            <div class="short_wrap">
                <a href="{{url('admin/section/create')}}"><i class="fa fa-plus"></i>添加部门</a>
                <a href="{{url('admin/section')}}"><i class="fa fa-recycle"></i>全部部门</a>
            </div>
        </div>
    </div>
    <!--结果集标题与导航组件 结束-->

    <div class="result_wrap">
        <form action="{{url('admin/section/update')}}" method="POST" id="add-form" name="add-form">
            <input type="hidden" name="_method" value="put">
            <input type="hidden" name="{{ _csrfKey }}" value="{{ _csrf }}"/>
            <input type="hidden" name="project_id" value="{{section.project_id}}">
            <input type="hidden" name="section_id" value="{{section.section_id}}">
            <table class="add_tab">
                <tbody>
                <tr>
                    <th width="120"><i class="require">*</i>父级部门：</th>
                    <td>
                        <select name="parent_id" class="multiselect">
                            <option value="0">顶级部门</option>
                            {% for v in data %}
                                <option value="{{v.section_id}}" {{ v.section_id == section.parent_id ? 'selected' : '' }}>{{ v.section_name }}</option>
                            {% endfor %}
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><i class="require">*</i>部门名称：</th>
                    <td>
                        <input type="text" name="section_name" value="{{section.section_name}}" required/>
                        <span><i class="fa fa-exclamation-circle yellow"></i>部门名称必须填写</span>
                    </td>
                </tr>
                {#<tr>#}
                    {#<th>部门介绍：</th>#}
                    {#<td>#}
                        {#<script type="text/javascript" charset="utf-8" src="{{asset('admin/org/ueditor/ueditor.config.js')}}"></script>#}
                        {#<script type="text/javascript" charset="utf-8" src="{{asset('admin/org/ueditor/ueditor.all.min.js')}}"> </script>#}
                        {#<script type="text/javascript" charset="utf-8" src="{{asset('admin/org/ueditor/lang/zh-cn/zh-cn.js')}}"></script>#}
                        {#<script id="editor" name="section_desc" type="text/plain" style="width:860px;height:500px;">{!! $section->section_desc !!}</script>#}
                        {#<script type="text/javascript">#}
                        {#var ue = UE.getEditor('editor',ueditor_toolbars);#}
                        {#</script>#}
                        {#<style>#}
                            {#.edui-default{line-height: 28px;}#}
                            {#div.edui-combox-body,div.edui-button-body,div.edui-splitbutton-body#}
                            {#{overflow: hidden; height:20px;}#}
                            {#div.edui-box{overflow: hidden; height:22px;}#}
                        {#</style>#}
                    {#</td>#}
                {#</tr>#}
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
            $('.multiselect').multiselect(multiselect_option);
            $("#add-form").validate({
                submitHandler: function(form){
                    form.submit();
                },
                errorElement: "span",
                ignore: ".hide",
                rules: {
                },
                messages: {
                }
            });
        })
    </script>

{% endblock %}
