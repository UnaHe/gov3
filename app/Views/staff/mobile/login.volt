{% extends "mobile/header.volt" %}

{% block content %}

    <title>用户登录</title>
    <div class="warp my_wrap">
        <p class="lf_title">工作状态管理系统</p>
        <form id="login_form" name="login_form" method="post" action="{{url('staff/login')}}">
            <div class="phone">
                <input type="hidden" name="{{ _csrfKey }}" value="{{ _csrf }}"/>
                <input type="text" id="number" name="user_phone" placeholder="输入手机号" >
                <input type="password" id="password" name="password" placeholder="输入密码" >
                <input type="hidden"  name="user_pass">
            </div>
            <div class="click_pwd">
                <span class="ResidentialClosely">
                    <span class="yes">
                        {#<i class="gou"></i>#}
                    </span>
                </span>
                <span class="jizhu" name="is_remember">记住密码</span>
                {#<a href="{{url('staff/changepassword')}}" class="xiugai">修改密码</a>#}
                <input type="hidden"  name="remember" value="">
            </div>
            <input  type="submit"  class="submit" value="登录" id="login">
        </form>
        <p class="wangji">忘记密码请联系管理员</p>
    </div>
    <script>
        $(function(){
            $(".warp").height($(window).height()+"px");
            $("#login_form").validate({
                submitHandler: submitHandler,
                errorElement: "span",
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
    
            $('.ResidentialClosely').click(function(){
                if($(this).find('i.gou').length>0){
                    $("input[name='remember']").val('');
                    $('span.yes').empty();
                }else{
                    $("input[name='remember']").val(true);
                    $('span.yes').append('<i class="gou"></i>');
                }
            });
        });
    
        function submitHandler(){
            $("input[name='user_pass']").val($.md5($("input[name='password']").val()));
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: '{{url('staff/login')}}',
                beforeSubmit: function(){
                    layer('提交中...');
                },
                data:{
                    "{{ _csrfKey }}": "{{ _csrf }}",
                    'user_phone': $("input[name='user_phone']").val(),
                    'user_pass': $("input[name='user_pass']").val(),
                    'remember': $("input[name='remember']").val(),
                    'tpl': '{{ tpl }}',
                },success: function(data){
                    if (data.status == 200) {
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