{% extends "layout/header.volt" %}

{% block content %}

    <style>
        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .Noticedetails_con p{
            position: relative;
           /*left: -2em;*/
        }
    </style>
    <title>科室详情</title>
    <div class="wrap">
        <div class="title_g">
            <a class="return center" href="{{ url('/status/workerStatusList?pid=' ~ project_id ~ '&did=' ~ department_id) }}">
                <img src="{{ url('home/style/img/return_03.png') }}" />
            </a>
            <h5 class="tetle_font">科室介绍（{{ department_info['department_name'] }}）</h5>
            <a class="Reserved"></a>
        </div>
        <div class="Noticedetails_con" style="margin-top: 1.28rem;padding: 10px">
            {{ department_info is defined and department_info['department_desc'] is not empty ? department_info['department_desc'] : '' }}
        </div>
    </div>

{% endblock %}