@extends('staff.mobile.header')
@section('content')
    <title>修改密码</title>
    <div class="warp my_wrap">
        <p class="lf_title">工作状态管理系统</p>
        <form name="edit_form" id="edit_form">
            <div class="phone">
                <input type="hidden"  name="user_phone" value="{{session('staff')['user_phone']}}">
                {{--<input type="text"  name="user_phone" placeholder="输入账号">--}}
                <input type="password" name="old_password" placeholder="输入原密码">
                <input type="hidden"  name="user_pass">
                <input type="password" name="password" placeholder="输入密码">
                <input type="hidden"  name="new_user_pass">
                <input type="password" name="password_two" placeholder="再次输入密码">
            </div>
            <a class="linkLogin" href="{{url('staff/login?tpl=m')}}">已有账号？点此登录</a>
            <input type="submit" value="修改" id="Remediation">
        </form>
        <p class="wangji">忘记密码请联系管理员</p>
    </div>
    <script>
        $(function(){
            $(".warp").height($(window).height()+"px");
            $("#edit_form").validate({
                submitHandler: submitHandler,
                errorElement: "span",
                rules: {
//                    user_phone: {
//                        required: true,
//                        isMobile: true
//                    },
                    old_password: {
                        required: true,
                        minlength: 6
                    },
                    password: {
                        required: true,
                        minlength: 6
                    },
                    password_two: {
                        required: true,
                        equalTo: $("input[name='password']")
                    }
                },
                messages: {
                    user_phone: {
                        required: "请输入你的手机号",
                        isMobile: "请填写正确的手机号",
                    },
                    old_password: {
                        required: "请输入原始密码",
                        minlength: "密码长度不能小于 6 个字符"
                    },
                    password: {
                        required: "请输入新的密码",
                        minlength: "密码长度不能小于 6 个字符"
                    },
                    password_two: {
                        required: "请再次输入密码",
                        equalTo: "两次密码不一样"
                    }
                }
            });
        })

        function submitHandler(){
            $("input[name='user_pass']").val($.md5($("input[name='old_password']").val()));
            $("input[name='new_user_pass']").val($.md5($("input[name='password']").val()));
            $.ajax({
                type: 'post',
                url: '{{url('staff/changepassword')}}',
                dataType: 'JSON',
                beforeSubmit: function(){
                    layer('提交中...');
                },
                data:{
                    '_token': '{{csrf_token()}}',
                    'user_phone':$("input[name='user_phone']").val(),
                    'user_pass':$("input[name='user_pass']").val(),
                    'new_user_pass':$("input[name='new_user_pass']").val()
                },success: function(data){
                    if(data.status == 200){
                        layer.msg('修改成功，请重新登陆！');
                        location.href = '{{url('staff/loginout')}}';
                        {{--setTimeout(function(){--}}
                            {{--location.href = '{{url('staff/login')}}';--}}
                        {{--},3000);--}}
                    }else{
                        layer.msg(data.msg);
                    }
                },error: function(){
                    layer.msg('系统错误，请刷新后重试！');
                }
            })
        }
    </script>
@endsection