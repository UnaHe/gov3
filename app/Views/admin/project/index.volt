{% extends "layout/main.volt" %}

{% block content %}
    <div class="crumb_warp">
        <i class="fa fa-home"></i> <a href="{{url('admin/home')}}">首页</a> &raquo; 单位列表
    </div>
    <!--面包屑导航 结束-->

    <!--搜索结果页面 列表 开始-->
    <form action="{{ url('/admin/project') }}" method="get">
        <div class="result_wrap">
            <!--快捷导航 开始-->
            <div class="search_wrap">
                {#<form action="" method="post">#}
                    <table class="search_tab">
                        <tr>
                            <th width="120">选择类型:</th>
                            <td>
                                <select id="statusselect" name="status">
                                    <option value="">请选择</option>
                                    <option value="1" {{ status is defined and status == 1 ? 'selected' : '' }}>正常状态单位</option>
                                    <option value="0" {{ status is defined and status == 0 ? 'selected' : '' }}>暂停单位</option>
                                </select>
                            </td>
                            <th width="70">关键字:</th>
                            <td>
                                <input type="text" id="keywords" name="keywords" placeholder="可搜索单位名或简介" value="{{ keywords is defined ? keywords : '' }}"><br>
                            </td>
                            <td><input type="submit" id="sub" class="btn btn-info" value="查询"></td>
                        </tr>
                    </table>
                {#</form>#}
            </div>
            <div class="result_content">
                <div class="short_wrap">
                    <a href="{{url('admin/project/create')}}"><i class="fa fa-plus"></i>添加单位</a>
                    <a href="{{url('admin/project')}}"><i class="fa fa-recycle"></i>全部单位</a>
                </div>
            </div>
            <!--快捷导航 结束-->
        </div>
    </form>
    <!--搜索结果页面 列表 结束-->
    {{ content() }}
    <p><?php $this->flashSession->output() ?></p>
    <div class="result_wrap">
        <div class="result_content">
            <table class="list_tab">
                <tr>
                    <th class="tc">ID</th>
                    <th>单位名称</th>
                    {#<th>单位简介</th>#}
                    <th>创建时间</th>
                    <th>当前状态</th>
                    <th>操作</th>
                </tr>
                {% for v in data.items %}
                    <tr>
                        <td class="tc">{{v.project_id}}</td>
                        <td>
                            <a href="#" onclick="showdetail({{v.project_id}})">{{v.project_name}}</a>
                        </td>

                        {#<td>#}
                            {#{!! $v->project_profile !!}#}
                            {#<a href="#" onclick="showdetail({{$v->project_id}})">#}
                                {#{{str_limit($v->project_profile,'50','... ...')}}#}
                            {#</a>#}
                        {#</td>#}

                        <td>{{ date("Y-m-d", v.created_at) }}</td>
                        <td>{{ v.project_status == 1 ? '开启' : '关闭' }}</td>
                        <td>
                            <a href="{{url('admin/project/' ~ v.project_id ~ '/edit')}}">修改</a>
                            {% if v.project_status == 1 %}
                                <a href="javascript:;" onclick="delArt({{v.project_id}})">关闭</a>
                            {% endif %}
                        </td>
                    </tr>
                {% endfor %}
            </table>

            <div class="page_list clear" >
                <label>共 {{ data.total_items }} 条记录</label>
                {% if data.total_pages > 1 %}
                <div style="float: right">
                    <ul class="paginate">
                        <li class="disabled"><span>总计: {{ data.total_pages }} 页</span></li>
                        <li class="active"><span>当前第: {{ data.current }} 页</span></li>
                        {% if status is defined or keywords is defined %}
                            {% if data.current == 1 %}
                                <li class="disabled"><span>第一页</span></li>
                            {% else %}
                                <li><a href="/admin/project?status={{ status is defined ? status : '' }}&keywords={{ keywords is defined ? keywords : '' }}&page=1">第一页</a></li>
                            {% endif %}
                            {% if data.current == 1 %}
                                <li class="disabled"><span>上一页</span></li>
                            {% else %}
                                <li><a href="/admin/project?status={{ status is defined ? status : '' }}&keywords={{ keywords is defined ? keywords : '' }}&page={{ data.before }}">上一页</a></li>
                            {% endif %}
                            {% if data.current == data.last or data.last == 0 %}
                                <li class="disabled"><span>下一页</span></li>
                            {% else %}
                                <li><a href="/admin/project?status={{ status is defined ? status : '' }}&keywords={{ keywords is defined ? keywords : '' }}&page={{ data.next }}">下一页</a></li>
                            {% endif %}
                            {% if data.current == data.last or data.last == 0 %}
                                <li class="disabled"><span>最后一页</span></li>
                            {% else %}
                                <li><a href="/admin/project?status={{ status is defined ? status : '' }}&keywords={{ keywords is defined ? keywords : '' }}&page={{ data.last }}">最后一页</a></li>
                            {% endif %}
                        {% else %}
                            {% if data.current == 1 %}
                                <li class="disabled"><span>第一页</span></li>
                            {% else %}
                                <li><a href="/admin/project">第一页</a></li>
                            {% endif %}
                            {% if data.current == 1 %}
                                <li class="disabled"><span>上一页</span></li>
                            {% else %}
                                <li><a href="/admin/project?page={{ data.before }}">上一页</a></li>
                            {% endif %}
                            {% if data.current == data.last or data.last == 0 %}
                                <li class="disabled"><span>下一页</span></li>
                            {% else %}
                                <li><a href="/admin/project?page={{ data.next }}">下一页</a></li>
                            {% endif %}
                            {% if data.current == data.last or data.last == 0 %}
                                <li class="disabled"><span>最后一页</span></li>
                            {% else %}
                                <li><a href="/admin/project?page={{ data.last }}">最后一页</a></li>
                            {% endif %}
                        {% endif %}
                    </ul>
                </div>
                {% endif %}
            </div>

        </div>
    </div>

    <style>
        .result_content ul li span {
            font-size: 15px;
            padding: 6px 12px;
        }
        .active {
            color: #fff;
            cursor: default;
            background-color: #337ab7;
            border-color: #337ab7;
        }
        .disabled {
            color: #777;
            cursor: not-allowed;
            background-color: #fff;
            border-color: #ddd;
        }
    </style>

    <script>
        // 状态索引
        $('#statusselect').change(function () {
            var status = $(this).find("option:selected").val();
            var keywords = $('#keywords').val();
            if(status == '' || status > 1){
                location.href = '/admin/project';
            }else{
                location.href = '/admin/project?status=' + status + '&keywords=' + keywords;
            }
        });

        // 关键字索引
        // $('#sub').click(function () {
        //     var words = $('#keywords').val();
        //     var status = $('#statusselect').find("option:selected").val();
        //     words = words ? words : '';
        //     status = status ? status : '';
        //     getListByKeys(words, status);
        // });
        // function getListByKeys(words, status){
        //     if (words) {
        //         location.href = '/admin/project/keywords/' + words + '/' + status;
        //     } else {
        //         layer.msg('请输入要查询的关键字',{icon: 5});
        //     }
        // }

        //删除单位
        function delArt(project_id) {
            layer.confirm('确定要暂停此项目吗？', {
                btn: ['确定','取消']
            },function(){
                $.ajax({
                    url: '{{url('admin/project/delete')}}',
                    type: "POST",
                    dataType: 'JSON',
                    data: {
                        'project_id': project_id,
                        "{{ _csrfKey }}": "{{ _csrf }}",
                    },
                    success:function(data){
                        if(data.status == 201) {
                            layer.msg(data.msg, {
                                icon: 6,
                                time: 2000, //2s后自动关闭
                            },function (){
                                location.reload();
                            });
                        }else{
                            layer.msg(data.msg, {icon: 5});
                        }
                    },
                    error:function(){
                        layer.msg('操作失败，请稍后重试！', {icon: 2});
                    }
                })
            });
        }

        // 显示详情
        function showdetail(project_id) {
            $.ajax({
                url: '{{url('admin/project/show')}}',
                type: "POST",
                dataType: 'JSON',
                data: {
                    'project_id': project_id,
                    "{{ _csrfKey }}": "{{ _csrf }}",
                },
                success: function(data){
                    if(data.status == 200){
                        data.msg = data.msg == null ? '' : data.msg;
                        var content = '<div style="padding: 10px">' + data.msg + '</div>';
                        layer.open({
                            title:'详情',
                            type: 1,
                            skin: 'layui-layer-rim', //加上边框
                            area: ['720px', '500px'], //宽高
                            content: content
                        });
                    }else{
                        layer.msg(data.msg, {icon: 5});
                    }
                },
                error: function() {
                    layer.msg('操作失败，请稍后重试！', {icon: 2});
                }
            });
        }
    </script>

{% endblock %}