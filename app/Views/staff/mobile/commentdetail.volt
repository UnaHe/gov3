{% extends "mobile/header.volt" %}

{% block content %}

    <title>留言详情</title>
    <div class="warp_2">
        <div class="message_title clear">
            <span>{{ comment['comment_name'] }}</span>
            <span>{{ comment['created_time'] }}</span>
        </div>
        <div class="message_con">
            {{ comment['comment_content'] }}
        </div>
        <div class="im_act3">
            <input type="button" style="background:{{ comment['comment_status'] == 0 ? '#EA5353' : '#ccc' }}" value="{{comment['comment_status'] == 0  ? '未处理' : '已处理'}}" class="gg_btn">
            <span class="my_tel">
                <a class="tel2" href="tel:{{comment['comment_phone']}}">{{comment['comment_phone']}}</a>
                <span class="tel_img"><img src="{{asset('staff/style/img/tel5_03.png')}}"></span>
            </span>
        </div>
    </div>
    <script>
        $(function () {
            $('.gg_btn').click(function () {
                var comment_id = '{{comment['comment_id']}}';
                var status = '{{comment['comment_status']}}' == 1 ? false : true;
                if(status){
                    layer.confirm('确定要处理此留言吗？', {
                        btn: ['确定', '取消'] //按钮
                    }, function () {
                        $.ajax({
                            type: 'POST',
                            dataType: 'JSON',
                            url: '{{url('staff/changecommentstatus?comment_id=')}}'+comment_id,
                            beforeSubmit: function () {
                                layer('提交中...');
                            },
                            data: {
                                "{{ _csrfKey }}": "{{ _csrf }}",
                            }, success: function (data) {
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
                            }, error: function () {
                                layer.msg('系统错误，请刷新后重试！', {icon: 2});
                            }
                        })
                    });
                }else{
                    layer.msg('你已处理此留言!');
                    {#layer.confirm('你已处理此留言，点击确定返回', {#}
                        {#btn: ['确定', '取消'] //按钮#}
                    {#}, function () {#}
                        {#location.href = '{{url('staff/refresh')}}';#}
                    {#});#}
                }
            })
        })
    </script>

{% endblock %}