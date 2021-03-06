{% extends "mobile/header.volt" %}

{% block content %}

    <style>
        .mypie {
            width: 200px;
            height: 200px;
            float: left;
        }
        .pie_list {
            float: left;
            margin-left: 20px;
        }
        .color_li {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 100%;
            background: #df4a4a
        }
        body {
            background: #fff !important;
        }
    </style>
    <title>统计</title>
    <div id="wrapper">
        <div class="wrap" id="wrap">
            <div class="main">
                {#<div id="my_status" style="width: 50%;height:300px;float: left;"></div>#}
                {#<div id="test" style="width: 100%;height:200px;"></div>#}

                <ul class="canvssssUl hide">
                    <li class="canvssssUl_active">我的留言</li>
                    <li>下属状态</li>
                    <li>下属留言</li>
                </ul>
                <div class="tt_all"></div>

                <div class="pie_box">
                    <div class="list_li_pies pie_li_show">
                        <div class="pie_li_mycomment">
                            <div id="my_comment" class="mypie"></div>
                            <div class="pie_list">
                                <ul>
                                    <li>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="tt_all_ul">
                        </div>
                        <div class="detail_ul_1">
                        </div>

                        <div class="tt_all1"></div>
                    </div>
                    <div class="list_li_pies pie_li_hide">
                        <div class="pie_li_status hide">
                            <div id="status" class="mypie"></div>
                            <div class="pie_list">
                                <ul class="pie_ul">
                                </ul>
                            </div>
                        </div>

                        <div class="tt_all_ul">
                        </div>
                        <div class="detail_ul_2">
                        </div>

                        <div class="tt_all1"></div>
                    </div>
                    <div class="list_li_pies pie_li_hide">
                        <div class="pie_li_comments hide">
                            <div id="comments" class="mypie"></div>
                            <div class="pie_list">
                                <ul class="pie_ul">
                                    <li>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="tt_all_ul">
                        </div>
                        <div class="detail_ul_3">
                        </div>

                        <div class="tt_all1"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {#    <script type="text/javascript" src="{{asset('org/echart/js/echarts.simple.min.js')}}"></script>#}
    {{ javascript_include('org/echart/js/echarts.js') }}
    {#    <script type="text/javascript" src="{{asset('org/echart/js/echarts.min.js')}}"></script>#}
    <script>
        //        test();
        var colors = ['#1F73C2', '#BEE5E9', '#AE5DE6', '#FFDE8D', '#F37216', '#13D06A', '#D90A79', '#A9C921', '#EF232E', '#03AAB0'];
        $(function () {
//            var myStatusContainer = document.getElementById('my_status');
            var myCommentContainer = document.getElementById('my_comment');

//            var myStatusChart = echarts.init(myStatusContainer);
            var myCommentChart = echarts.init(myCommentContainer);

            var public_option = {
//                title: {
//                    show: true,
//                    text: '我的留言',
//                    textStyle: {
//                        fontSize: 12,
//                        fontWeight: 'bolder',
//                        color: '#333'
//                    },
//                    x: 'center',
//                    y: 'center',
//                },
                color: colors,
                calculable: true,
                series: [
                    {
                        name: '我的状态',
                        type: 'pie',
                        radius: ['30%', '50%'],
                        clickable: false,
                        avoidLabelOverlap: false,
                        label: {
                            normal: {
                                show: true,
                                position: 'center',
                                formatter:function(){
                                    return "我的\n\n留言";
                                },
                                textStyle:{
                                    color:'black'
                                }
                            },
                            emphasis: {
                                show: false,
                                textStyle: {
                                    fontSize: '10'
                                }
                            }
                        },
                        labelLine: {
                            normal: {
                                show: false,
                                position: 'center',
                                length: 0.1
                            }
                        },
                        data: []
                    }
                ]
            };

            var my_comment_option = {
                series: {
                    label: {
                        normal: {
                            formatter:function(){
                                return "我的\n\n留言";
                            }
                        }
                    }
                }
            };
            var status_option = {
                series: {
                    label: {
                        normal: {
                            formatter:function(){
                                return "下属\n\n状态";
                            }
                        }
                    }
                }
            };
            var comments_option = {
                series: {
                    label: {
                        normal: {
                            formatter:function(){
                                return "下属\n\n留言";
                            }
                        }
                    }
                }
            };
            // 使用刚指定的配置项和数据显示图表。
//            myStatusChart.setOption(public_option);
            myCommentChart.setOption(public_option);


            //请求数据
            get_my_data();

            //请求数据方法
            function get_my_data() {
//                myStatusChart.showLoading();
                myCommentChart.showLoading();
//                myStatusChart.setOption(my_status_option);
                myCommentChart.setOption(my_comment_option);
                $.ajax({
                    type: 'POST',
                    dataType: 'JSON',
                    url: '{{url('staff/mycount')}}',
                    data: {
                        "{{ _csrfKey }}": "{{ _csrf }}",
                    },
                    success: function (data) {
                        if (data.status == 200) {
                            var my_comment;
                            //组装我的状态的数据
                            my_comment = package_comment(data.data.my_comments);
                            add_data_to_pie(myCommentChart, my_comment);
                            add_pie_list($("#my_comment"),data.data.my_comments,1);

                            //有下属表
                            if (data.data.belongs) {
                                $(".canvssssUl").removeClass('hide');
                                $('.pie_box .hide').removeClass('hide');
                                var StatusContainer = document.getElementById('status');
                                var CommentContainer = document.getElementById('comments');
                                var StatusChart = echarts.init(StatusContainer);
                                var CommentChart = echarts.init(CommentContainer);

                                StatusChart.setOption(public_option);
                                CommentChart.setOption(public_option);

                                StatusChart.showLoading();
                                CommentChart.showLoading();
                                StatusChart.setOption(status_option);
                                CommentChart.setOption(comments_option);

                                var status, comment;

                                status = package_status(data.data.today_belong_status_list);
                                add_data_to_pie(StatusChart, status);
                                add_pie_list($("#status"),data.data.today_belong_status_list,2);
//                                testChart.setOption(option);

                                comment = package_comment(data.data.my_belong_comments);
                                add_data_to_pie(CommentChart, comment);
                                add_pie_list($("#comments"),data.data.my_belong_comments,3);
                            } else {
                                if(StatusChart && CommentChart){
                                    StatusChart.clear();
                                    CommentChart.clear();
                                }
                            }
                        } else {
                            layer.msg('数据加载失败，请刷新后重试！', {icon: 5});
                        }
                    },
                    error: function () {
                        layer.msg('数据加载失败，请刷新后重试！', {icon: 2});
//                        myStatusChart.hideLoading();
                        myCommentChart.hideLoading();
                    }
                })
            }

            //组装状态的数据
            function package_status(status_list) {
                var my_status = [];
                $.each(status_list, function (k, v) {
                    my_status.push({
//                        itemStyle:{
//                            normal:{color:v.status_color}
//                        },
                        name: v.name,
                        value: v.value

                    });
                });
                return my_status;
            }

            //组装留言数据
            function package_comment(comment_list) {
                var my_comment = [];
                $.each(comment_list, function (k, v) {
                    my_comment.push({
                        name: v.name,
                        value: v.value
                    });
                });
                return my_comment;
            }

            //加载数据到图标
            function add_data_to_pie(obj, data) {
                obj.hideLoading();    //隐藏加载动画
                if (data.length > 0) {
                    obj.setOption({        //加载数据图表
                        series: [{
                            data: data,
                        }]
                    });
                } else {
                    var option = obj.getOption();
                    var name = option.title[0].text;
                    layer.msg(name + '暂无统计数据！');
                }
            }

        //
            function add_pie_list(obj, data_list, type){
                var str = str1 = "";

                $.each(data_list,function (k,v) {

                    var link = percent = zl_img = '';
                    if(v.value > 0 && v.percent > 0 && v.status_id >= 0){
//                        console.log(v.status_id);
                        link = type%2 ? "location.href = '/staff/countcommentdetail?type="+type+"&status_id="+v.status_id +"'": "location.href = '/staff/countstatusdetail?type="+type+"&status_id="+v.status_id+"'";
                    }
                    percent = v.percent+'%';

                    zl_img = !!link ? '<img src="{{ url('staff/style/img/zl.png') }}" mode="widthFix" class="img_lf">' : null;
                    str += '<li>'+
                        '<span class="color_li" style="background:'+colors[k]+'"></span>'+
                        '<span class="color_value">'+v.name+'</span>'+
                        '</li>';
                    str1 += '<div class="tt_all1"></div><ul class="ul_detail" onclick="'+link+'">' +
                        '<li >'+
                        '<a class="ul_detail_a">'+v.name+
                        '</a>' +
                        zl_img+
                        '<span class="bfb">'+percent+'</span>'+
                        '</li>' +
                        '</ul>';
                });
                $(obj).next('div.pie_list').find('ul').html(str);
                $(".detail_ul_"+type).html(str1);
            }
        });
		
		$('.canvssssUl li').bind('click',function(){
			$('.canvssssUl li').removeClass('canvssssUl_active');
            $(this).addClass('canvssssUl_active');
            var _index=$(this).index();
            $('.pie_box .list_li_pies').hide();

            $('.pie_box .list_li_pies').eq(_index).show();
		})
    </script>

{% endblock %}