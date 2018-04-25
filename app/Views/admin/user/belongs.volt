{% extends "layout/main.volt" %} {% block content %}
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
                    {% for v in data['list'] %}
                        <tr>
                            <td class="tc">{v.belong_id}}</td>
                            <td>{{v.user_name}}</td>
                            <td>{{v.user_job}}</td>
                            <td>{{v.project_name}}</td>
                            <td>{{v.department_name}}</td>
                            <td>{{str_limit(rtrim(ltrim(v.users,'{'),'}'),'50','...')}}</td>
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
                <label>共 {{data['list'].total()}} 条记录</label>
                <div style="float: right">
                    {{data['list'].links()}}</div>
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
                    url: "{{url('admin/users/delbelong')}}?belong_id=" + belong_id,
                    type: "post",
                    data: {
                        "{{ _csrfKey }}": "{{ _csrf }}",
                        '_method': 'post',
                    },
                    dataType:'json',
                    success: function(data){
                        if(data.state == 0){
                            layer.msg(data.msg, {icon: 6});
                            location.reload();
                        }else{
                            layer.msg(data.msg, {icon: 5});
                        }
                    },
                    error: function(data) {
                        layer.msg("操作失败，请稍后重试！");
                    }
                });

            }, function () {

            });
        }
    </script>


{% endblock %}
