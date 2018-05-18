{% extends "layout/main.volt" %}

{% block content %}
    <!--面包屑导航 开始-->
    <div class="crumb_warp">
        <!--<i class="fa fa-bell"></i> 欢迎使用登陆网站后台，建站的首选工具。-->
        <i class="fa fa-home"></i> <a href="{{url('admin/home')}}">首页</a> &raquo; 员工管理
    </div>
    <!--面包屑导航 结束-->

    <!--结果集标题与导航组件 开始-->
    <div class="result_wrap">
        <div class="result_title">
            <h3>编辑员工</h3>
            {{ content() }}
            <p><?php $this->flashSession->output() ?></p>
        </div>
        <div class="result_content">
            <div class="short_wrap">
                <a href="{{url('admin/users/create')}}"><i class="fa fa-plus"></i>添加员工</a>
                <a href="{{url('admin/users')}}"><i class="fa fa-recycle"></i>全部员工</a>
            </div>
        </div>
        <div class="result_title">
            <div style="background-color: #e5e9ec;">
                &nbsp;&nbsp;&nbsp;&nbsp; 上传的员工寸照，宽最多为295px,  高最多为415px,  并且宽和高的比例在7:10左右
            </div>
        </div>

    </div>
    <!--结果集标题与导航组件 结束-->

    <div class="result_wrap">
        <form action="{{url('admin/users/update')}}" method="post" enctype="multipart/form-data" id="add-form" name="add-form">
            <input type="hidden" name="_method" value="put">
            <input type="hidden" name="{{ _csrfKey }}" value="{{ _csrf }}"/>
            <input type="hidden" name="user_id" value="{{ field['user_id'] }}"/>
            <table class="add_tab">
                <tbody>
                <tr class="project_type">
                    <th>单位：</th>
                    <td>
                        {{field['project_name']}}
                    </td>
                </tr>
                {% Include 'layout/common_tr' with ['type': 2,'department_id': field['department_id'],'section_id':field['section_id']] %}
                <tr>
                    <th><i class="require">*</i> 姓名：</th>
                    <td>
                        <input type="text" class="lg" name="user_name" value="{{field['user_name']}}" required/>
                    </td>
                </tr>
                <tr>
                    <th>寸照：</th>
                    <td>
                        <input type="hidden" name="user_image" class="user_image" value="{{field['user_image']}}">
                        <input id="thumbnail" name="thumbnail" type="file" multiple onchange="UpLoadFile()">
                        <img alt="" id="previewImage"  src="{{field['user_image'] is empty ? '' : _config['upload_url'] ~ field['user_image']}}" style="max-width: 75px; max-height:100px;display:{{ field['user_image'] is empty ? 'none' : ''}}">
                        <input type="button" value="移除图片" class="btn_remove back btn btn-info" style="display: {{ field['user_image'] is empty ? 'none' : ''}}">
                    </td>
                </tr>

                <tr>
                    <th><i class='require'>*</i> 性别：</th>
                    <td>
                        <input type="radio" name="user_sex" id="user_sex_1" value="1" {{ field['user_sex'] == 1 ? 'checked' : '' }} required/><label for="user_sex_1">男</label>
                        <input type="radio" name="user_sex" id="user_sex_0" value="0" {{ field['user_sex']== 0 ? 'checked' : '' }} required/><label for="user_sex_0">女</label>
                    </td>
                </tr>
                <tr>
                    <th><i class="require">*</i> 年龄：</th>
                    <td>
                        <input type="number" class="lg" name="user_age" value="{{field['user_age']}}" required min="1" max="100"/>
                    </td>
                </tr>
                <tr>
                    <th><i class="require">*</i> 电话：</th>
                    <td>
                        <input type="text" class="lg" name="user_phone" value="{{field['user_phone']}}" required/>
                    </td>
                </tr>
                <tr>
                    <th><i class='require'>*</i> 状态：</th>
                    <td>
                        <input type="radio" name="user_status" id="user_status_1" value="1" {{ field['user_status'] == 1 ? 'checked' : '' }} required/><label for="user_status_1">正常在职</label>
                        <input type="radio" name="user_status" id="user_status_0" value="0" {{ field['user_status'] == 0 ? 'checked' : '' }} required/><label for="user_status_0">不在职</label>
                </tr>
                <tr class="project_type">
                    <th>职务：</th>
                    <td>
                        <input type="text" class="lg" name="user_job" value="{{field['user_job']}}">
                    </td>
                </tr>
                <tr class="project_type">
                    <th>职务内容：</th>
                    <td>
                        <textarea name="user_intro">{{field['user_intro']}}</textarea>
                    </td>
                </tr>
                <tr class="project_type">
                    <th>他的留言：</th>
                    <td>
                        <textarea name="user_comments">{{field['user_comments']}}</textarea>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <input type="submit" class="btn btn-info" value="提交">
                        <input type="button" class="back btn btn-info" onclick="history.go(-1)" value="返回">
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
    <script type="text/javascript">
        $(function(){
            $("#add-form").validate({
                submitHandler: function(form){
                    form.submit();
                },
                errorElement: "span",
                ignore: ".hide",
                rules: {
                    project_id: {
                        required:true,
                        min: 1
                    },
                    department_id: {
                        required:true,
                        min: 1
                    },
                    user_age: {
                        required:true,
                        min: 1
                    },
                    user_phone: {
                        required:true,
                        isMobile: true
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
                    password: {
                        minlength: "<i class='fa fa-exclamation-circle yellow'></i>密码长度不能小于 6 个字符"
                    },
                    user_age: {
                        min: "<i class='fa fa-exclamation-circle yellow'></i>请填写实际年龄",
                        max: "<i class='fa fa-exclamation-circle yellow'></i>请填写实际年龄"
                    }
                }
            });

            var project_id = '{{field['project_id']}}';
            if(!'{{field['project_id']}}'){
                $(".project_type").addClass('hide');
            }
            $('.btn_remove').on('click',function(){
                if ($('#user_thumb_img').attr('src') != '') {
                    $('input[name=user_image]').val('');
                    $('#previewImage').hide();
                    $('.btn_remove').hide();
                }
            });
        });

        function UpLoadFile() {
            var xhr = new XMLHttpRequest();
            xhr.overrideMimeType('text/plain; charset=utf-8');
            // FormData 对象
            var formData = new FormData();
            var files = document.getElementById('thumbnail').files;
            if (files[0] ==undefined){
                alert("No file chosen");
                return;
            }
            //验证图片的格式和尺寸开始
            if(!/.(gif|jpg|png|GIF|JPG|PNG)$/.test($("#thumbnail").val())){
                layer.msg("图片限于gif,jpg,png格式");
                $("#thumbnail").val("").focus();
                return false;
            }
            //宽高
            getImageWidthAndHeight(files[0], function (obj) {
                var proportion = (obj.width * 1) / (obj.height * 1);
                if (obj.width > 295 || obj.height > 415 || proportion < 0.65 || proportion > 0.75) {
                   layer.msg("图片 最宽不大于295px, 最高不大于415px，并且宽和高 的比例 在0.7左右",{time:5000});
                    $("#thumbnail").val("").focus();
                    return false;
                } else {
                    var totalBytes = files[0].size;

                    if (totalBytes > 2097152) {
                        alert("上传图片应小于2兆");
                        return false;
                    }

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
                                $('#previewImage').attr('src', '{{ _config['upload_url'] }}' + xhr.responseText);
                                $('#previewImage').show();
                                $('.btn_remove').show();
                                $('.user_image').val(xhr.responseText);
                            } else {
                                alert(xhr.responseText)
                            }
                        }
    //                                            $("#thumbnailProgress").width("0%");
                        $('#thumbnail').removeAttr("disabled");
                    }
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