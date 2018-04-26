{% extends "layout/main.volt" %}

{% block content %}
    <div class="crumb_warp">
        <i class="fa fa-home"></i> <a href="{{url('admin/home')}}">首页</a> &raquo; 管理员管理
    </div>
    {#<section class="content">#}
    <!-- Small boxes (Stat box) -->
    <div class="result_wrap">
        <div class="result_title">
            <h3>创建管理员</h3>
            {{ content() }}
            <p><?php $this->flashSession->output() ?></p>
            <div class="result_content">
                <div class="short_wrap">
                    <a href="{{url('admin/project/createadmin')}}"><i class="fa fa-plus"></i>创建管理员</a>
                    <a href="{{url('admin/project/adminuserlist')}}"><i class="fa fa-recycle"></i>全部管理员</a>
                </div>
            </div>
            <div class="result_title">
                <div style="background-color: #e5e9ec;">
                    &nbsp;&nbsp;&nbsp;&nbsp;上传的用户寸照，宽最多为295px,  高最多为415px,  并且宽和高的比例在7:10左右
                </div>
            </div>
        </div>
        <div class="result_wrap">
            <form  method="POST"  id="add-form" action="{{ url('admin/project/saveadmin')}}" >
                <input name="user_id" type="hidden" value="{{ user_id }}">
                <input type="hidden" name="{{ _csrfKey }}" value="{{ _csrf }}"/>
                <table class="add_tab">
                    <tbody>
                    <tr>
                        <th width="150"><i class="require">*</i> 管理员类型：</th>
                        <td>
                            {#@if($user_id > 0)#}
                            {#@if(!empty($user_info->project_id) || $user_info->project_id >0)#}
                            {#<input type="hidden" name="admin_type" value="1">#}
                            {#单位管理员#}
                            {#@else#}
                            {#系统管理员#}
                            {#<input type="hidden" name="admin_type" value="0">#}
                            {#@endif#}
                            {#@else#}
                            <input type="radio" id="type1" name="admin_type" value="1" {{ admin_type is defined and admin_type == '1' ? 'checked' : '' }} required><label for="type1">单位管理员</label>
                            <input type="radio" id="type2" name="admin_type" value="0" {{ admin_type is defined and admin_type == '0' ? 'checked' : '' }} required><label for="type2">系统管理员</label>
                            {#@endif#}
                        </td>
                    </tr>
                    {% Include "layout/common_tr" with ['type' : 0, 'project_id' : user_info is defined and user_info['project_id'] is not empty ? user_info['project_id'] : '', 'department_id' : user_info is defined and user_info['department_id'] is not empty ? user_info['department_id'] : ''] %}
                    <input type="hidden" name="roles1[]" value="{{ project_administrator['id'] }}">
                    <tr class="hide assign_role">
                        <th>分配角色：</th>
                        <td>
                            {% if roles is defined and roles is not empty %}
                            <div class="checkbox_list" style="">
                                <select  name="roles2[]" id="">
                                    {% for v in roles %}
                                        <option value="{{ v['id'] }}" {{ old_role is not empty and old_role == v['id'] ? 'selected' : '' }}>{{ v['name'] }}</option>
                                    {% endfor %}
                                </select>
                            </div>
                            {% endif %}
                        </td>
                    </tr>
                    <tr>
                        <th><i class="require">*</i> 姓名：</th>
                        <td>
                            <input type="text" class="lg" name="user_name" value="{{ user_info['user_name'] is not empty ? user_info['user_name'] : '' }}" required minlength="2"/>
                        </td>
                    </tr>
                    {% if user_info is empty %}
                    <tr>
                        <th><i class="require">*</i> 设置密码：</th>
                        <td>
                            <input type="hidden" class="lg" name="user_pass" >
                            <input type="password" class="lg" id="user_pass" value="123456" required>
                            <span>密码默认为123456</span>
                        </td>
                    </tr>
                    {% endif %}
                    <tr>
                        <th>用户头像：</th>
                        <td>
                            <input type="hidden" name="user_image" class="user_image" value="{{ user_info['user_image'] is not empty ? user_info['user_image'] : '' }}">
                            <input id="thumbnail" name="thumbnail" type="file" multiple onchange="UpLoadFile()">
                            <img alt="" id="previewImage"  src="{{ user_info['user_image'] is not empty ? _config['upload_url'] ~ user_info['user_image'] : '' }}" style="max-width: 75px; max-height:100px;display: {{ user_info['user_image'] is empty ? 'none' : '' }}">
                            <input type="button" value="移除图片" class="btn_remove back btn btn-info" style="display: {{ user_info['user_image'] is empty ? 'none' : '' }}">
                        </td>
                    </tr>
                    <tr>
                        <th><i class="require">*</i> 性别：</th>
                        <td>
                            <input type="radio" name="user_sex" id="user_sex_1" value="1" {{ user_info is not empty ? (user_info['user_sex'] == '1' ? 'checked' : '') : 'checked' }} required><label for="user_sex_1">男</label>
                            <input type="radio" name="user_sex" id="user_sex_0" value="0" {{ user_info is not empty ? (user_info['user_sex'] == '0' ? 'checked' : '') : '' }} required><label for="user_sex_0">女</label>
                        </td>
                    </tr>
                    <tr>
                        <th><i class="require">*</i> 年龄：</th>
                        <td>
                            <input type="number" class="" name="user_age" value="{{ user_info['user_age'] is not empty ? user_info['user_age'] : '' }}" required min="1" max="100"/>
                        </td>
                    </tr>
                    <tr>
                        <th><i class="require">*</i> 电话：</th>
                        <td>
                            <input type="text" class="lg" name="user_phone" value="{{ user_info['user_phone'] is not empty ? user_info['user_phone'] : '' }}" required isMobile="true"/>
                        </td>
                    </tr>
                    <tr>
                        <th>状态：</th>
                        <td>
                            <input type="radio" name="user_status" id="user_status_1" value="1" {{ user_info is not empty ? (user_info['user_status'] == '1' ? 'checked' : '') : 'checked' }} required/><label
                                    for="user_status_1">正常</label>
                            <input type="radio" name="user_status" id="user_status_0" value="0" {{ user_info is not empty ? (user_info['user_status'] == '0' ? 'checked' : '') : '' }} required/><label for="user_status_0">不正常</label>
                        </td>
                    </tr>
                    <tr class="project_type">
                        <th>职务：</th>
                        <td>
                            <input type="text" class="lg" name="user_job" value="{{ user_info['user_job'] is not empty ? user_info['user_job'] : '' }}">
                        </td>
                    </tr>
                    <tr class="project_type">
                        <th>职务内容：</th>
                        <td>
                            <textarea name="user_intro">{{ user_info['user_intro'] is not empty ? user_info['user_intro'] : '' }}</textarea>
                        </td>
                    </tr>

                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" class="btn btn-info" value="提交" id="submit-btn">
                            <input type="button" class="back btn btn-info" onclick="history.go(-1)" value="返回">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
    {#</section>#}
    {{ javascript_include('admin/style/js/jquery.md5.js') }}
    <script type="text/javascript">
        var admin_type = '{{ admin_type }}' * 1;

        var rules = {
            0 :{
                'project_id':{},
                'department_id':{}

            },
            1:{
                'project_id': {
                    required:true,
                    min:1
                },
                'department_id': {
                    required:true,
                    min:1
                }
            }
        };
        $(function(){
            //初始化
            $("#add-form").validate({
                submitHandler: function(form){
                    var project_id = $("input[name='user_id']").val();
                    if($("input[name='user_id']").val() == 0){
                        $("input[name='user_pass']").val($.md5($("#user_pass").val()));
                    }
                    form.submit();
                },
                errorElement: "span",
                ignore: "",
                rules: {
                    project_id: {
                        required:true,
                        min: 1
                    },
                    department_id: {
                        required:true,
                        min: 1
                    },
                    user_age:{
                        min:1,
                        max:100
                    },
                    user_phone:{
                        isMobile:true
                    }
                },
                messages: {
                    project_id: {
                        required: "<i class='fa fa-exclamation-circle yellow'></i>请选择单位",
                        min: "<i class='fa fa-exclamation-circle yellow'></i>请选择单位"
                    },
                    department_id: {
                        required: "<i class='fa fa-exclamation-circle yellow'></i>请选择科室",
                        min: "<i class='fa fa-exclamation-circle yellow'></i>请选择科室"
                    },
                    user_age: {
                        min: "<i class='fa fa-exclamation-circle yellow'></i>请填写实际年龄",
                        max: "<i class='fa fa-exclamation-circle yellow'></i>请填写实际年龄"
                    },
                }
            });

            if(admin_type == '1'){ //单位
                change_valid_rules(1);
                $("#type1").prop('checked',true);
                $(".assign_role").addClass("hide");
            }else{//系统
                change_valid_rules(0);
                $("#type2").prop('checked',true);
                $(".project_type").addClass("hide");
                $(".assign_role").removeClass("hide");
            }
            $('.btn_remove').on('click',function(){
                if ($('#user_thumb_img').attr('src') != '') {
                    $('input[name=user_image]').val('');
                    $('#previewImage').hide();
                    $('.btn_remove').hide();
                }
            });
            //切换管理员类型时
            $("input[name='admin_type']").click(function () {
                var value = $(this).val();
                if (value == 1) {
                    admin_type = 1;
                    $(".assign_role").addClass("hide");
                    $(".project_type").removeClass("hide");
                } else {
                    admin_type = 0;
                    $(".project_type").addClass("hide");
                    $(".assign_role").removeClass("hide");
                }
                change_valid_rules(admin_type);
            });

            function change_valid_rules(type) {
                if(!!type){
                    $("#project_id").rules("add",rules[admin_type]['project_id']);
                    $("#department_id").rules("add",rules[admin_type]['department_id']);
                }else{
                    $("#project_id").rules("remove");
                    $("#department_id").rules("remove");
                }
            }
        });
        //上传文件
        function UpLoadFile() {
            var xhr = new XMLHttpRequest();
            xhr.overrideMimeType('text/plain; charset=utf-8');
            // FormData 对象
            var formData = new FormData();
            var files = document.getElementById('thumbnail').files;
            if (files[0] == undefined) {
                layer.msg('没有选中任何文件');
                return;
            }
            //验证图片的格式和尺寸开始
            if (!/.(gif|jpg|png|GIF|JPG|PNG)$/.test($("#thumbnail").val())) {
                layer.msg("图片限于gif,jpg,png格式");
                $("#thumbnail").val("").focus();
                return false;
            }

            //宽高
            getImageWidthAndHeight(files[0], function (obj) {
                var proportion = (obj.width * 1) / (obj.height * 1);

                if (obj.width > 295 || obj.height > 415 || proportion < 0.65 || proportion > 0.75) {
                    layer.msg("图片最宽不大于295px, 最高不大于415px，并且宽和高 的比例 在0.7左右", {time:5000});
                    $("#thumbnail").val("").focus();
                    return false;
                } else {
                    //验证图片的格式和尺寸结束
                    //上传
                    var totalBytes = files[0].size;
                    $("#thumbnail").attr("disabled", "disabled");

                    formData.set("{{ _csrfKey }}", "{{ _csrf }}");
                    formData.set('Filedata', files[0]);
                    formData.set('type','staff_photo');

                    // XMLHttpRequest 对象
                    xhr.upload.onprogress = function (ev) {
                        var percent = 0;
                        if (ev.lengthComputable) {
                            percent = parseInt(100 * ev.loaded / ev.total);
                            //$("#thumbnailProgress").width(percent + "%");
                        }
                    };
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState == 4) {
                            if (xhr.status == 200) {
                                $('#previewImage').attr('src', '{{ _config['upload_url'] }}' + xhr.responseText).show();
                                $('.btn_remove').show();
                                $('.user_image').val(xhr.responseText);
                            } else {
                                alert(xhr.responseText)
                            }
                        }
                        $("#thumbnailProgress").width("0%");
                        $('#thumbnail').removeAttr("disabled");
                    };
                    xhr.open("post", "{{url('admin/upload')}}", true);
                    xhr.send(formData);
                }
            });
        }

        function getImageWidthAndHeight(file, callback) {
            var _URL = window.URL || window.webkitURL;
            var img = new Image();
            img.onload = function () {
                callback && callback({"width": this.width, "height": this.height, "filesize": file.size});
            };
            img.src = _URL.createObjectURL(file);
        }
    </script>

{% endblock %}