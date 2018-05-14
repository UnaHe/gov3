{% extends "layout/header.volt" %}

{% block content %}

    <title>公告列表</title>
    <div class="wrap">
        <div class="title_g">
            <a class="return center" href="{{'/status/workerStatusList?pid=' ~ project_id ~ '&did=' ~ department_id}}">
                <img src="{{ url('home/style/img/return_03.png') }}" />
            </a>
            <h5 class="tetle_font">告示</h5>
            <a class="Reserved"></a>
        </div>
        <div class="main">
            <div class="bulletin">
                <ul class="list" style="overflow: hidden">
                    {#<li>#}
                        {#<span class="bulletin_time">2017/12/25</span>#}
                        {#<span class="bulletin_font text_overflow">李克强对推进工业产品许可证制度改革现场交流会作出重要批示</span>#}
                    {#</li>#}
                    {#<li>#}
                        {#<span class="bulletin_time">2017/12/25</span>#}
                        {#<span class="bulletin_font text_overflow">李克强对推进工业产品许可证制</span>#}
                    {#</li>#}
                </ul>
            </div>
        </div>
    </div>

    <script>
        $(function () {
            var counter = 0;
            // 每页展示4个
            var page_size = 20;
            var page = 1;
            var have_data = true;

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
                    page = 2;
                    have_data = true;
                    $.ajax({
                        type: 'POST',
                        dataType: 'JSON',
                        url: "{{ url('notice/ajaxIndex?pid=' ~ project_id ~ '&did=' ~ department_id ~ '&page=1') }}",
                        data: {
                            "{{ _csrfKey }}": "{{ _csrf }}",
                        },
                        success: function (data) {
                            if(data.status == 200){
                                $('.list').empty();
                                if (!data.data) {
                                    have_data = false;
                                    me.resetload();
                                    me.unlock();
                                    me.noData(true);
                                }else{
                                    var result = '';
                                    $.each(data.data, function (k, v) {
                                        result += '<li>'+
                                            '<span class="bulletin_time">'+ v.created_at+'</span>'+
                                            '<span class="bulletin_font text_overflow" onclick="show_detail('+v.notice_id+')">' +
                                            v.notice_title+'</span>'+
                                            '</li>';
                                        {#result += '<li>' +#}
                                        {#'<span style="margin-right: 50px">' +#}
                                        {#v.created_time +#}
                                        {#'</span>' +#}
                                        {#'<a class="" href="{{url('notice/detail?notice_id=')}}' + v.notice_id + '">' +#}
                                        {#v.notice_title +#}
                                        {#'</a>' +#}
                                        {#'</li>';#}
                                    });
                                    $('.list').html(result);
                                    me.resetload();
                                    me.unlock();
                                    me.noData(false);
                                }
                            }else{
                                layer.msg('服务器错误，请刷新后重试！', {icon: 5});
                            }
                        },
                        error: function () {
                            layer.msg('系统错误，请刷新后重试！', {icon: 2});
                            // 即使加载出错，也得重置
//                            me.resetload();
                        }
                    });
                },
                loadDownFn: function (me) {
                    var flag = true;
                    len = $('.list li').length * 1;
                    if ((len % page_size == 0) && have_data && flag) {
                        flag = false;
                        $.ajax({
                            type: 'POST',
                            dataType: 'JSON',
                            url: "{{ url('notice/ajaxIndex?pid=' ~ project_id ~ '&did=' ~ department_id ~ '&page=') }}" + page,
                            data: {
                                "{{ _csrfKey }}": "{{ _csrf }}",
                            },
                            success: function (data) {
                                if(data.status == 200) {
                                    page++;
                                    if (!data.data) {
                                        have_data = false;
                                        me.noData(true);
                                        me.resetload();
                                        me.unlock();
                                        return false;
                                    }else{
                                        var result = '';
                                        $.each(data.data, function (k, v) {
                                            result += '<li>'+
                                                '<span class="bulletin_time">'+ v.created_at+'</span>'+
                                                '<span class="bulletin_font text_overflow" onclick="show_detail('+v.notice_id+')">' +
                                                v.notice_title+'</span>'+
                                                '</li>';
                                        });
                                        $('.list').append(result);
                                        // 每次数据加载完，必须重置
                                        me.resetload();
                                    }
                                }else{
                                    layer.msg('服务器错误，请刷新后重试！', {icon: 5});
                                }
                            },
                            error: function () {
                                layer.msg('系统错误，请刷新后重试！', {icon: 2});
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
        function show_detail(notice_id){
            location.href = "{{ url('notice/detail?notice_id=') }}" + notice_id + '&pid=' + '{{ project_id }}' + '&did=' + '{{ department_id }}' ;
        }
    </script>

{% endblock %}