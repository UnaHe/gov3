{% extends "mobile/header.volt" %}

{% block content %}

    <style type="text/css">
        #wrapper {
            width: 100%;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            overflow: auto;
        }
        #scroller {
            position: absolute;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
            width: 100%;
            padding: 0;
        }
        /*下拉加载*/
        #pullDown {
            margin-top: 0;
            width: 100%;
            text-align: center;
        }
        .pullDownLabel{
            margin: 20px auto;
            text-align: center;
            font-size: 14px;
            /*color: #37bbf5;*/
        }
        .my_count{
            width: 1.45rem;
            height: 0.64rem;
            /*position: absolute;*/
            top: 0.22rem;
            right: 0.2rem;
            border-radius: 2px;
            overflow: hidden;
        }
    </style>
    <title>用户列表</title>
    <div id="wrapper">
        <div id="scroller">
            <div class="wrap" id="wrap">
                <div id="pullDown" class="loading">
                    <label class="pullDownLabel flip"></label>
                </div>
                <div class="main">
                    <div class="personnel_info clear">
                        <div class="personnel_info_img"><img src="{{ data['user_image'] }}"></div>
                        <div class="personnel_info_font y_personnel_info_font">
                            <span class="PersonnelName y_PersonnelName">{{ data['user_name'] }}</span>
                            <span class="PersonnelDuties">{{ data['user_job'] is defined ? data['user_job'] : '科员' }}</span>
                            <span class="PersonnelRole text_overflow">{{ data['user_intro'] is defined ? data['user_intro'] : '无个人简介' }}</span>
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
                              style="background:{{ data['nowstatus']['status_color'] }}">{{ data['nowstatus']['status_name'] }}</span>
                            </div>
                            <div class="y_time_con">
                                <p class="beginning"><a>始</a>&nbsp;&nbsp;<a>{{ date('Y/m/d H:i', data['nowstatus']['start_time']) }}</a></p>
                                <p class="expiry"><a>终</a>&nbsp;&nbsp;<a>{{ date('Y/m/d H:i', data['nowstatus']['end_time']) }}</a></p>
                            </div>
                            {#<div class="y_shutdown center"><img src="../staff/style/img/y_shutdown_03.png"></div>#}
                        </div>
                    </div>
                    <div class="CurrentState y_CurrentState">
                        <div class="CurrentState_title y_CurrentState_title">
                            <span class="ghost"></span>
                            <span class="CurrentState_title_font">计划列表</span>
                            {#<span class="CurrentState_d CurrentState_e" onclick="edit_user_status(0)"><img#}
                                        {#src="../staff/style/img/xz1.png"></span>#}
                        </div>
                        {% if data['statuslist'] is not empty %}
                            {% for v in data['statuslist'] %}
                                <div class="y_time clear">
                                    <div class="y_time_left center edit_status" data-status="{{ v['user_status_id'] }}">
                                <span class="y_xiuxi no_status"
                                      style="background:{{ v['status_color'] }}">{{ v['status_name'] }}</span>
                                    </div>
                                    <div class="y_time_con edit_status" data-status="{{ v['user_status_id'] }}">
                                        <p class="beginning"><a>始</a>&nbsp;&nbsp;<a>{{ date('Y/m/d H:i', v['start_time']) }}</a></p>
                                        <p class="expiry"><a>终</a>&nbsp;&nbsp;<a>{{ date('Y/m/d H:i', v['end_time']) }}</a></p>
                                    </div>
                                </div>
                            {% endfor %}
                        {% endif %}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function () {
//            $(".edit_status").click(function () {
//                var user_status_id = $(this).data('status');
//                edit_user_status(user_status_id);
//            })

        });
        {#//编辑计划#}
        {#function edit_user_status(user_status_id) {#}
            {#location.href = '{{url('staff/addstatus?user_status_id=')}}' + user_status_id;#}
        {#}#}
        {#//删除#}
        {#function del_user_status(user_status_id, status_name) {#}
            {#layer.confirm('确实要删除  ' + status_name + '  计划吗？', {#}
                {#btn: ['确定', '取消'] //按钮#}
            {#}, function () {#}
                {#$.ajax({#}
                    {#type: 'post',#}
                    {#url: '{{url('staff/delstatus')}}',#}
                    {#dataType: 'JSON',#}
                    {#beforeSubmit: function () {#}
                        {#layer('提交中...');#}
                    {#},#}
                    {#data: {#}
                        {#'_token': '{{csrf_token()}}',#}
                        {#'user_status_id': user_status_id#}
                    {#}, success: function (data) {#}
                        {#if (data.status == 200) {#}
                            {#layer.msg(data.msg);#}
                            {#setTimeout(function () {#}
                                {#location.href = '{{url('staff/refresh')}}';#}
                            {#}, 3000);#}
                        {#} else {#}
                            {#layer.msg(data.msg);#}
                        {#}#}
                    {#}, error: function () {#}
                        {#layer.msg('系统错误，请刷新后重试！');#}
                    {#}#}
                {#})#}
            {#});#}
        {#}#}
    </script>
    {{ javascript_include('js/lib/iScroll/iscroll.js') }}
    <script>
        function loaded() {
//            var myScroll = new iScroll("wrapper");
            pullDownEl = document.getElementById('pullDown');
            pullDownOffset = pullDownEl.offsetHeight;
            // console.log(pullDownEl.querySelector('.pullDownLabel'));
            myScroll = new iScroll('wrapper', {
                scrollbarClass: 'myScrollbar',
                useTransition: false,
                topOffset: pullDownOffset,
                onRefresh: function () {
//                    if (pullDownEl.className.match('loading')) {
//                        pullDownEl.className = '';
//                        pullDownEl.querySelector('.pullDownLabel').innerHTML = '下拉刷新...';
//                    }
                },
                onScrollMove: function () {
                    if (this.y > 5 && !pullDownEl.className.match('flip')) {
                        pullDownEl.className = 'flip';
                        pullDownEl.querySelector('.pullDownLabel').innerHTML = '松手开始更新...';
                        this.minScrollY = 0;
                    } else if (this.y < 5 && pullDownEl.className.match('flip')) {
                        pullDownEl.className = '';
                        pullDownEl.querySelector('.pullDownLabel').innerHTML = '下拉刷新...';
                        this.minScrollY = -pullDownOffset;
                    }
                },
                onScrollEnd: function () {
                    if (pullDownEl.className.match('flip')) {
                        pullDownEl.className = 'loading';
                        pullDownEl.querySelector('.pullDownLabel').innerHTML = '加载中...';
                        pullDownAction();   // ajax call
                    }
                }
            });

            setTimeout(function () {
                document.getElementById('wrapper').style.left = '0';
            }, 800);

        }
        document.addEventListener('DOMContentLoaded', loaded, false);

        var myScroll,
            pullDownEl, pullDownOffset;
        /**
         * 下拉刷新 （自定义实现此方法）
         * myScroll.refresh(); 数据加载完成后，调用界面更新方法
         */
        function pullDownAction() {
            setTimeout(function () {
                var new_time = ((new Date()).getTime()), myhref = location.href;
                if (myhref.indexOf("&time") !== -1) {
                    myhref = myhref.substr(0, myhref.indexOf("&time"));
                }
                window.location.href = myhref + '&time=' + new_time;
            }, 600);
        }

    </script>
@endsection