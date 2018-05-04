{% extends "layout/header.volt" %}

{% block content %}

    <title>工作状态详情</title>
    <div class="wrap">
        <div class="title_g">
            <a class="return center" href="{{url('status/workerStatusList?pid=' ~ project_id ~ '&did=' ~ department_id)}}">
                <img src="{{ url('home/style/img/return_03.png') }}" />
            </a>
            <h5 class="tetle_font">个人信息</h5>
            <a class="Reserved"></a>
        </div>
        <div class="main">
            <div class="personnel_info clear">
                <div class="personnel_info_img">
                    <img src="{{ data.user_image is not empty ? _config['upload_url'] ~ data.user_image : _config['defalut_staff_img'] }}">
                </div>
                <div class="personnel_info_font">
                    <span class="PersonnelName">{{ data.user_name }}</span>
                    <span class="PersonnelDuties">{{ data.user_job == '' ? '科员' : data.user_job }}</span>
                    <span class="PersonnelRole text_overflow">{{ data.user_intro == '' ? '' :  data.user_intro }}</span>
                </div>
            </div>
            <div class="CurrentState">
                <div class="CurrentState_title">
                    <span class="ghost"></span>
                    <span class="CurrentState_title_font">当前状态</span>
                    <span class="CurrentState_d no_status"
                          style="background-color: {{ data.status_color }}">{{ data.status_name }}</span>
                </div>
                <div class="time">
                    <p class="clear">
                        <a class="StartingTime">开始时间</a>
                        <a class="im_time">{{ data.user_status_start_time }}</a>
                    </p>
                    <p class="clear">
                        <a class="StartingTime">结束时间</a>
                        <a class="im_time">{{ data.user_status_end_time }}</a>
                    </p>
                </div>
            </div>
            <div class="MessageBoard">
                <div class="CurrentState_title">
                    <span class="ghost"></span>
                    <span class="CurrentState_title_font">他的留言</span>
                    <span class="CurrentState_font">{{ data.user_comments }}</span>
                </div>
            </div>
            <input id="leaveamessageBtn" type="button" value="给他留言">
        </div>
    </div>
    <script>
        $(function () {
            $("#leaveamessageBtn").click(function () {
                location.href = "{{ url('status/addcomment?user_id=' ~ data.user_id ~ '&pid=' ~ project_id ~ '&did=' ~ department_id) }}";
            })
        });
    </script>

{% endblock %}
