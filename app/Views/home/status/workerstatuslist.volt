{% extends "layout/header.volt" %}

{% block content %}

    <style>
        .Department_title{
            margin-top: 10px;
        }
        .dropload-up{
            margin-top: 50px;
        }
    </style>
    <title>工作状态列表</title>
    <div class="wrap">
        <div class="title_g">
            <a class="return center" href="{{ url('status/index?pid=' ~ title_info.project.project_id ~ "&did=" ~ title_info.department_id) }}">
                <img src="{{ url('home/style/img/return_03.png') }}" />
            </a>
            <h5 class="tetle_font">{{ title_info.department_name }}</h5>
            <a class="Reserved"></a>
            <div class="lf_BreadCrumbs">
                <span class="lf_BreadCrumbs_1" onclick="location.href='{{ url('notice?pid=' ~ title_info.project.project_id ~ "&did=" ~ title_info.department_id) }}'">
                    <img src="{{ url('home/style/img/jkl.png') }}">
                </span>
                <span class="lf_BreadCrumbs_2" onclick="location.href='{{ url('department/detail?pid=' ~ title_info.project.project_id ~ "&did=" ~ title_info.department_id) }}'">
                    <img src="{{ url('home/style/img/jko.png') }}">
                </span>
            </div>
        </div>
        <div class="main">
            {#<div class="Department_title">科室：{{$title_info->department_name}}</div>#}
            {#<div class="main_con">#}
                {#<ul style="overflow: hidden">#}
                {#</ul>#}
            {#</div>#}
        </div>
    </div>

    {{ stylesheet_link('home/style/css/style.css') }}
    {{ stylesheet_link('js/lib/dropload/dropload.css') }}
    {{ javascript_include('js/lib/dropload/dropload.min.js') }}
    <script>
    // 每页展示4个
    var page_size = 20;
    var page = 1;
    var have_data = true;
    var default_staff_img = '{{ _config['default_staff_img'] }}';
    var img_path = '{{ _config['upload_url'] }}';
    var flag = true;
    $(function () {
        // dropload
        $('.wrap').dropload({
            scrollArea : window,
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
                    url: "{{ url('status/ajaxWorkerStatusList?pid=' ~ title_info.project.project_id ~ '&did=' ~ title_info.department_id ~ '&page=1') }}",
                    data: {
                        "{{ _csrfKey }}": "{{ _csrf }}",
                    },
                    success: function (data) {
                        if(data.status == 200){
                            $('.main').empty();
                            if (!data.data) {
                                have_data = false;
                                me.resetload();
                                me.unlock();
                                me.noData(true);
                            }else{
                                package_li_list(data.data);
                                me.resetload();
                                me.unlock();
//                                me.noData(false);
                            }
                        }else{
                            layer.msg('服务器错误，请刷新后重试！', {icon: 5});
                        }
                    },
                    error: function () {
                        layer.msg('系统错误，请刷新后重试！', {icon: 2});
                        me.lock();
                        // 即使加载出错，也得重置
                        me.resetload();
                    }
                });
            },
            loadDownFn : function(me) {
                len = $('li').length * 1;
                if ((len % page_size == 0) && have_data && flag) {
                    flag = false;
                    $.ajax({
                        type: 'POST',
                        dataType: 'JSON',
                        url: "{{ url('status/ajaxWorkerStatusList?pid=' ~ title_info.project.project_id ~ '&did=' ~ title_info.department_id ~ '&page=') }}" + page,
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
                                    package_li_list(data.data);
                                    // 每次数据加载完，必须重置
                                    flag = true;
                                    me.unlock()
                                    me.resetload();
                                }
                            }else{
                                layer.msg('服务器错误，请刷新后重试！', {icon: 5});
                            }
                        },
                        error: function () {
                            layer.msg('系统错误，请刷新后重试！', {icon: 2});
                            flag = true;
                            me.lock()
                            // 即使加载出错，也得重置
//                            me.resetload();
                        }
                    });
                }else{
//                    me.unlock();
                    me.noData(true);
                    me.resetload();

                }
            },
            threshold: 100
        });
    });
    function detail(user_id){
        location.href = "{{ url('status/workerStatusDetail?user_id=') }}" + user_id + '&pid=' + '{{ project_id }}' + '&did=' + '{{ department_id }}' ;
    }

    function package_li_list(data){
        $.each(data, function (k, v) {
            var have_flag = false;
            var ul = '';

            if($(".main").find("div.list_"+k).length >0){
                have_flag = true;
            }

            if(!have_flag){
                var ul = '<div class="Department_title">部门：'+v.section_name+'</div>'+
                    '<div class="main_con list_'+k+'">'+
                    ' <ul style="overflow: hidden" class="ul_'+k+'">';
            }
            $.each(v.user_list,function(kk,vv){
                var img_src = vv.a.user_image ? img_path + vv.a.user_image : default_staff_img;
                ul += '<li>' +
                    '<span class="status" style="background-color:' + vv.status_color + '">' + vv.status_name + '</span>' +
                    '<div class="portrait" onclick="detail(' + vv.a.user_id + ')"><img src="' + img_src + '"></div>' +
                    '<span class="name text_no">' + vv.a.user_name + '</span>' +
                    '<p class="Duties text_no">' + (vv.a.user_job ? vv.a.user_job : '科员') + '</p>' +
                    '<span class="effect text_overflow">' + (vv.a.user_intro ? vv.a.user_intro : '') + '</span>' +
                    '</li>';
            });
            if(!have_flag){
                ul += '</ul></div>';
                $(".main").append(ul);
            }else{
                $(".main").find("div.list_"+k+" ul").append(ul);
            }

        });
    }
    </script>

{% endblock %}