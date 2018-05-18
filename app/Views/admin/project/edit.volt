{% extends "layout/main.volt" %}

{% block content %}

    <style>
        #editor img{
            width: 100%  !important;
        }
    </style>
    <!--面包屑导航 开始-->
    <div class="crumb_warp">
        <i class="fa fa-home"></i> <a href="{{url('admin/home')}}">首页</a> &raquo; 单位管理
    </div>
    <!--面包屑导航 结束-->

    <!--结果集标题与导航组件 开始-->
    <div class="result_wrap">
        <div class="result_title">
            <h3>编辑单位信息</h3>
            {{ content() }}
            <p><?php $this->flashSession->output() ?></p>
        </div>
        {% if _session['user_is_super'] or (_session['user_is_admin'] and _session['project_id'] == '') %}
        <div class="result_content">
            <div class="short_wrap">
                <a href="{{url('admin/project/create')}}"><i class="fa fa-plus"></i>添加单位</a>
                <a href="{{url('admin/project')}}"><i class="fa fa-recycle"></i>全部单位</a>
            </div>
        </div>
        {% endif %}
    </div>
    <!--结果集标题与导航组件 结束-->

    <div class="result_wrap">
        <form action="{{ url('admin/project/update') }}" method="post">
            <input type="hidden" name="project_id" value="{{ unit.project_id }}">
            <input type="hidden" name="_method" value="PUT">
            <input type="hidden" name="{{ _csrfKey }}" value="{{ _csrf }}"/>
            <table class="add_tab">
                <tbody>
                <tr>
                    <th><i class="require">*</i>名称：</th>
                    <td>
                        <input type="text" name="project_name" value="{{ unit.project_name }}" style="width: 350px !important;" required maxlength="100">
                        <span><i class="fa fa-exclamation-circle yellow"></i>名称必须填写</span>
                    </td>
                </tr>
                <tr>
                    <th>单位主图：</th>

                    <td>
                        <span>
                            <i class="fa fa-exclamation-circle yellow"></i>单位主图用于群众端主页背景(点击提交才生效)，请上传宽大于1080px,高大于1920px，且宽高比为9:16的图片
                        </span>
                        <div style="position: relative;width: 90px;">
                            <a href="/upload/{{ unit.project_image }}" target="_blank"><img src="{{ unit.project_image is not empty ? url('/upload/' ~ unit.project_image) : '' }}"  id="previewImage" style="position: relative;max-width: 90px; max-height:160px;@if(empty($field->project_image)) display: none @endif"/>
                            </a>
                            <span class="close_img btn_remove {{ unit.project_image is empty ? 'hide' : '' }}">X</span>
                        </div>
                        <input id="thumbnail" type="file" multiple onchange="UpLoadFile()">

                        <input type="hidden" name="project_image" class="project_image" value="{{ unit.project_image }}">
                    </td>

                </tr>
                <tr>
                    <th>简介：</th>
                    <td>
                        {{ javascript_include('admin/org/ueditor/ueditor.config.js') }}
                        {{ javascript_include('admin/org/ueditor/ueditor.all.min.js') }}
                        {{ javascript_include('admin/org/ueditor/lang/zh-cn/zh-cn.js') }}
                        <script id="editor" name="project_profile" type="text/plain" style="width: 600px;height: 380px;">
                            {{ unit.project_profile }}
                        </script>
                        <script type="text/javascript">
                            var ue = UE.getEditor('editor',ueditor_toolbars);
                        </script>
                        <style>
                            .edui-default{line-height: 28px;}
                            div.edui-combox-body,div.edui-button-body,div.edui-splitbutton-body
                            {overflow: hidden; height:20px;}
                            div.edui-box{overflow: hidden; height:22px;}
                        </style>
                    </td>
                </tr>
                {% if _session['user_is_super'] or (_session['user_is_admin'] and _session['project_id'] == '') %}
                <tr>
                    <th>状态</th>
                    <td>
                        <input type="radio" class="lg" id="open_p" name="project_status" value="1" {{ unit.project_status == 1 ? 'checked' : '' }}><label for="open_p">开启</label>
                        <input type="radio" class="lg" id="close_p" name="project_status" value="0" {{ unit.project_status == 0 ? 'checked' : '' }}><label for="close_p">关闭</label>
                    </td>
                </tr>
                {% endif %}
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
        $(function () {
            $('.btn_remove').on('click', function () {
                $('input[name=project_image]').val('');
                $('#previewImage').hide();
                $('.btn_remove').hide();
            });
        })
        function UpLoadFile() {
            var xhr = new XMLHttpRequest();
            xhr.overrideMimeType('text/plain; charset=utf-8');
            // FormData 对象
            var formData = new FormData();
            var files = document.getElementById('thumbnail').files;
            if (files[0] == undefined) {
                layer.msg("No file chosen");
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
                //0.5625
                if (obj.width < 1080 || obj.height < 1920 || proportion < 0.5 || proportion > 0.57) {
                    layer.msg("为了单位主页的布局美观，请上传宽大于1080px,高不大于1920px，宽高比例为9:16的图片",{time:5000});
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
                    formData.set('type', 'project_image');
                    formData.set('Filedata', files[0]);

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
                                if ($(".user_pic").hasClass('hide')) {
                                    $(".user_pic").removeClass('hide')
                                }
                                $('#previewImage').attr('src', '{{_config['upload_url']}}' + xhr.responseText);
                                $('#previewImage').show();
                                $('.btn_remove').show();
                                $('.project_image').val(xhr.responseText);
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