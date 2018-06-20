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
                    <img src="{{ data['user_info']['user_image'] is not empty ? _config['upload_url'] ~ data['user_info']['user_image'] : _config['default_staff_img'] }}">
                </div>
                <div class="personnel_info_font">
                    <span class="PersonnelName">{{ data['user_info']['user_name'] }}</span>
                    <span class="PersonnelDuties">{{ data['user_info']['user_job'] == '' ? '科员' : data['user_info']['user_job'] }}</span>
                    <span class="PersonnelRole text_overflow">{{ data['user_info']['user_intro'] == '' ? '' :  data['user_info']['user_intro'] }}</span>
                </div>
            </div>
            <div class="CurrentState">
                <div class="CurrentState_title">
                    <span class="ghost"></span>
                    <span class="CurrentState_title_font">当前状态</span>
                    <span class="CurrentState_d no_status"
                          style="background-color: {{ data['user_info']['status_color'] }}">{{ data['user_info']['status_name'] }}</span>
                </div>
                <div class="time">
                    <p class="clear">
                        <a class="StartingTime">开始时间</a>
                        <a class="im_time">{{ data['user_info']['user_status_start_time'] }}</a>
                    </p>
                    <p class="clear">
                        <a class="StartingTime">结束时间</a>
                        <a class="im_time">{{ data['user_info']['user_status_end_time'] }}</a>
                    </p>
                </div>
            </div>
            <div class="MessageBoard">
                <div class="CurrentState_title">
                    <span class="ghost"></span>
                    <span class="CurrentState_title_font">TA的留言 (仅显示最近5条)</span>
                    {% if data['user_comments'] !== false %}
                        {% for v in  data['user_comments'] %}
                            <span class="CurrentState_font"><?php echo strlen($v['comment_content']) > 20 ? mb_substr($v['comment_content'],0,20,'utf-8').'...' : $v['comment_content']; ?></span>
                        {% endfor %}
                    {% else %}
                        <span class="CurrentState_font">暂无留言</span>
                    {% endif %}
                </div>
            </div>
            <input id="leaveamessageBtn" type="button" value="给TA留言">
        </div>
    </div>
    <script>
        $(function () {
            $("#leaveamessageBtn").click(function () {
                location.href = "{{ url('status/addComment?user_id=' ~ data['user_info']['user_id'] ~ '&pid=' ~ project_id ~ '&did=' ~ department_id) }}";
            })
        });
    </script>

{% endblock %}