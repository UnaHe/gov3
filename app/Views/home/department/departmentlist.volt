{% extends "layout/header.volt" %}

{% block content %}

    <title>科室列表</title>
    <div class="wrap">
        <div class="title_g">
            <a class="return center" href="{{ url('status/index?pid=' ~ project_id ~ '&did=' ~ department_id) }}">
                <img src="{{ url('home/style/img/return_03.png') }}" />
            </a>
            <h5 class="tetle_font">科室列表</h5>
            <a class="Reserved"></a>
        </div>
        <div class="main">
            <div class="classList">
                <ul class="list">
                    {#<li>保卫科</li>#}
                </ul>
            </div>
        </div>
    </div>

    <script>
        var url = '{{ url('department/ajaxDepartmentList?pid=' ~ project_id ~ '&did=' ~ department_id) }}';
        $(function () {
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
                        url: url,
                        type: 'POST',
                        dataType: 'JSON',
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
                                    var result = ul_data(data.data);
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
                        }
                    });
                },
                loadDownFn: function (me) {
                    var flag = true;
                    len = $('.list li').length * 1;
                    if ((len % page_size == 0) && have_data && flag) {
                        flag = false;
                        $.ajax({
                            url: url,
                            type: 'POST',
                            dataType: 'JSON',
                            data: {
                                "{{ _csrfKey }}": "{{ _csrf }}",
                                page: page
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
                                        var result = ul_data(data.data);
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
        function ul_data(data){
            var result = '';
            var link = '';
            $.each(data, function (k, v) {
                result += '<li onclick="show_detail('+v.project_id+','+v.department_id+')">'+ v.department_name + '</li>';
            });
            return result;
        }
        function show_detail(project_id, department_id){
            console.log(department_id);
            location.href = '/status/workerStatusList?&pid='+project_id+'&did='+department_id ;
        }
    </script>

{% endblock %}