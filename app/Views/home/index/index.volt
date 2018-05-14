{% extends "layout/header.volt" %}

{% block content %}

    <title>主页</title>
    <div class="wrap">
        <div class="index_bg">
            <img src="{{ title_info is defined and title_info.project.project_image is not empty ? _config['upload_url'] ~ title_info.project.project_image : '/home/style/img/index.jpg' }}" />
        </div>
        <div class="im_index">
            <h3 class="im_index_title">{{ title_info.project.project_name }}</h3>
            <a href="{{ url('status/workerStatusList?pid=' ~ project_id ~ '&did=' ~ department_id) }}">工作状态</a>
            <a href="{{ url('department/projectDetail?pid=' ~ project_id ~ '&did=' ~ department_id) }}">单位介绍</a>
            <a href="{{ url('department/departmentList?pid=' ~ project_id ~ '&did=' ~ department_id) }}">科室列表</a>
        </div>
    </div>
    <script>
        $(".wrap,.im_index,.index_bg").height($(window).height()+"px");
    </script>
    <style type="text/css">
        .wrap{position: fixed;top: 0;left: 0;}
    </style>

{% endblock %}