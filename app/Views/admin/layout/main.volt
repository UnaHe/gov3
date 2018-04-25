<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    {#<meta name="csrf-token" content="{{ csrf_token() }}">#}
    <title>橙视光标智慧科室牌-后台管理</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    {{ stylesheet_link('/admin/org/bootstrap/css/bootstrap.min.css') }}
    {{ stylesheet_link('/adminlte/dist/css/AdminLTE.min.css') }}
    {{ stylesheet_link('/adminlte/dist/css/skins/_all-skins.min.css') }}
    {{ stylesheet_link('/admin/style/css/ch-ui.admin.css') }}
    {{ stylesheet_link('/admin/style/font/css/font-awesome.min.css') }}
    {{ stylesheet_link('/admin/style/css/admin.admin.css') }}

    {{ javascript_include('js/lib/jQuery/jquery-2.2.3.min.js') }}
    {{ javascript_include('admin/org/bootstrap/js/bootstrap.min.js') }}
    {{ javascript_include('admin/org/multiselect/bootstrap-multiselect.js') }}
</head>
<script>
    //ueditor编辑器的工具栏配置
    var ueditor_toolbars = {
        toolbars: [["fullscreen","source","undo","redo","bold","indent","italic","underline","fontborder","strikethrough","superscript","formatmatch","pasteplain","subscript","preview","horizontal","insertunorderedlist","insertorderedlist","justifyleft","justifycenter","justifyright","justifyjustify","removeformat","simpleupload","snapscreen","emotion","inserttable","insertrow","insertcol","mergeright","mergedown","deleterow","deletecol","splittorows","splittocols","splittocells","deletecaption","inserttitle","mergecells","deletetable","cleardoc","fontsize","paragraph","link'","spechars","searchreplace","forecolor","backcolor","rowspacingtop","rowspacingbottom","imagecenter","lineheight","customstyle","autotypeset","background"]]};
    var multiselect_option = {
        // 自定义参数，按自己需求定义
        nonSelectedText : '--请选择--',
        enableFiltering: true,
        maxHeight : 350,
        includeSelectAllOption : true,
        numberDisplayed : 5
    };
    var multiselect_option_no_search = {
        // 自定义参数，按自己需求定义
        nonSelectedText : '--请选择--',
        maxHeight : 350,
        includeSelectAllOption : true,
        numberDisplayed : 5
    };
</script>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    {% Include "layout/header.volt" %}
    {% Include "layout/sidebar.volt" %}
    <div class="content-wrapper">
        {% block content %}

        {% endblock %}
    </div>
    {% Include "layout/footer.volt" %}
    <div class="control-sidebar-bg"></div>
</div>

{{ javascript_include('adminlte/dist/js/app.min.js') }}
{{ javascript_include('admin/style/js/jquery.form.js') }}
{{ javascript_include('admin/style/js/ch-ui.admin.js') }}
{{ javascript_include('org/layer/layer.js') }}
{{ javascript_include('js/lib/validate/jquery.validate.js') }}
{{ javascript_include('js/lib/validate/messages_cn.js') }}
{{ javascript_include('admin/style/js/admin.js') }}
</body>
</html>