@extends('staff.pc.header')
@section('content')
    <title>个人状态编辑</title>
    <div class="warp_2">
        <div class="title_g">
            <a class="return center" href="{{url('staff/refresh')}}"><img src="../staff/style/img/return_03.png"></a>
            <h5 class="tetle_font">计划编辑</h5>
            <a class="Reserved"></a>
        </div>
        <form action="" id="edit_form">
            <input name="user_status_id" type="hidden" value="{{$old_info->user_status_id or ''}}">
            <div class="xiala">
                <select name="status_id" id="lf_select">
                    <option value="">请选择</option>
                    @foreach($statuslist as $v)
                        <option value="{{$v->status_id}}"
                                @if(isset($old_info->status_id) && $old_info->status_id==$v->status_id)
                                    selected
                                @endif
                        >{{$v->status_name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="Selection_Time">
                <div class="Start_Time clear">
                    <input type="text"  name="start_date" class="start_date"  value="{{isset($old_info->start_time) &&  $old_info->start_time ? date('Y-m-d',$old_info->start_time) : ''}}">
                    <span class="Start_Time_img start_date form_date"><img src="{{asset('staff/style/img/time1_03.png')}}"></span>

                    <input type="text" name="start_time" class="hunterTimePicker" value="{{isset($old_info->start_time) &&  $old_info->start_time ? date('H:i',$old_info->start_time) : ''}}" placeholder="时间" >
                    <span class="Start_Time_img start_time"><img src="{{asset('staff/style/img/time2_03.png')}}"></span>
                </div>
            </div>
            <div class="Selection_Time">
                <div class="Start_Time clear">
                    <input type="text" name="end_date" class="end_date"  value="{{isset($old_info->end_time) && $old_info->end_time ? date('Y-m-d',$old_info->end_time) : ''}}" placeholder="结束日期" >
                    <span class="Start_Time_img end_date form_date"><img src="{{asset('staff/style/img/time1_03.png')}}"></span>

                    <input type="text" name="end_time" class="hunterTimePicker" value="{{isset($old_info->end_time) && $old_info->end_time ? date('H:i',$old_info->end_time) : ''}}" placeholder="时间" >
                    <span class="Start_Time_img end_time"><img src="{{asset('staff/style/img/time2_03.png')}}" readonly></span>
                </div>
            </div>
            <div class="my_textarea">
                <textarea name="desc" id="lf_textarea" cols="" rows="" placeholder="备注">{{$old_info->user_status_desc or ''}}</textarea>
            </div>
            <input type="submit" value="保存" id="Save_btn">
        </form>
    </div>
    {{--<script src="{{asset('org/datePicker/js/datePicker.js')}}"></script>--}}
    {{--日期插件--}}
    <script type="text/javascript" src="{{asset('org/laydate/laydate.js')}}"></script>
    {{--时间插件--}}
    <link rel="stylesheet" href="{{asset('org/TimePicker/css/timePicker.css')}}">
    <script type="text/javascript" src="{{asset('org/TimePicker/js/timepicker.js')}}"></script>
    <script>
        $(function(){
//            //开始日期、结束日期
            laydate.render({
                elem: ".start_date" //指定元素
            });
            laydate.render({
                elem: ".end_date" //指定元素
            });
            //开始时间、结束时间
            $(".hunterTimePicker").hunterTimePicker();


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
        })

        function submitHandler(){
            var start_time = $("input[name='start_date']").val()+$("input[name='start_time']").val();
            var end_time = $("input[name='end_date']").val()+$("input[name='end_time']").val();
            var status_id = $("select[name='status_id']").val();
            var user_status_id = $("input[name='user_status_id']").val();
            var desc = $.trim($("textarea[name='desc']").val());
            var user_id = '{{session('staff')['user_id']}}';
            $.ajax({
                type: 'post',
                url: '{{url('staff/addstatus')}}',
                dataType: 'JSON',
                beforeSubmit: function(){
                    layer('提交中...');
                },
                data:{
                    '_token': '{{csrf_token()}}',
                    'user_id': user_id,
                    'start_time': start_time,
                    'end_time': end_time,
                    'status_id': status_id,
                    'user_status_id': user_status_id,
                    'desc': desc
                },success: function(data){
                    layer.msg(data.msg);
                    if(data.status == 200){
                        location.href = '{{url('staff/refresh')}}';
                        {{--setTimeout(function(){--}}
                            {{--location.href = '{{url('staff/refresh')}}';--}}
                        {{--},3000);--}}
                    }
                },error: function(){
                    layer.msg('系统错误，请刷新后重试！');
                }
            })
        }
    </script>
@endsection