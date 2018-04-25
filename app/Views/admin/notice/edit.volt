{% extends "layout/main.volt" %}

{% block content %}

    <!--面包屑导航 开始-->
    <style>
        .sele_lo {
            position: absolute;
            width: 15px;
            left: .1rem;
            top: 0.2rem;
        }
    </style>
    <div class="crumb_warp">
        <i class="fa fa-home"></i> <a href="{{url('admin/home')}}">首页</a> &raquo; 告示管理
    </div>
    <!--面包屑导航 结束-->

    <!--结果集标题与导航组件 开始-->
    <div class="result_wrap">
        <div class="result_title">
            <h3>编辑告示</h3>
            {{ content() }}
            <p><?php $this->flashSession->output() ?></p>
        </div>
        <div class="result_content">
            <div class="short_wrap">
                <a href="{{url('admin/notice/create')}}"><i class="fa fa-plus"></i>添加告示</a>
                <a href="{{url('admin/notice')}}"><i class="fa fa-recycle"></i>全部告示</a>
            </div>
        </div>
    </div>
    <!--结果集标题与导航组件 结束-->

    <div class="result_wrap">
        <form action="{{ url('admin/notice/update') }}" method="POST" id="add_form">
            <input type="hidden" name="_method" value="put">
            <input type="hidden" name="notice_id" value="{{ notice.notice_id }}"/>
            <input type="hidden" name="{{ _csrfKey }}" value="{{ _csrf }}"/>
            <table class="add_tab">
                <tbody>
                <tr>
                    <th width="150"><i class="require">*</i>归属单位：</th>
                    <td>
                        <?php if(is_object($project)) { ?>
                            <select name="project_id" class="project_id multiselect" required>
                                <option value="">请选择</option>
                                {% for d in project %}
                                    <option value="{{d.project_id}}"  {{ d.project_id == notice.project_id ? 'selected' : '' }}>
                                        {{d.project_name}}
                                    </option>
                                {% endfor %}
                            </select>
                            <span for="project_id" class="error"></span>
                        <?php } else { ?>
                            <input type="hidden" name="project_id" class="project_id" value="{{ _session['project_id'] }}">
                            <span><i class=""></i>{{ _session['project_name'] }}</span>
                        <?php } ?>
                    </td>
                </tr>
                <tr style="margin-bottom: 50px">
                    <th>显示科室：</th>
                    <td class="department_list">
                        {#按科室排序#}
                        {% if department_list is not empty %}
                            <div class="tab-pane fade in active" id="department">
                                <div style="" class="nav-tabs">
                                    <label for="checkAll">全选</label><input type="checkbox" style="margin-right:60px;" class="checkAll" id="checkAll">
                                    <div style="display: inline" class="checked_name">
                                        科室：
                                        {% if used_department_list is not empty %}
                                            {% for v in used_department_list %}
                                                <span class="u_{{v}}">{{department_list[v]['department_name']}}</span>
                                            {% endfor %}
                                        {% endif %}
                                    </div>
                                </div>
                                <div class="checkbox_list nav-tabs" style="">
                                    {% for v in department_list %}
                                        <div style="display:inline-block ;">
                                            <div style="display: inline;margin:10px 20px;">
                                                <input type="checkbox" id="departmentli_{{v['department_id']}}" class="departmentli departmentli_{{v['department_id']}}" name="departments[]" value="{{v['department_id']}}"
                                                    <?php if (array_key_exists($v['department_id'], $used_department_list)) { ?>
                                                        checked
                                                    <?php } ?>
                                                />
                                                <label for="departmentli_{{v['department_id']}}">{{v['department_name']}}</label>
                                            </div>
                                        </div>
                                    {% endfor %}
                                </div>
                            </div>
                        {% endif %}
                    </td>
                </tr>
                <tr>
                    <th><i class="require">*</i>告示标题：</th>
                    <td>
                        <input type="text" name="notice_title" style="width: 500px" value="{{ notice.notice_title }}" required/>
                    </td>
                </tr>
                <tr>
                    <th>告示详情：</th>
                    <td>
                            {{ javascript_include('admin/org/ueditor/ueditor.config.js') }}
                            {{ javascript_include('admin/org/ueditor/ueditor.all.min.js') }}
                            {{ javascript_include('admin/org/ueditor/lang/zh-cn/zh-cn.js') }}
                        <script id="editor" name="notice_content" type="text/plain" style="width: 600px;height: 380px;">
                            {{ notice.notice_content }}
                        </script>
                        <script type="text/javascript">
                            var ue = UE.getEditor('editor',ueditor_toolbars);
                        </script>
                        <style>
                            .edui-default {
                                line-height: 28px;
                            }

                            div.edui-combox-body, div.edui-button-body, div.edui-splitbutton-body {
                                overflow: hidden;
                                height: 20px;
                            }

                            div.edui-box {
                                overflow: hidden;
                                height: 22px;
                            }
                        </style>
                    </td>
                </tr>
                <tr>
                    <th><i class="require">*</i>状态：</th>
                    <td>
                        <input type="radio" name="notice_status" id="notice_status_1" value="1" {{ notice.notice_status == 1 ? 'checked' : '' }}><label for="notice_status_1">发布</label>&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="radio" name="notice_status" id="notice_status_0" value="0" {{ notice.notice_status == 0 ? 'checked' : '' }}><label for="notice_status_0">不发布</label>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <input type="submit" id="submit_btn" class="btn btn-info" value="提交">
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
    <script>
        $(function () {
            $('.multiselect').multiselect(multiselect_option);
            $("#add_form").validate({
//                submitHandler: function(form){
//                    form.submit();
//                },
                ignore: ".hide",
                errorElement: "span",
                rules: {
                    project_id: {
                        required: true,
                        min: 1
                    }
                },
                messages: {
                    project_id: {
                        required: "<i class='fa fa-exclamation-circle yellow'></i>请选择一个单位",
                        min: "<i class='fa fa-exclamation-circle yellow'></i>请选择一个单位"
                    }
                }
            });
        });

        var old_department = "<?php current($used_department_list) ?>";
        if(old_department){
            check_selectAll(true);
        }

        //获取单位科室
        $("select[name='project_id']").change(function () {
            var project_id = $(this).val();
            $('.department_list').empty();
            if (project_id <= 0 || project_id == '') {
                return false;
            }
            var url = '{{url("admin/notice/ajaxGetDepartments")}}';
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                data: {
                    "{{ _csrfKey }}": "{{ _csrf }}",
                    'project_id': project_id,
                    'notice_id': '{{ notice.notice_id }}',
                }, success: function (data) {
                    if (data.status == 0) {
                        var html = '';
                        if ($('#department').length <= 0) {
                            html += '<div class="tab-pane fade in active" id="department">' +
                                '<div style="" class="nav-tabs">' +
                                '<label for="checkAll">全选</label><input type="checkbox" style="margin-right:60px;"  class="checkAll" id="checkAll">' +
                                '<div style="display: inline" class="checked_name">' +
                                '科室：' +
                                '</div>' +
                                '</div>' +
                                '<div class="checkbox_list nav-tabs" style="">';
                        }
                        //科室列表
                        var checked_name = '';
                        $.each(data.department_list, function (k, v) {
                            var checked = '';
                            if(data.used_department_list.hasOwnProperty(v.department_id)){
                                checked = 'checked';
                                checked_name += '<span class="u_'+v.department_id+'">'+v.department_name+'</span>'
                            }
                            html += '<div style="display:inline-block ;">' +
                                '<div style="display: inline;margin:10px 20px;">' +
                                '<input type="checkbox" id="departmentli_' + v.department_id + '" class="departmentli departmentli_' + v.department_id + '" name="departments[]" value="' + v.department_id + '"  '+checked+'>' +
                                '<label for="departmentli_' + v.department_id + '">' + v.department_name + '</label>' +
                                '</div>' +
                                '</div>';
                        });
                        if ($('#department').length <= 0) {
                            html += '</div>';
                            $('.department_list').html(html);
                        } else {
                            $('.checkbox_list').html(html);
                        }
                        $('.checked_name').html(checked_name);
                        if(data.used_department_list){
                            check_selectAll(true);
                        }
                    }
                },
                error: function(data) {
                    if (!!data.responseJSON && data.responseJSON.error == 'Unauthenticated.') {
                        location.href="/admin/login";
                    }else{
                        layer.msg("操作失败，请稍后重试！");
                    }
                }
            })
        });

        //全选
        $(".department_list").on('click','.checkAll',function(){
            var value = $(this).prop('checked');
            $(".checkAll").prop('checked',value);
            $(" .checkbox_list ").find("input[type='checkbox']").each(function(){
                $(this).prop('checked',value);
                if($(this).hasClass("departmentli")){
                    select_user($(this).val(),$(this).next().html(),value)
                }
            })
        });

        //单个选择
        $(".department_list").on('click','.departmentli',function(){
            var value = $(this).prop("checked");
            $(".departmentli_"+$(this).val()).prop("checked",value);
            select_user($(this).val(),$(this).next().html(),value);
            check_selectAll(value);
        });

        //选择一个
        function select_user(id,name, checked){
            if(checked){
                if($(".checked_name").find('span.u_'+id).length <= 0){
                    var html  = '<span class="u_'+id+'">'+name+'</span>';
                    $(".checked_name").append(html);
                }
            }else{
                if($(".checked_name").find('span.u_'+id).length > 0){
                    $(".checked_name").find('span.u_'+id).remove();
                }
            }
        }

        //检查是否改变全选按钮
        function check_selectAll(value){
            var flag = false;
            $(" .checkbox_list ").find("input[type='checkbox']").each(function(k,v){
                if(!($(this).prop("checked"))){
                    if(!value){
                        flag = true;
                    }
                    return false;
                }else if(k == ($(" .checkbox_list ").find("input[type='checkbox']").length *1-1)){
                    if(value){
                        flag = true;
                    }
                }
            });
            if(flag){
                $(".checkAll").prop("checked",value);
            }
        }

    </script>

{% endblock %}
