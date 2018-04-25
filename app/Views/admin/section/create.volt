{% extends "layout/main.volt" %}

{% block content %}

    <div class="crumb_warp">
        <i class="fa fa-home"></i> <a href="{{url('admin/home')}}">首页</a> &raquo; 部门管理
    </div>

    <div class="result_wrap">
        <div class="result_title">
            <h3>添加部门</h3>
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
        <form action="{{ url('admin/section') }}" method="post" id="add-form" name="add-form">
            <input type="hidden" name="{{ _csrfKey }}" value="{{ _csrf }}"/>
            <table class="add_tab">
                <tbody>
                     {% Include 'layout/common_tr' with ['type':1,'section_list_name':'父级部门','section_name':'parent_id'] %}
                <tr>
                    <th><i class="require">*</i> 部门名称：</th>
                    <td>
                        <input type="text" class="lg" name="section_name" required/>
                    </td>
                </tr>
                {#<tr>#}
                    {#<th>部门介绍：</th>#}
                    {#<td>#}
                        {#<script type="text/javascript" charset="utf-8" src="{{asset('admin/org/ueditor/ueditor.config.js')}}"></script>#}
                        {#<script type="text/javascript" charset="utf-8" src="{{asset('admin/org/ueditor/ueditor.all.min.js')}}"> </script>#}
                        {#<script type="text/javascript" charset="utf-8" src="{{asset('admin/org/ueditor/lang/zh-cn/zh-cn.js')}}"></script>#}
                        {#<script id="editor" name="section_desc" type="text/plain" style="width:860px;height:300px;">#}
                        {#</script>#}
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
        $("#add-form").validate({
            submitHandler: function(form){
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
                    required: "<i class='fa fa-exclamation-circle yellow'></i>请选择单位",
                    min: "<i class='fa fa-exclamation-circle yellow'></i>请选择单位"
                }
            }
        });
    })
    </script>

{% endblock %}
