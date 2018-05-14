{% extends "layout/header.volt" %}

{% block content %}

    <style>
        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
        }

    </style>
    <title>公告详情</title>
    <div class="wrap">
        <div class="title_g">
            <a class="return center" href="{{ url('notice/index?pid=' ~ project_id ~ '&did=' ~ department_id) }}">
                <img src="{{ url('home/style/img/return_03.png') }}" />
            </a>
            <h5 class="tetle_font">公告详情</h5>
            <a class="Reserved"></a>
        </div>
        <div class="main">

            <div class="Noticedetails">
                <h5 class="Noticedetails_title">{{ data.notice_title }}</h5>
                <p class="Noticedetails_time">{{ date("Y/m/d", data.created_at) }}</p>
                <div class="Noticedetails_con">
                    <p>{{ data.notice_content is not empty ? data.notice_content : '' }}</p>
                </div>
            </div>
        </div>
    </div>

{% endblock %}