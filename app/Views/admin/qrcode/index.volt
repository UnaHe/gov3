{% extends "layout/main.volt" %}

{% block content %}

    <style>
        .wrap{
            width: 400px;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }
    </style>
    <!--面包屑导航 开始-->
    {{ stylesheet_link('admin/org/bigcolorpicker/css/jquery.bigcolorpicker.css') }}
    <div class="crumb_warp">
        <i class="fa fa-home"></i> <a href="{{url('admin/home')}}">首页</a> &raquo; 科室二维码列表
    </div>

    <!--面包屑导航 结束-->
    <!--普通科员不显示查询-->
    {% if _session['project_id'] is empty or _session['user_is_admin'] %}
    <div class="search_wrap">
        <form action="{{ url('admin/qrcode') }}" method="get" name="search_form">
            <table class="search_tab">
                <tr>
                    {#载入单位、部门、科室的查询#}
                    {% Include 'layout/search_list1' with ['type': 0 ] %}
                    <td><input type="submit"  class="btn btn-info" value="查询"></td>
                </tr>
            </table>
        </form>
    </div>
    {% endif %}
    {{ content() }}
    <p><?php $this->flashSession->output() ?></p>

    <div class="result_wrap">
        <div class="result_content">
            <table class="list_tab">
                <tr>
                    <th class="tc">QrCodeID</th>
                    <th>单位</th>
                    <th>科室</th>
                    <th class="wrap">Link</th>
                    <th>说明</th>
                    <th>操作</th>
                </tr>
                {% if data['list'] is not empty %}
                    {% for v in data['list'].items %}
                        <tr>
                            <td class="tc">{{v.forwards.forward_id}}</td>
                            <td>{{v.project_name}}</td>
                            <td>{{v.department_name}}</td>
                            <td ><a class="wrap" href="{{APP_URL ~ '/forward/' ~ v.forwards.forward_id}}" target="_blank" style="display:inline;" title="{{APP_URL ~ '/forward/' ~ v.forwards.forward_id}}">{{APP_URL ~ '/forward/' ~ v.forwards.forward_id}}</a></td>
                            <td>{{v.forwards.forward_introduction}}</td>
                            <td>
                                {% if _session['user_is_super'] or (_session['user_is_admin'] and _session['project_id'] == '') %}
                                <a href="{{url('admin/qrcode/' ~ v.forwards.id ~ '/edit')}}">编辑</a>
                                <a href="javascript:;" onclick="del({{v.forwards.id}})">删除</a>
                                {% endif %}
                                <a href="javascript:;" onclick="get_qrcode({{v.forwards.id}})">二维码</a>
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
                            <li class="active"><span>当前第: <input class="page_input" onchange="changePage(this.value)" onfocus="this.select()" value='{{ data['list'].current }}' /> 页</span></li>
                            {% if input['project_id'] is defined or input['department_id'] is defined %}
                                {% if data['list'].current == 1 %}
                                    <li class="disabled"><span>第一页</span></li>
                                {% else %}
                                    <li><a href="/admin/qrcode?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&department_id={{ input['department_id'] is defined ? input['department_id'] : '' }}&page=1">第一页</a></li>
                                {% endif %}
                                {% if data['list'].current == 1 %}
                                    <li class="disabled"><span>上一页</span></li>
                                {% else %}
                                    <li><a href="/admin/qrcode?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&department_id={{ input['department_id'] is defined ? input['department_id'] : '' }}&page={{ data['list'].before }}">上一页</a></li>
                                {% endif %}
                                {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                    <li class="disabled"><span>下一页</span></li>
                                {% else %}
                                    <li><a href="/admin/qrcode?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&department_id={{ input['department_id'] is defined ? input['department_id'] : '' }}&page={{ data['list'].next }}">下一页</a></li>
                                {% endif %}
                                {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                    <li class="disabled"><span>最后一页</span></li>
                                {% else %}
                                    <li><a href="/admin/qrcode?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&department_id={{ input['department_id'] is defined ? input['department_id'] : '' }}&page={{ data['list'].last }}">最后一页</a></li>
                                {% endif %}
                            {% else %}
                                {% if data['list'].current == 1 %}
                                    <li class="disabled"><span>第一页</span></li>
                                {% else %}
                                    <li><a href="/admin/qrcode">第一页</a></li>
                                {% endif %}
                                {% if data['list'].current == 1 %}
                                    <li class="disabled"><span>上一页</span></li>
                                {% else %}
                                    <li><a href="/admin/qrcode?page={{ data['list'].before }}">上一页</a></li>
                                {% endif %}
                                {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                    <li class="disabled"><span>下一页</span></li>
                                {% else %}
                                    <li><a href="/admin/qrcode?page={{ data['list'].next }}">下一页</a></li>
                                {% endif %}
                                {% if data['list'].current == data['list'].last or data['list'].last == 0 %}
                                    <li class="disabled"><span>最后一页</span></li>
                                {% else %}
                                    <li><a href="/admin/qrcode?page={{ data['list'].last }}">最后一页</a></li>
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
            location.href = "/admin/qrcode?project_id={{ input['project_id'] is defined ? input['project_id'] : '' }}&department_id={{ input['department_id'] is defined ? input['department_id'] : '' }}&page=" + page;
        }

        //生成二维码
        function get_qrcode(id){
            $.ajax({
                url: '{{ url('admin/qrcode/ajaxGetForwardQrCode') }}',
                type: 'POST',
                dataType :'JSON',
                data: {
                    "{{ _csrfKey }}": "{{ _csrf }}",
                    'id': id,
                },
                success: function(data){
                    if(data.status == 200){
                        var content = '<div style="padding: 10px">' + '<img src="http://qr.liantu.com/api.php?w=150&h=150&m=0&el=l&text='+data.msg+'" width="150px" height="150px">'+ '</div>';
                        layer.open({
                            title:'二维码',
                            type: 1,
                            area: ['168px', '215px'], //宽高
                            shadeClose: true,
                            content: content
                        });
                    }else{
                        layer.msg(data.msg, {icon: 5});
                    }
                },
                error: function() {
                    layer.msg('操作失败，请稍后重试！', {icon: 2});
                }
            })
        }

        //删除
        function del(id) {
            layer.confirm('您确定要删除吗？', {
                btn: ['确定','取消'] //按钮
            }, function(){
                $.ajax({
                    url: "{{url('admin/qrcode/delete')}}",
                    type: 'POST',
                    dataType :'JSON',
                    data: {
                        '_method':'delete',
                        'id': id,
                        "{{ _csrfKey }}": "{{ _csrf }}",
                    },
                    success: function(data){
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
                    error: function() {
                        layer.msg('操作失败，请稍后重试！', {icon: 2});
                    }
                })
            });
        }
    </script>

{% endblock %}
