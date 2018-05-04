@extends('staff.mobile.header')
@section('content')
    <title>统计状态列表</title>
    <style>
        .more img{
            height: 20px;
        }
    </style>
    <div class="warp_1">
        <div class="main">
            <div class="bulletin">
                <ul class="" style="overflow: hidden">
                    <li>
                        <span class="bulletin_time text_overflow">姓名</span>
                        <span class="bulletin_time text_overflow search_user" data-type="section">部门
                            <image class='xlIcon' src='{{asset('staff/style/img/select_triangle.png')}}'></image>
                        </span>
                        <span class="bulletin_time text_overflow search_user" data-type="department">科室<image class='xlIcon' src='{{asset('staff/style/img/select_triangle.png')}}'></image>
                        </span>
                        {{--<select name="section_id" class="bulletin_time text_overflow">--}}
                        {{--<option value="0" selected>部门</option>--}}
                        {{--@foreach($section_list as $v)--}}
                        {{--<option value="{{$v->section_id}}">{{$v->section_name}}</option>--}}
                        {{--@endforeach--}}
                        {{--</select>--}}
                        {{--<select name="department_id" class="bulletin_time text_overflow">--}}
                        {{--<option value="0" selected>办公室</option>--}}
                        {{--@foreach($department_list as $v)--}}
                        {{--<option value="{{$v->department_id}}">{{$v->department_name}}</option>--}}
                        {{--@endforeach--}}
                        {{--</select>--}}
                        <span class="bulletin_time text_overflow search_user" data-type="status">状态
                        <image class='xlIcon' src='{{asset('staff/style/img/select_triangle.png')}}'></image>
                        </span>
                    </li>
                </ul>
                <ul class="list" style="overflow: hidden">
                </ul>
            </div>
        </div>

        <div class="section_option departmentDiv bright_view_hide">
            <div class="departmentDiv_con">
                <ul>
                    <li data-id="0" data-type="section" class="listBtn1">— 部门 —</li>
                    @foreach($section_list as $v)
                        <li data-id="{{$v->section_id}}" data-type="section" class="listBtn1">{{$v->section_name}}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="department_option departmentDiv bright_view_hide">
            <div class="departmentDiv_con">
                <ul>
                    <li data-id="0" data-type="department" class="listBtn1">— 科室 —</li>
                    @foreach($department_list as $v)
                        <li data-id="{{$v->department_id}}" data-type="department" class="listBtn1">{{$v->department_name}}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="status_option departmentDiv bright_view_hide">
            <div class="departmentDiv_con">
                <ul>
                    <li data-id="0" data-type="status" class="listBtn1">— 状态 —</li>
                    @foreach($status_list as $v)
                        <li data-id="{{$v->status_id}}" data-type="status" class="listBtn1">{{$v->status_name}}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="{{asset('js/lib/dropload/dropload.css')}}">
    <script src="{{asset('js/lib/dropload/dropload.min.js')}}"></script>
    <script>
        var section_id = department_id = 0;
        var status_id = '{{$status_id}}';
        var load_obj;
        var url = "{{url('staff/countstatusdetailbystatus')}}";
        var page_size = 20;
        var page = 1;
        var have_data = true;

        $(function () {
            $(".listBtn1").click(function () {
                var id = $(this).data('id');
                var type = $(this).data('type');
                if(type == 'section'){
                    section_id = id;
                }
                if(type == 'department'){
                    department_id = id;
                }
                if(type == 'status'){
                    status_id = id;
                    url = !!status_id ?  "{{url('staff/countstatusdetailbystatus')}}" : "{{url('staff/countstatusdetail')}}";
                }
                search_user();
            })
            $(".search_user").click(function () {
                var type = $(this).data('type');
                $("div."+type+"_option").removeClass('bright_view_hide').addClass('bright_view_show');
            });

             $('.departmentDiv').bind('click',function(){
				 $(this).removeClass('bright_view_show').addClass('bright_view_hide');
			 })
            // dropload
            $('.main').dropload({
                scrollArea: window,
                domUp: {
                    domClass: 'dropload-up',
                    domRefresh: '<div class="dropload-refresh">↓下拉刷新</div>',
                    domUpdate: '<div class="dropload-update">↑释放更新</div>',
                    domLoad: '<div class="dropload-load"><span class="loading"></span>加载中...</div>'
                },
                domDown: {
                    domClass: 'dropload-down',
                    domRefresh: '<div class="dropload-refresh">↑上拉加载更多</div>',
                    domLoad: '<div class="dropload-load"><span class="loading"></span>加载中...</div>',
                    domNoData: '<div class="dropload-noData">没有更多了</div>'
                },
                loadUpFn: function (me) {
                    load_obj = me;
                    have_data = true;
                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {
                            '_token': "{{csrf_token()}}",
                            'type': '{{$type}}',
                            section_id: section_id,
                            department_id: department_id,
                            status_id:status_id
                        },
                        success: function (data) {
                            $('.list').empty();
                            if (data.data.data) {
                                var result = set_list(data.data.data);
                                $('.list').html(result);
                                me.resetload();
                                me.unlock();
                                me.noData(false);
                            } else {
                                have_data = false;
                                me.noData(true);
                                me.resetload();
                                me.unlock();
                            }
                        },
                        error: function (xhr, type) {
                            layer.msg('网络错误，请刷新后重试！');
                            // 即使加载出错，也得重置
//                            me.resetload();
                        }
                    });
                },
                loadDownFn: function (me) {
                    load_obj = me;
                    var flag = true;
                    len = $('.list li').length * 1;
                    if ((len % page_size == 0) && have_data && flag) {
                        flag = false;
                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: {
                                '_token': "{{csrf_token()}}",
                                'type': "{{$type}}",
                                'page': page,
                                section_id: section_id,
                                department_id: department_id,
                                status_id:status_id
                            },
                            success: function (data) {
                                page++;
                                if (!data.data.data) {
                                    have_data = false;
                                    me.unlock();
                                    me.noData(true);
                                    me.resetload();
                                    return false;
                                } else {
                                    var result = set_list(data.data.data);
                                    $('.list').append(result);
                                    // 每次数据加载完，必须重置
                                    me.resetload();
                                }
                            },
                            error: function (xhr, type) {
                                layer.msg('网络错误，请刷新后重试！');
                                me.lock();
                                // 即使加载出错，也得重置
//                                me.resetload();
                            }
                        });
                    } else {
                        me.unlock();
                        me.noData(true);
                        me.resetload();
                    }
                },
                threshold: 50
            });
        });
        {{--function show_detail(notice_id) {--}}
            {{--location.href = "{{url('notice/detail?notice_id=')}}" + notice_id + '&pid=' + '{{$project_id}}' + '&did=' + '{{$department_id}}';--}}
        {{--}--}}

        $(function () {
            $('.show_detail').click(function () {
                var status_id = $(this).parent('li').data('id');
                //ajax
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: '{{url('staff/countdetail')}}',
                    data: {
                        'type': type,
                        'status_id': status_id,
                    },
                    success: function (data) {

                    },
                    error: function (data) {
                        layer.msg('加载失败，请重试！');
                    }
                })
            })
        })

        function set_list(data) {
            var result = '';
            $.each(data, function (k, v) {
                var link = "location.href='{{url('staff/userstatuslist?user_id=')}}" + v.user_id + "'";
                result += ' <li data-id="">' +
                    ' <span class="bulletin_font">' + v.user_name + '</span>' +
                    '<span class="bulletin_font text_overflow">' + (v.section_name ? v.section_name : '--') + '</span>' +
                    '<span class="bulletin_font text_overflow">' + (v.department_name ? v.department_name : '--') + '</span>' +
                    '<span class="bulletin_font text_overflow" onclick="' + link + '">' + v.status_name + '' +
                    '<span class="more" ><img src="/staff/style/img/zl.png" alt=""></span>' +
                    '</span>' +
                    ' </li>';
            });
            return result;
        }
        function search_user() {
            page = 2;
            have_data = true;
            $("div.departmentDiv").removeClass('bright_view_show').addClass('bright_view_hide');
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': "{{csrf_token()}}",
                    'type': '{{$type}}',
                    section_id: section_id,
                    department_id: department_id,
                    status_id: status_id
                },
                success: function (data) {
                    $('.list').empty();
                    if (data.data.data) {
                        var result = set_list(data.data.data);
                        $('.list').html(result);
                        load_obj.resetload();
                    } else {
                        have_data = false;
                        load_obj.unlock();
                        load_obj.noData(true);
                        load_obj.resetload();
                        return false;
                    }
                },
                error: function (xhr, type) {
                    layer.msg('网络错误，请刷新后重试！');
                    // 即使加载出错，也得重置
                }
            })
        }
    </script>
@endsection