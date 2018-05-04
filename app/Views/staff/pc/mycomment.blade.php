@extends('staff.pc.header')
@section('content')
    <title>修改我的留言</title>

    <div class="warp_2">
        <div class="title_g">
            <a class="return center" href="{{url('staff/refresh')}}"><img src="../staff/style/img/return_03.png"></a>
            <h5 class="tetle_font">我的留言</h5>
            <a class="Reserved"></a>
        </div>
        <form action="" id="edit_form">
            <input name="user_phone" type="hidden" value="{{$data['user_phone']}}">
            <div class="my_textarea">
                <textarea name="user_comments" id="lf_textarea" cols="" rows="" placeholder="我的留言">{{$data['user_comments']}}</textarea>
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
        })
        function submitHandler(){
            var user_phone = "{{$data['user_phone']}}";
            $.ajax({
                type: 'post',
                url: "{{url('staff/edit_comments')}}",
                dataType: 'JSON',
                beforeSubmit: function(){
                    layer('提交中...');
                },
                data:{
                    '_token': '{{csrf_token()}}',
                    'user_phone': user_phone,
                    'user_comments': $("textarea[name='user_comments']").val()
                },success: function(data){
                    layer.msg(data.msg);
                    if(data.status == 200){
                        location.href = "{{url('staff/refresh')}}";
//                        setTimeout(function(){
//                            location.href = '{{url('staff/refresh')}}';
//                        },3000);
                    }
                },error: function(){
                    layer.msg('系统错误，请刷新后重试！');
                }
            })
        }
    </script>
@endsection