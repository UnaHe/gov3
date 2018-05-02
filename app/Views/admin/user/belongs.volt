{% extends "layout/main.volt" %}

{% block content %}
    <style>
        .wrap {width: 700px;white-space: nowrap;text-overflow: ellipsis;overflow: hidden;}
    </style>
    <!--面包屑导航 开始-->
    <div class="crumb_warp">
        <i class="fa fa-home"></i> <a href="{{url('admin/home')}}">首页</a> &raquo; 人员归属关系列表
    </div>

    <!--面包屑导航 结束-->


    <!--搜索结果页面 列表 开始-->
    <!--结果页快捷搜索框 开始-->
    <div class="search_wrap">
        <form action="{{url('admin/users/belongs')}}" method="get" name="search_form">
            <table class="search_tab">
                <tr>
                    {% Include 'layout/search_list1' with ['type': 0] %}
                    <td><input type="submit" class="btn btn-info" value="查询"></td>
                </tr>
            </table>
        </form>
    </div>
    {{ content() }}
    <p><?php $this->flashSession->output() ?></p>

    <div class="result_wrap">
        <div class="result_content">
            <table class="list_tab">
                <tr>
                    <th class="tc" width="5%">ID</th>
                    <th>用户</th>
                    <th>职位</th>
                    <th>单位</th>
                    <th>科室</th>
                    <th>管理用户</th>
                    <th>操作</th>
                </tr>
                {% if data['list'] is not empty %}
                    {% for v in data['list'].items %}
                        <tr>
                            <td class="tc">{{ v.belong_id }}</td>
                            <td>{{v.user_name}}</td>
                            <td>{{v.user_job}}</td>
                            <td>{{v.project_name}}</td>
                            <td>{{v.department_name}}</td>
                            <td><?php echo mb_substr(rtrim(ltrim($v->users,'{'),'}'),0,35,'utf-8').'...'; ?></td>
                            <td>
                                <a href="{{url('admin/users/addbelong?user_id=' ~ v.belong_id)}}">编辑</a>
                                <a href="javascript:;" onclick="del({{v.belong_id}})">删除</a>
                            </td>
                        </tr>
                    {% endfor %}
                {% else %}
                    <tr>
                        <td col="6">暂无数据</td>
                    </tr>
                {% endif %}
            </table>

            <div class="page_list clear" >
                <label>共 {{ data['list'].total_items }} 条记录</label>
                {% if data['list'].total_pages > 1 %}
                    <div style="float: right">
                        <ul class="paginate">
                            <li class="disabled"><span>总计: {{ data['list'].total_pages }} 页</span></li>
                            <li class="active"><span>当前第: {{ data['list'].current }} 页</span></li>
                            {% if input['project_id'] is defined or input['department_id'] is defined %}
                                {% if data['list'].current == 1 %}
                                    <li class="disabled"><span>第一页</span></li>
                                {% else %}
                                    <li><a href="/admin/users/belongs?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&department_id={{ input['department_id'] is defined ? input['department_id'] : '' }}&page=1">第一页</a></li>
                                {% endif %}
                                {% if data['list'].current == 1 %}
                                    <li class="disabled"><span>上一页</span></li>
                                {% else %}
                                    <li><a href="/admin/users/belongs?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&department_id={{ input['department_id'] is defined ? input['department_id'] : '' }}&page={{ data['list'].before }}">上一页</a></li>
                                {% endif %}
                                {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                    <li class="disabled"><span>下一页</span></li>
                                {% else %}
                                    <li><a href="/admin/users/belongs?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&department_id={{ input['department_id'] is defined ? input['department_id'] : '' }}&page={{ data['list'].next }}">下一页</a></li>
                                {% endif %}
                                {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                    <li class="disabled"><span>最后一页</span></li>
                                {% else %}
                                    <li><a href="/admin/users/belongs?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&department_id={{ input['department_id'] is defined ? input['department_id'] : '' }}&page={{ data['list'].last }}">最后一页</a></li>
                                {% endif %}
                            {% else %}
                                {% if data['list'].current == 1 %}
                                    <li class="disabled"><span>第一页</span></li>
                                {% else %}
                                    <li><a href="/admin/users/belongs">第一页</a></li>
                                {% endif %}
                                {% if data['list'].current == 1 %}
                                    <li class="disabled"><span>上一页</span></li>
                                {% else %}
                                    <li><a href="/admin/users/belongs?page={{ data['list'].before }}">上一页</a></li>
                                {% endif %}
                                {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                    <li class="disabled"><span>下一页</span></li>
                                {% else %}
                                    <li><a href="/admin/users/belongs?page={{ data['list'].next }}">下一页</a></li>
                                {% endif %}
                                {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                    <li class="disabled"><span>最后一页</span></li>
                                {% else %}
                                    <li><a href="/admin/users/belongs?page={{ data['list'].last }}">最后一页</a></li>
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
        //删除
        function del(belong_id) {
            layer.confirm('您确定要删除吗？', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                $.ajax({
                    url: "{{url('admin/users/belongs/delete')}}",
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        'belong_id': belong_id,
                        "{{ _csrfKey }}": "{{ _csrf }}",
                    },
                    success: function(data){
                        if(data.status == 201){
                            layer.msg(data.msg, {
                                icon: 6,
                                time: 2000, //2s后自动关闭
                            });
                        }else{
                            layer.msg(data.msg, {icon: 5});
                        }
                    },
                    error: function(data) {
                        layer.msg('操作失败，请稍后重试！', {icon: 2});
                    }
                });

            });
        }
    </script>

{% endblock %}
