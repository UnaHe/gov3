@extends('staff.pc.header')
@section('content')
    <title>设置</title>
{{--    <link rel="stylesheet" href="{{asset('staff/style/css/style.css')}}">--}}
    <div class="wrap">
        <div class="title_g">
            <a class="return center" href="{{url('staff/refresh')}}"><img src="../staff/style/img/return_03.png"></a>
            <h5 class="tetle_font">设置</h5>
            <a class="Reserved"></a>
        </div>
        <div class="main">
            <div class="x_pwd">
                <span onclick="location.href='{{url('staff/changepassword')}}';">修改密码</span>
                <span class="x_imgbg" onclick="location.href='{{url('staff/changepassword')}}';"></span>
            </div>
            <div class="x_pwd">
                <span class="loginout">退出登录</span>
                <span class="x_imgbg loginout"></span>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            $('.loginout').click(function () {
                layer.confirm('确定要退出登录吗？', {
                    btn: ['确定', '取消'] //按钮
                }, function () {
                    $.ajax({
                        type: 'post',
                        url: '{{url('staff/loginout')}}',
                        dataType: 'JSON',
                        beforeSubmit: function () {
                            layer('提交中...');
                        },
                        data: {
                            '_token': '{{csrf_token()}}'
                        }, success: function (data) {
                            if (data.status == 200) {
                                location.href = '{{url('staff/login')}}';
                            } else {
                                layer.msg(data.msg);
                            }
                        }, error: function () {
                            layer.msg('系统错误，请刷新后重试！');
                        }
                    })
                });
            })
        })
    </script>
@endsection