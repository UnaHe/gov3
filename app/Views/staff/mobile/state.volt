{% extends "mobile/header.volt" %}

{% block content %}

    <title>个人状态编辑</title>
    <div class="warp_2">
        <form action="" id="edit_form">
            <input name="user_status_id" type="hidden" value="{{ old_info.user_status_id is defined ? old_info.user_status_id : '' }}">
            <div class="xiala">
                <select name="status_id" id="lf_select">
                    <option value="">请选择</option>
                    {% for v in statuslist %}
                        <option value="{{ v.status_id }}"
                                {{ old_info.status_id is defined and old_info.status_id == v.status_id ? 'selected' : '' }}
                        >{{ v.status_name }}</option>
                    {% endfor %}
                </select>
            </div>
            <div class="Selection_Time">
                <div class="Start_Time clear">
                    <input type="text"  name="start_date" value="{{ old_info.start_time is defined and old_info.start_time ? date('Y-m-d', old_info.start_time) : '' }}" placeholder="开始日期" readonly>
                    <span class="Start_Time_img start_date"><img src="{{url('staff/style/img/time1_03.png')}}"></span>

                    <input type="text" name="start_time" value="{{ old_info.start_time is defined and old_info.start_time ? date('H:i', old_info.start_time) : '' }}" placeholder="时间" readonly>
                    <span class="Start_Time_img start_time"><img src="{{url('staff/style/img/time2_03.png')}}"></span>
                </div>
            </div>
            <div class="Selection_Time">
                <div class="Start_Time clear">
                    <input type="text" name="end_date" value="{{ old_info.end_time is defined and old_info.end_time ? date('Y-m-d', old_info.end_time) : '' }}" placeholder="结束日期" readonly>
                    <span class="Start_Time_img end_date"><img src="{{url('staff/style/img/time1_03.png')}}"></span>

                    <input type="text" name="end_time" value="{{ old_info.end_time is defined and old_info.end_time ? date('H:i', old_info.end_time) : '' }}" placeholder="时间" readonly>
                    <span class="Start_Time_img end_time"><img src="{{url('staff/style/img/time2_03.png')}}" readonly></span>
                </div>
            </div>
            <div class="my_textarea">
                <textarea name="desc" id="lf_textarea" cols="" rows="" placeholder="备注">{{ old_info.user_status_desc is defined ? old_info.user_status_desc : '' }}</textarea>
            </div>
            <input type="submit" value="保存" id="Save_btn">
        </form>
    </div>
    {{ javascript_include('org/datePicker/js/datePicker.js') }}
    <script>
        $(function(){
            //开始日期
            var calendar = new datePicker();
            calendar.init({
                'trigger': "input[name='start_date']",//标签id
                'type': 'date'//date 调出日期选择 datetime 调出日期时间选择 time 调出时间选择 ym 调出年月选择
            });
            var calendar = new datePicker();
            calendar.init({
                'trigger': ".start_date",//标签id
                'type': 'date'//date 调出日期选择 datetime 调出日期时间选择 time 调出时间选择 ym 调出年月选择
            });
            //开始时间
            var calendar = new datePicker();
            calendar.init({
                'trigger': "input[name='end_date']",//标签id
                'type': 'date'//date 调出日期选择 datetime 调出日期时间选择 time 调出时间选择 ym 调出年月选择
            });
            var calendar = new datePicker();
            calendar.init({
                'trigger': ".end_date",//标签id
                'type': 'date'//date 调出日期选择 datetime 调出日期时间选择 time 调出时间选择 ym 调出年月选择
            });
            //结束日期
            var calendar = new datePicker();
            calendar.init({
                'trigger': "input[name='start_time']",//标签id
                'type': 'time'//date 调出日期选择 datetime 调出日期时间选择 time 调出时间选择 ym 调出年月选择
            });
            var calendar = new datePicker();
            calendar.init({
                'trigger': ".start_time",//标签id
                'type': 'time'//date 调出日期选择 datetime 调出日期时间选择 time 调出时间选择 ym 调出年月选择
            });
            //结束时间
            var calendar = new datePicker();
            calendar.init({
                'trigger': "input[name='end_time']",//标签id
                'type': 'time'//date 调出日期选择 datetime 调出日期时间选择 time 调出时间选择 ym 调出年月选择
            });
            var calendar = new datePicker();
            calendar.init({
                'trigger': ".end_time",//标签id
                'type': 'time'//date 调出日期选择 datetime 调出日期时间选择 time 调出时间选择 ym 调出年月选择
            });


            $("#edit_form").validate({
                submitHandler: submitHandler,
                errorElement: "span",
                rules: {
                    status_id: {
                        required: true
                    },
                    start_date: {
                        required: true
                    },
                    start_time: {
                        required: true
                    },
                    end_date: {
                        required: true
                    },
                    end_time: {
                        required: true
                    }
                },
                messages: {
                    status_id: {
                        required: "请选择一个事件"
                    },
                    start_date: {
                        required:''
//                        required: "请选择开始日期"
                    },
                    start_time: {
                        required:''
//                        required: "请选择开始时间"
                    },
                    end_date: {
                        required:''
//                        required: "请选择结束日期"
                    },
                    end_time: {
                        required:''
//                        required: "请选择结束时间"
                    }
                }
            });
        });

        function submitHandler(){
            var start_time = $("input[name='start_date']").val()+$("input[name='start_time']").val();
            var end_time = $("input[name='end_date']").val()+$("input[name='end_time']").val();
            var status_id = $("select[name='status_id']").val();
            var user_status_id = $("input[name='user_status_id']").val();
            var desc = $.trim($("textarea[name='desc']").val());
            var user_id = '{{_session['user_id']}}';
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: '{{url('staff/addstatus')}}',
                beforeSubmit: function(){
                    layer('提交中...');
                },
                data:{
                    "{{ _csrfKey }}": "{{ _csrf }}",
                    'user_id': user_id,
                    'start_time': start_time,
                    'end_time': end_time,
                    'status_id': status_id,
                    'user_status_id': user_status_id,
                    'desc': desc,
                },success: function(data){
                    if (data.status == 201) {
                        layer.msg(data.msg, {
                            icon: 6,
                            time: 2000, //2s后自动关闭
                        },function (){
                            location.href = '{{url('staff/refresh')}}';
                        });
                    } else {
                        layer.msg(data.msg, {icon: 5});
                    }
                },error: function(){
                    layer.msg('系统错误，请刷新后重试！', {icon: 2});
                }
            })
        }
    </script>

{% endblock %}