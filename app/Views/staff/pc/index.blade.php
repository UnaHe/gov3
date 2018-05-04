@extends('staff.pc.header')
@section('content')
    <!--个人中心页-->
{{--    <link rel="stylesheet" href="{{asset('staff/style/css/style.css')}}">--}}
    <title>个人中心</title>
    <div class="wrap">
        <div class="title_g">
            <a class="return center" href="javascript:history.back(-1);"><img src="../staff/style/img/return_03.png"></a>
            <h5 class="tetle_font">科员个人信息</h5>
            <a class="Reserved"></a>
        </div>
        <div class="main">
            <div class="personnel_info clear">
                <div class="personnel_info_img"><img src="{{$data['user_image']}}"></div>
                <div class="personnel_info_font y_personnel_info_font">
                    <span class="PersonnelName y_PersonnelName">{{$data['user_name']}}</span>
                    <span class="my_set" >
                         <img src="../staff/style/img/tongji.png" onclick="location.href = '{{url('staff/count')}}'">
                        <img src="../staff/style/img/sc.png" onclick="location.href = '{{url('staff/setting')}}'">
                    </span>
                    <span class="PersonnelDuties">{{$data['user_job'] or '科员'}}</span>

                    <span class="PersonnelRole text_overflow">{{$data['user_intro'] or '无个人简介'}}</span>
                </div>
            </div>
            <div class="CurrentState y_CurrentState">
                <div class="CurrentState_title y_CurrentState_title">
                    <span class="ghost"></span>
                    <span class="CurrentState_title_font">当前状态</span>
                </div>
                <div class="y_time clear">
                    <div class="y_time_left center">
                        <span class="y_xiuxi no_status"
                              style="background:{{$data['nowstatus']['status_color']}}">{{$data['nowstatus']['status_name']}}</span>
                    </div>
                    <div class="y_time_con">
                        <p class="beginning"><a>始</a>&nbsp;&nbsp;<a>{{$data['nowstatus']['start_time']}}</a></p>
                        <p class="expiry"><a>终</a>&nbsp;&nbsp;<a>{{$data['nowstatus']['end_time']}}</a></p>
                    </div>
                    {{--<div class="y_shutdown center"><img src="../staff/style/img/y_shutdown_03.png"></div>--}}
                </div>
            </div>
            <div class="CurrentState y_CurrentState">
                <div class="CurrentState_title y_CurrentState_title">
                    <span class="ghost"></span>
                    <span class="CurrentState_title_font">计划列表</span>
                    <span class="CurrentState_d CurrentState_e" onclick="edit_user_status(0)"><img src="../staff/style/img/xz1.png"></span>
                </div>
                @if(!empty($data['statuslist']))
                    @foreach($data['statuslist'] as $v)
                        <div class="y_time clear">
                            <div class="y_time_left center edit_status" data-status="{{$v['user_status_id']}}">
                                <span class="y_xiuxi no_status"
                                      style="background:{{$v['status_color']}}">{{$v['status_name']}}</span>
                            </div>
                            <div class="y_time_con edit_status" data-status="{{$v['user_status_id']}}">
                                <p class="beginning"><a>始</a>&nbsp;&nbsp;<a>{{$v['start_time']}}</a></p>
                                <p class="expiry"><a>终</a>&nbsp;&nbsp;<a>{{$v['end_time']}}</a></p>
                            </div>
                            <div class="y_shutdown center"
                                 onclick="del_user_status({{$v['user_status_id']}},'{{$v['status_name']}}')"><img
                                        src="../staff/style/img/y_shutdown_03.png"></div>
                        </div>
                    @endforeach
                @endif
                {{--<div class="y_time clear">--}}
                {{--<div class="y_time_left center">--}}
                {{--<span class="y_xiuxi no_status">休息</span>--}}
                {{--</div>--}}
                {{--<div class="y_time_con">--}}
                {{--<p class="beginning"><a>始</a>&nbsp;&nbsp;<a>2017/12/25 09:00</a></p>--}}
                {{--<p class="expiry"><a>终</a>&nbsp;&nbsp;<a>2017/12/25 18:00</a></p>--}}
                {{--</div>--}}
                {{--<div class="y_shutdown center"><img src="../staff/style/img/y_shutdown_03.png"></div>--}}
                {{--</div>--}}
            </div>
            <div class="CurrentState y_CurrentState">
                <div class="CurrentState_title y_CurrentState_title">
                    <span class="ghost"></span>
                    <span class="CurrentState_title_font">我的留言</span>
                    <span class="CurrentState_d CurrentState_e" onclick="location.href='{{url('staff/edit_comments')}}'" ><img src="../staff/style/img/bj.png"></span>
                    <span class="CurrentState_font">{{$data['user_comments']}}</span>
                </div>
            </div>
            <div class="MessageBoard">
                <div class="CurrentState_title">
                    <span class="ghost"></span>
                    <span class="CurrentState_title_font">留言区</span>
                    @if(!empty($data['comments']))
                        @foreach($data['comments'] as $v)
                            <p class="MessageArea">
                                <a  onclick=" location.href = '{{url('staff/commentone?comment_id='.$v['comment_id'])}}';" >{{$v['comment_name']}}</a><a  onclick=" location.href = '{{url('staff/commentone?comment_id='.$v['comment_id'])}}';" class="{{$v['comment_status']==0 ? 'unread' : 'read'}}">{{$v['comment_status']==0 ? '未处理' : '已处理'}}
                                </a>
                            </p>
                            <span class="CurrentState_font"  onclick=" location.href = '{{url('staff/commentone?comment_id='.$v['comment_id'])}}';">
                                {{$v['comment_content']}}
                            </span>
                        @endforeach
                    @endif
                </div>
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
            $(".edit_status").click(function () {
                var user_status_id = $(this).data('status');
                edit_user_status(user_status_id);
            })

        })
        //编辑计划
        function edit_user_status(user_status_id) {
            location.href = '{{url('staff/addstatus?user_status_id=')}}' + user_status_id;
        }
        //删除
        function del_user_status(user_status_id, status_name) {
            layer.confirm('确实要删除  ' + status_name + '  计划吗？', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.ajax({
                    type: 'post',
                    url: '{{url('staff/delstatus')}}',
                    dataType: 'JSON',
                    beforeSubmit: function () {
                        layer('提交中...');
                    },
                    data: {
                        '_token': '{{csrf_token()}}',
                        'user_status_id': user_status_id
                    }, success: function (data) {
                        if (data.status == 200) {
                            layer.msg(data.msg);
                            setTimeout(function () {
                                location.href = '{{url('staff/refresh')}}';
                            }, 3000);
                        } else {
                            layer.msg(data.msg);
                        }
                    }, error: function () {
                        layer.msg('系统错误，请刷新后重试！');
                    }
                })
            });
        }
    </script>
@endsection