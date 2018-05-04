{% extends "layout/header.volt" %}

{% block content %}

    <style>
        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
    <title>单位介绍</title>
    <div class="wrap">
        <div class="title_g">
            <a class="return center" href="{{ url('status/index?pid=' ~ project_id ~ '&did=' ~ department_id) }}">
                <img src="{{ url('home/style/img/return_03.png') }}" />
            </a>
            <h5 class="tetle_font">单位介绍</h5>
            <a class="Reserved"></a>
        </div>
        <div class="Noticedetails_con" style="margin-top: 1.28rem;padding: 10px">
            {{  project_info is defined and project_info.project_profile is not empty ? project_info.project_profile : '' }}
        </div>
    </div>

{% endblock %}