<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>橙视光标智慧科室系统-登录</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    {{ stylesheet_link('/admin/style/css/admin.admin.css') }}
</head>
<style>
    span.error {
        padding-left: 16px;
    }

    label.error {
        padding-left: 16px;
    }
</style>
<body>
<div class="admin_login">
    <div class="admin_login_img">
        <div class="admin_login_con">
            <form action="" id="login_form">
                <div class="username_div">
                    <span>电话</span>
                    <input type="text" name="user_phone" class="admin_name">
                    <p for="user_phone" class="error"></p>
                </div>
                <div class="password_div">
                    <span>密码</span>
                    <input name="user_pass" type="hidden" class="form-control" placeholder="密码">
                    <input type="password" name="password" class="admin_password">
                    <p for="password" class="error"></p>
                </div>
                <p>
                    <input type="submit" value="登录" class="login_btn">
                </p>
            </form>
        </div>
    </div>
</div>
</body>

{{ javascript_include('js/lib/jQuery/jquery-2.2.3.min.js') }}
{{ javascript_include('js/lib/validate/jquery.validate.js') }}
{{ javascript_include('js/lib/jquery.md5.js') }}
{{ javascript_include('org/layer/layer.js') }}

<script>
    $(function () {
        myresize();
        window.onresize = function(){
            myresize();
        };
        function myresize(){
            var hei = document.documentElement.clientHeight;
            document.getElementsByClassName("admin_login")[0].style.height = hei+'px';
        }
    });

    $(function () {
        var hei = document.documentElement.clientHeight;
        document.getElementsByClassName("admin_login")[0].style.height = hei + 'px';
        $("#login_form").validate({
            submitHandler: submitHandler,
            errorElement: "p",
            rules: {
                user_phone: {
                    required: true,
                    isMobile: true
                },
                password: {
                    required: true,
                    minlength: 6
                }
            },
            messages: {
                user_phone: {
                    required: "请输入你的手机号",
                    isMobile: "请填写正确的手机号",
                },
                password: {
                    required: "请输入密码",
                    minlength: "密码长度不能小于 6 个字符"
                }
            }
        });
    });

    function submitHandler() {
        $("input[name='user_pass']").val($.md5($("input[name='password']").val()));
        $.ajax({
            url: '{{ url('admin/login') }}',
            type: 'POST',
            dataType: 'JSON',
            beforeSubmit: function () {
                layer('提交中...');
            },
            data: {
                "{{ _csrfKey }}": "{{ _csrf }}",
                'user_phone': $("input[name='user_phone']").val(),
                'user_pass': $("input[name='user_pass']").val()
            },
            success: function (data) {
                if (data.status == 200) {
                    location.href = '{{url('admin/home')}}';
                } else {
                    layer.msg(data.msg, {
                        icon: 5,
                        time: 2000, //2s后自动关闭
                    },function (){
                        location.reload();
                    });
                }
            },
            error: function () {
                layer.msg('系统错误，请刷新后重试！', {icon: 2});
            }
        })
    }
</script>
</html>