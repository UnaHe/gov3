{% extends "layout/main.volt" %}

{% block content %}

    <style>
        table.add_tab tr td span{margin-left:8px;}
        input[type='checkbox']{margin-top:0px;margin-right: 0px;}
        .nav-tabs>td{padding: 10px 0;}
        .lf_bottom{padding: 28px 0px 15px 0;}
        .user_list{height: 20px;padding: 0!important}
        #department_list{width: 200px;height: 30px;line-height: 30px;text-align: center;font-size: 15px;color: #474747;border-radius: 3px;outline:none}
        .lf_b{width: auto;min-width: 40px;display: inline-block;height: auto;text-align: center;}
        .lf_label{margin-right: 0px;padding: 0 10px;margin-bottom: 2px;}
        .user_box_list{width: 100%;}
        /* .nav-tabs>a{cursor:pointer} */
    </style>
    <div class="crumb_warp">
        <i class="fa fa-home"></i> <a href="{{url('admin/home')}}">首页</a> &raquo; 添加人员归属关系
    </div>
    <!--面包屑导航 结束-->

    <!--结果集标题与导航组件 开始-->
    <div class="result_wrap">
        {#<h3>添加归属</h3>#}
        <div class="result_title">
            {{ content() }}
            <p><?php $this->flashSession->output() ?></p>
        </div>
    </div>
    <!--结果集标题与导航组件 结束-->
    <form action="{{url('admin/users/addbelong?user_id=' ~ user.user_id)}}" method="post" id="status_form">
        <input type="hidden" name="{{ _csrfKey }}" value="{{ _csrf }}"/>
        <div class="result_wrap">

            <table class="add_tab">
                <tbody>
                <tr>
                    <th width="150">归属于：</th>
                    <td><span>{{user.user_name}}{% if user.user_job is not empty %}({{user.user_job}}){% endif %}</span></td>
                </tr>
                <tr>
                    <th width="150">人员列表：</th>
                    <td class="user_list">
                        {% for v in old_list %}
                            <span class="u_{{v['user_id']}}">{{v['user_name']}}</span>
                        {% endfor %}
                    </td>
                </tr>
                </tbody>
            </table>

        </div>
        
        <div class="result_wrap">
            <div id="myTabContent" class="tab-content">
                <ul id="myTab" class="nav nav-tabs">
                    <li class="active">
                        <a href="#department" data-toggle="tab">
                            按科室排序
                        </a>
                    </li>
                    <li><a href="#name" data-toggle="tab">按姓名排序</a></li>

                </ul>
                {#按科室排序#}
                <div class="tab-pane fade in active" id="department">
                    <div style="padding: 20px 0;" class="nav-tabs">
                        <label for="checkAll">全选</label>&nbsp;<input id="checkAll" type="checkbox" style="margin-right:60px;margin-top: -1px"  class="checkAll" >
                        <label for="">科室: </label> <select onchange="" id="department_list" name="department_id" style="margin-left: 10px" class="multiselect">
                            <option value="">请选择</option>
                                {% for v in department_list %}
                                    <option value="{{v.department_id}}">{{v.department_name}}</option>
                                {% endfor %}
                        </select>
                    </div>
                    <table class="user_box_list">
                        <tbody>
                        {#<div class="checkbox_list nav-tabs" style="">#}
                        <!-- @foreach($user_list_by_dp  as $k=>$v) -->
                        {% for index,v in user_list_by_dp %}
                            <tr class="nav-tabs" id="dep_{{index}}">
                                <td width="300px">
                                    <div style="display: inline;margin:10px 20px;">
                                        <input type="checkbox" class="checkAll_li" id="dp_{{index}}" data-id="{{index}}" name="" value="35"><label for="dp_{{index}}"><b>{{v['department_name']}}</b></label>
                                    </div>
                                </td>
                                <td>
                                    <div style="display:inline-block ;">
                                        {% for i,vv in v['list'] %}
                                        <div style="display: inline;margin:10px 20px;">
                                             <label for="li_{{vv['user_id']}}"><input id="li_{{vv['user_id']}}" type="checkbox" class="userli userli_{{vv['user_id']}}" name="users[]" value="{{vv['user_id']}}" {{ old_list[vv['user_id']] is defined ? 'checked' : '' }}><span>{{vv['user_name']}}</span></label>
                                        </div>
                                        {% endfor %}
                                    </div>
                                </td>
                            </tr>
                        {% endfor %}
                        {#</div>#}
                        </tbody>
                    </table>
                </div>
                {#按姓名排序#}
                <div class="tab-pane" id="name">
                    <div style="padding:20px 0 15px 0;" class="nav-tabs">
                        <label for="checkAll2">全选</label>&nbsp;<input id="checkAll2" type="checkbox" style="margin-right:60px;" class="checkAll" >
                        关键字：
                        <a href="#all" onclick="see_list('all')"><label class="lf_label">全部</label></a>
                        {% for v in first_chars %}
                            <a href="#{{v}}" onclick="see_list('{{v}}')" data-toggle="tab"><label class="lf_label">{{v == 'zother' ? '其他' : v}}</label></a>
                        {% endfor %}
                    </div>
                    <table class="user_box_list">
                        <tbody>
                        {#<div class="checkbox_list nav-tabs" style="">#}
                        {% for index,v in user_list_by_name %}
                            <tr class="nav-tabs tab-pane"  id="{{index}}">
                                <td width="100px">
                                    <div style="display: inline;margin:10px 20px;">
                                        <input id="char_{{v['first_char']}}" type="checkbox" class="checkAll_li" data-id="{{index}}" name="" value="35"><label for="char_{{v['first_char']}}"><b class="lf_b">{{v['first_char'] == 'zother' ? '其他' : v['first_char']}}</b></label>
                                    </div>
                                </td>
                                <td>
                                    <div style="display:inline-block ;" class="td_user_list">
                                        {% for vv in  v['list'] %}
                                            <div style="display: inline;margin:10px 20px;">
                                                <label for="cli_{{vv['user_id']}}"><input id="cli_{{vv['user_id']}}" type="checkbox" class="userli userli_{{vv['user_id']}}" name="users[]" value="{{vv['user_id']}}" {{ old_list[vv['user_id']] is defined ? 'checked' : '' }}><span>{{vv['user_name']}}</span></label>
                                            </div>
                                        {% endfor %}
                                    </div>
                                </td>
                            </tr>
                        {% endfor %}
                        {#</div>#}
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="lf_bottom">
                <input type="submit" class="btn btn-info" value="提交">
            </div>
        </div>
    </form>
    <script>
        $(function () {
            $('.multiselect').multiselect(multiselect_option);
            //全选
            $(".checkAll").click(function(){
                var value = $(this).prop('checked');
                $(".checkAll").prop('checked',value);
                $(".tab-pane .user_box_list").find("input[type='checkbox']").each(function(){
                    $(this).prop('checked',value);
                    if($(this).hasClass("userli")){
                        select_user($(this).val(),$(this).next().html(),value)
                    }
                })
            });

            //类别全选
            $(".checkAll_li").click(function(){

                var value = $(this).prop("checked");
                var  list = $(this).parents("tr").find(".userli");
                $(list).each(function(){
                    $(this).prop('checked',value);
                    $(".userli_"+$(this).val()).prop("checked",value);
                    select_user($(this).val(),$(this).next().html(),value)
                });
                check_selectAll(value);
            });

            //单个选择
            $(".userli").click(function(){
                var value = $(this).prop("checked");
                $(".userli_"+$(this).val()).prop("checked",value);
                select_user($(this).val(),$(this).next().html(),value);
                check_checkAll_li($(this).parents("tr"),value);

            });

            //选择一个人
            function select_user(user_id,user_name, checked){
                if(checked){
                    if($(".user_list").find('span.u_'+user_id).length <= 0){
                        var html  = '<span class="u_'+user_id+'">'+user_name+'</span>';
                        $(".user_list").append(html);
                    }
                }else{
                    if($(".user_list").find('span.u_'+user_id).length > 0){
                        $(".user_list").find('span.u_'+user_id).remove();
                    }
                }
            }

            //检查是否改变分类全选按钮
            function check_checkAll_li(obj, value){
                var flag = false;
                $($(obj).find(".userli")).each(function(k,v){
                    if(!($(this).prop("checked"))){
                        if(!value){
                            flag = true;
                        }
                        return false;
                    }else if(k == ($(obj).find(".userli").length *1-1)){
                        if(value){
                            flag = true;
                        }
                    }
                });
                if(flag){
                    $(obj).find(".checkAll_li").prop("checked",value);
                }
                check_selectAll(value);
            }

            //检查是否改变全选按钮
            function check_selectAll(value){
                var flag = false;
                $(".tab-pane.active  .user_box_list").find("input[type='checkbox']").each(function(k,v){
                    if(!($(this).prop("checked"))){
                        if(!value){
                            flag = true;
                        }
                        return false;
                    }else if(k == ($(".tab-pane.active .user_box_list").find("input[type='checkbox']").length *1-1)){
                        if(value){
                            flag = true;
                        }
                    }
                })
                if(flag){
                    $(".checkAll").prop("checked",value);
                }
            }


            //按科室筛选
            $("#department_list").change(function(){
                var value = $(this).val();
                if(value == ''){
                    $('#department tbody').find("tr").removeClass('hide');
                }else{
                    $('#department tbody').find("tr").addClass('hide');
                    $('#department tbody').find("tr#dep_"+value).removeClass('hide');
                }
            })
        });

        ///按首写字母
        function see_list(key_word){
            if(key_word == 'all'){
                $('#name tbody').find("tr").removeClass('hide');
            }else{
                $('#name tbody').find("tr#"+key_word).removeClass('hide').siblings('tr').addClass('hide');
            }
        }
    </script>

{% endblock %}