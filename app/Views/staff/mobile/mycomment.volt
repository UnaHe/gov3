{% extends "mobile/header.volt" %}

{% block content %}

    <title>修改我的留言</title>
    <div class="warp_2">
        <form action="" id="edit_form">
            <input name="user_phone" type="hidden" value="{{ data['user_phone'] }}">
            <div class="my_textarea">
                <textarea name="user_comments" id="lf_textarea" cols="" rows="" placeholder="我的留言">{{ data['user_comments'] }}</textarea>
            </div>
            <input type="submit" value="保存" id="Save_btn">
        </form>
    </div>
    <script>
        $(function(){
            //开始日期
            $("#edit_form").validate({
                submitHandler: submitHandler,
                errorElement: "span",
                rules: {
                    status_id: {
                        required: true
                    }
                },
                messages: {
                    status_id: {
                        required: "请选择一个事件"
                    }
                }
            });
        });
        function submitHandler(){
            var user_phone = "{{ data['user_phone'] }}";
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: "{{url('staff/edit_comments')}}",
                beforeSubmit: function(){
                    layer('提交中...');
                },
                data:{
                    "{{ _csrfKey }}": "{{ _csrf }}",
                    'user_phone': user_phone,
                    'user_comments': $("textarea[name='user_comments']").val()
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