{% extends "layout/header.volt" %}

{% block content %}

    <title>给他留言</title>
    <div class="wrap">
        <div class="title_g">
            <a class="return center" href="{{ url('status/workerStatusDetail?user_id=' ~ user_id ~ '&pid=' ~ project_id ~ '&did=' ~ department_id) }}">
                <img src="{{ url('home/style/img/return_03.png') }}" />
            </a>
            <h5 class="tetle_font">留言</h5>
            <a class="Reserved"></a>
        </div>
        <div class="main">
            <div class="Leaveamessage_con">
                <form>
                    <input type="text" name="comment_name" placeholder="输入姓名">
                    <input type="number" name="comment_phone" placeholder="输入手机号">
                    <textarea name="comment_content" id="lf_textarea" class="lf_textarea"  cols="" rows="" placeholder="输入留言"></textarea>
                    <input id="tijiao" type="button" value="提交">
                </form>
            </div>
        </div>
    </div>

    <script>

        $(function () {
            $("#tijiao").click(function () {
                if (valid()) {
                    var comment_user_id = '{{ user_id }}';
                    var comment_phone = $("input[name='comment_phone']").val();
                    var comment_name = $("input[name='comment_name']").val();
                    var comment_content = $("textarea[name='comment_content']").val();
                    $.ajax({
                        type: 'POST',
                        dataType: 'JSON',
                        url: "{{ url('status/addComment?pid=' ~ project_id ~ '&did=' ~ department_id) }}",
                        data: {
                            "{{ _csrfKey }}": "{{ _csrf }}",
                            'user_id': comment_user_id,
                            'comment_phone': comment_phone,
                            'comment_name': comment_name,
                            'comment_content': comment_content,
                        },
                        success: function (data) {
                            if(data.status == 201) {
                                layer.msg(data.msg, {
                                    icon: 6,
                                    time: 2000, //2s后自动关闭
                                },function (){
                                    location.href = "{{ url('status/workerStatusDetail?user_id=' ~ user_id ~ '&pid=' ~ project_id ~ '&did=' ~ department_id) }}";
                                });
                            }else{
                                layer.msg(data.msg, {icon: 5});
                            }
                        },
                        error: function () {
                            layer.msg('系统错误，请刷新后重试！', {icon: 2});
                        }
                    });
                }
            });
            $('.lf_textarea').keyup(function () {
                if ($(this).val().length >= 150) {
                    layer.msg('留言最多150个字符');
                    $(this).val($(this).val().substring(0, 150));
                    $(this).blur();
                    return false;
                }
            });
        });
        function valid() {
            var comment_phone = $("input[name='comment_phone']").val();
            var comment_name = $("input[name='comment_name']").val();
            var comment_content = $("textarea[name='comment_content']").val();
            if (comment_name == '') {
                layer.msg('请填写您的姓名');
                $("input[name='comment_name']").focus();
                return false;
            }
            if (comment_phone == '') {
                layer.msg('请填写您的电话号码');
                $("input[name='comment_phone']").focus();
                return false;
            } else {
                var pattern = /^1[3456789]\d{9}$/;
                if (!pattern.test(comment_phone)) {
                    $("input[name='comment_phone']").focus();
                    layer.msg('手机号格式不正确！');
                    return false;
                }
            }
            if (comment_content == '') {
                layer.msg('请填写留言');
                $("textarea[name='comment_content']").focus();
                return false;
            }


            if (comment_content.length >= 150) {
                layer.msg('留言最多150个字符');
                comment_content.val(comment_content.substring(0, 150));
                $("textarea[name='comment_content']").blur();
                return false;
            }
            return true;
        }
    </script>

{% endblock %}
