{% extends "layout/main.volt" %}
{% block content %}

    <!--面包屑导航 开始-->
    <div class="crumb_warp">
        <!--<i class="fa fa-bell"></i> 欢迎使用登陆网站后台，建站的首选工具。-->
        <i class="fa fa-home"></i> <a href="{{url('admin/home')}}">首页</a> &raquo; <a href="#">留言管理</a> &raquo; 留言列表
    </div>
    <!--面包屑导航 结束-->

    <!--结果页快捷搜索框 开始-->
    <div class="search_wrap">
        <form action="{{ url('admin/comment') }}" method="get" name="search_form">
            <table class="search_tab">
                <tr>
                    {# 载入单位、部门、科室的查询  #}
                    {% Include 'layout/search_list1' with ['type': 1] %}
                    <th width="120">留言状态:</th>
                    <td>
                        <select id="statusselect" name="status">
                            <option value="">请选择</option>
                            <option value="1" {{( input['status'] is not empty and input['status'] == 1 ) ? 'selected' : null}}>
                                已处理
                            </option>
                            <option value="0" {{( input['status'] is defined and input['status'] == 0 ) ? 'selected' : null}}>
                                未处理
                            </option>
                        </select>
                    </td>
                    <th width="70">关键字:</th>
                    <td>
                        <input type="text" id="key_words" name="key_words" placeholder="可搜索姓名，内容，电话"
                               value="{{ input['key_words'] is not empty ? input['key_words'] : null}}"><br>
                    </td>
                    <td><input type="submit" id="sub" class="btn btn-info" value="查询"></td>
                </tr>
            </table>
        </form>
    </div>
    <!--结果页快捷搜索框 结束-->

    <!--搜索结果页面 列表 开始-->
    <form action="#" method="post">
        {% if _session['user_is_super'] or _session['user_is_admin'] %}
            <div class="result_wrap">
                <!--快捷导航 开始-->
                <div class="result_content">
                    <div class="short_wrap">
                        <a id="allhandle"><i class="fa fa-plus"></i>批量操作</a>
                        <a id="alldelete"><i class="fa fa-recycle"></i>批量删除</a>
                        <a href="{{ url('admin/comment') }}"><i class="fa fa-refresh"></i>查看所有</a>
                    </div>
                </div>
                <!--快捷导航 结束-->
            </div>
        {% endif %}
    </form>
    <div class="result_wrap">
        <div class="result_content">
            <table class="list_tab">
                <tr>
                    <th class="tc" width="5%"><input type="checkbox" name=""></th>
                    <th class="tc">工作人员</th>
                    <th class="tc">所属项目</th>
                    <th class="tc">所属部门</th>
                    <th class="tc">所在科室</th>
                    <th class="tc">留言者姓名</th>
                    <th class="tc">留言者电话</th>
                    <th>留言详情</th>
                    <th>处理状态</th>
                    <th>留言时间</th>
                    <th>操作</th>
                </tr>
                {% for v in data['list'].items %}
                    <tr>
                        <td class="tc"><input type="checkbox" name="comment_id" value="{{v.comments.comment_id}}"></td>
                        <td class="tc">{{v.user_name}}</td>
                        <td class="tc">{{v.project_name}}</td>
                        <td class="tc">{{v.section_name}}</td>
                        <td class="tc">{{v.department_name}}</td>
                        <td class="tc comment_name">{{v.comments.comment_name}}</td>
                        <td class="tc comment_phone">{{v.comments.comment_phone}}</td>
                        <td>
                            <a class="comment_content"
                               content="{{v.comments.comment_content}}"><?php echo strlen($v->comments->comment_content) > 20 ? mb_substr($v->comments->comment_content,0,20,'utf-8').'...' : $v->comments->comment_content; ?></a>
                        </td>
                        <td>
                            <select id="testSelect"
                                    {% if _session['user_is_super'] or _session['user_is_admin'] %} onchange="handleCommentOne({{v.comments.comment_id}})" {% endif %}
                                    {% if (v.comments.comment_status == 0) %}  style="color: red"{% else %} style="color: green" {% endif %}>
                                <option value="0" {{ v.comments.comment_status == 0 ? 'selected' : null }} style="color: red">未处理</option>
                                <option value="1" {{ v.comments.comment_status == 1 ? 'selected' : null }} style="color: green">已处理</option>
                            </select>
                        </td>

                        <td class="created_time">{{ date("Y-m-d H:i:s", v.comments.created_time) }}</td>
                        <td>
                            {% if _session['user_is_super'] or _session['user_is_admin'] %}
                                <a href="javascript:;" onclick="delComment({{ v.comments.comment_id }})">删除</a>
                            {% endif %}
                        </td>
                    </tr>
                {% endfor %}
            </table>

            <div class="page_list clear" >
                <label>共 {{ data['list'].total_items }} 条记录</label>
                {% if data['list'].total_pages > 1 %}
                <div style="float: right">
                    <ul class="paginate">
                        <li class="disabled"><span>总计: {{ data['list'].total_pages }} 页</span></li>
                        <li class="active"><span>当前第: <input class="page_input" onchange="changePage(this.value)" onfocus="this.select()" value='{{ data['list'].current }}' /> 页</span></li>
                        {% if input['project_id'] is defined or input['section_id'] is defined or input['department_id'] is defined or input['status'] is defined or input['key_words'] is defined %}
                            {% if data['list'].current == 1 %}
                                <li class="disabled"><span>第一页</span></li>
                            {% else %}
                                <li><a href="/admin/comment?project_id={{ input['project_id'] is defined ? input['project_id'] : null }}&section_id={{ input['section_id'] is defined ? input['section_id'] : null }}&department_id={{ input['department_id'] is defined ? input['department_id'] : null }}&status={{ input['status'] is defined ? input['status'] : null }}&key_words={{ input['key_words'] is defined ? input['key_words'] : null }}&page=1">第一页</a></li>
                            {% endif %}
                            {% if data['list'].current == 1 %}
                                <li class="disabled"><span>上一页</span></li>
                            {% else %}
                                <li><a href="/admin/comment?project_id={{ input['project_id'] is defined ? input['project_id'] : null }}&section_id={{ input['section_id'] is defined ? input['section_id'] : null }}&department_id={{ input['department_id'] is defined ? input['department_id'] : null }}&status={{ input['status'] is defined ? input['status'] : null }}&key_words={{ input['key_words'] is defined ? input['key_words'] : null }}&page={{ data['list'].before }}">上一页</a></li>
                            {% endif %}
                            {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                <li class="disabled"><span>下一页</span></li>
                            {% else %}
                                <li><a href="/admin/comment?project_id={{ input['project_id'] is defined ? input['project_id'] : null }}&section_id={{ input['section_id'] is defined ? input['section_id'] : null }}&department_id={{ input['department_id'] is defined ? input['department_id'] : null }}&status={{ input['status'] is defined ? input['status'] : null }}&key_words={{ input['key_words'] is defined ? input['key_words'] : null }}&page={{ data['list'].next }}">下一页</a></li>
                            {% endif %}
                            {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                <li class="disabled"><span>最后一页</span></li>
                            {% else %}
                                <li><a href="/admin/comment?project_id={{ input['project_id'] is defined ? input['project_id'] : null }}&section_id={{ input['section_id'] is defined ? input['section_id'] : null }}&department_id={{ input['department_id'] is defined ? input['department_id'] : null }}&status={{ input['status'] is defined ? input['status'] : null }}&key_words={{ input['key_words'] is defined ? input['key_words'] : null }}&page={{ data['list'].last }}">最后一页</a></li>
                            {% endif %}
                        {% else %}
                            {% if data['list'].current == 1 %}
                                <li class="disabled"><span>第一页</span></li>
                            {% else %}
                                <li><a href="/admin/comment">第一页</a></li>
                            {% endif %}
                            {% if data['list'].current == 1 %}
                                <li class="disabled"><span>上一页</span></li>
                            {% else %}
                                <li><a href="/admin/comment?page={{ data['list'].before }}">上一页</a></li>
                            {% endif %}
                            {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                <li class="disabled"><span>下一页</span></li>
                            {% else %}
                                <li><a href="/admin/comment?page={{ data['list'].next }}">下一页</a></li>
                            {% endif %}
                            {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                <li class="disabled"><span>最后一页</span></li>
                            {% else %}
                                <li><a href="/admin/comment?page={{ data['list'].last }}">最后一页</a></li>
                            {% endif %}
                        {% endif %}
                    </ul>
                </div>
                {% endif %}
            </div>

        </div>
    </div>

    <!--搜索结果页面 列表 结束-->

    <script>
        // 修改页码.
        function changePage(page) {
            var total_pages = {{ data['list'].total_pages }};
            if (page > total_pages) {
                layer.msg('不能大于总'+total_pages+'页', {icon: 5});
                return;
            }
            location.href = "/admin/comment?project_id={{ input['project_id'] is defined ? input['project_id'] : null }}&section_id={{ input['section_id'] is defined ? input['section_id'] : null }}&department_id={{ input['department_id'] is defined ? input['department_id'] : null }}&status={{ input['status'] is defined ? input['status'] : null }}&key_words={{ input['key_words'] is defined ? input['key_words'] : null }}&page=" + page;
        }

        //显示详情
        $('.comment_content').click(function () {
            var comment_content = $(this).attr('content');
            var comment_name = $(this).parents('tr').find('.comment_name').html();
            var comment_phone = $(this).parents('tr').find('.comment_phone').html();
            var created_time = $(this).parents('tr').find('.created_time').html();
            var content = '<div style="padding: 10px;margin:20px;">' +
                '<div style="background-color: #f1f1f1;text-align: left">' +
                '<span style="display: block;padding: 8px 0;">留言人：<a style="text-decoration: none">' + comment_name + '</a></span>' +
                '<span style="display: block;padding: 8px 0;">时 &nbsp;&nbsp;间：<a style="text-decoration: none">' + created_time + '</a></span>' +
                '<span style="display: block;padding: 8px 0;">电  &nbsp;&nbsp;话：<a style="text-decoration: none">' + comment_phone + '</a></span>' +
                '</div>' +
                '<div>' +
                '<span style="display: block;width: 55px;height: auto;float: left;padding-top: 10px;">内  &nbsp;&nbsp;容：</span>' +
                '<div style="text-align: left;float: left;width: 590px;padding:10px 0 20px 0;line-height: 20px;">' + comment_content + '</div>' +
                '</div>' +
                '</div>';
            layer.open({
                type: 1,
                title: '<h4>留言详情</h4>',
                skin: 'layui-layer-rim', //加上边框
                area: ['720px', 'auto'], //宽高
                content: content
            });
        });

        //批量处理留言
        $('#allhandle').click(function () {
            var id = $("input[name='comment_id']");
            length = id.length;
            var str = "";
            for (var i = 0; i < length; i++) {
                if (id[i].checked == true) {
                    str = str + "," + id[i].value;
                }
            }
            str = str.substr(1)
            if (str == '') {
                return;
            }
            layer.confirm('您确定批量处理吗？(注意:选中的记录状态将反转)', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.ajax({
                    url: "{{url('admin/comment/changeall')}}",
                    type: "POST",
                    dataType: 'JSON',
                    data: {
                        "{{ _csrfKey }}": "{{ _csrf }}",
                        'comment_id': str,
                    },
                    success:function(data){
                        if (data.status == 201) {
                            layer.msg(data.msg, {
                                icon: 6,
                                time: 2000, //2s后自动关闭
                            },function (){
                                location.reload();
                            });
                        } else {
                            layer.msg(data.msg, {icon: 5});
                        }
                    },
                    error:function(){
                        layer.msg('操作失败，请稍后重试！', {icon: 2});
                    }
                })
            });
        });

        //批量删除
        $('#alldelete').click(function () {
            var id = $("input[name='comment_id']");
            length = id.length;
            var str = "";
            for (var i = 0; i < length; i++) {
                if (id[i].checked == true) {
                    str = str + "," + id[i].value;
                }
            }
            str = str.substr(1)
            if (str == '') {
                return;
            }
            layer.confirm('您确定要批量删除吗？', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.ajax({
                    url: "{{url('admin/comment/deleteall')}}",
                    type: "POST",
                    dataType: 'JSON',
                    data: {
                        "{{ _csrfKey }}": "{{ _csrf }}",
                        'comment_id': str
                    },
                    success:function(data){
                        if (data.status == 201) {
                            layer.msg(data.msg, {
                                icon: 6,
                                time: 2000, //2s后自动关闭
                            },function (){
                                location.reload();
                            });
                        } else {
                            layer.msg(data.msg, {icon: 5});
                        }
                    },
                    error:function(){
                        layer.msg('操作失败，请稍后重试！', {icon: 2});
                    }
                })
            });
        });

        //根据状态显示列表
        //    $('#statusselect').change(function () {
        //        var status = $(this).find("option:selected").val();
        //        location.href = '/admin/comment/indexbystatus/'+status;
        //    });


        //处理一条留言
        function handleCommentOne(id) {
            var status = $('#testSelect').find("option:selected").val();
            layer.confirm('您确定要处理吗？', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.ajax({
                    url:"{{url('admin/comment/change')}}",
                    type: "POST",
                    dataType: 'JSON',
                    data: {
                        "{{ _csrfKey }}": "{{ _csrf }}",
                        'comment_id': id,
                        'comment_status': status
                    },
                    success:function(data){
                        if (data.status == 201) {
                            layer.msg(data.msg, {
                                icon: 6,
                                time: 2000, //2s后自动关闭
                            },function (){
                                location.reload();
                            });
                        } else {
                            layer.msg(data.msg, {icon: 5});
                        }
                    },
                    error:function(){
                        layer.msg('操作失败，请稍后重试！', {icon: 2});
                    }
                })
            });
        }

        //删除一条留言
        function delComment(id) {
            layer.confirm('您确定要删除吗？', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.ajax({
                    url:"{{url('admin/comment/delete')}}",
                    type: "POST",
                    dataType: 'JSON',
                    data: {
                        "{{ _csrfKey }}": "{{ _csrf }}",
                        'comment_id': id,
                    },
                    success:function(data){
                        if (data.status == 201) {
                            layer.msg(data.msg, {
                                icon: 6,
                                time: 2000, //2s后自动关闭
                            },function (){
                                location.reload();
                            });
                        } else {
                            layer.msg(data.msg, {icon: 5});
                        }
                    },
                    error:function(){
                        layer.msg('操作失败，请稍后重试！', {icon: 2});
                    }
                })
            });
        }

        $(function(){
            $("input[name='comment_id']").click(function(){
                var value = $(this).prop("checked");
                check_selectAll(value);
            })
        });

        //检查是否改变全选按钮
        function check_selectAll(value){
            var flag = false;
            $(" .list_tab ").find("input[name='comment_id']").each(function(k,v){
                if(!($(this).prop("checked"))){
                    if(!value){
                        flag = true;
                    }
                    return false;
                }else if(k == ($(" .list_tab ").find("input[name='comment_id']").length *1-1)){
                    if(value){
                        flag = true;
                    }
                }
            });
            if(flag){
                $(".list_tab tr").eq(0).find("input[type='checkbox']").prop("checked",value);
            }
        }
    </script>

{% endblock %}