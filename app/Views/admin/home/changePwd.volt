{% extends "layout/main.volt" %}

{% block content %}

    <div class="crumb_warp">
        <i class="fa fa-home"></i> <a href="{{url('admin/home')}}">首页</a> &raquo; 修改密码
    </div>
    <div class="result_wrap">
        <div class="result_title">
            <h3>修改密码</h3>
            {{ content() }}
            <p><?php $this->flashSession->output() ?></p>
        </div>
    </div>
    <div class="result_wrap">
        <form action="" id="edit_form">
            <div class="box-body">
                <div class="form-group">
                    <label for="exampleInputEmail1">原密码</label><span for="old_pass" class="error"></span>
                    <input type="password" class="form-control" name="old_pass" value="">
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="exampleInputEmail1">新密码</label><span for="new_pass" class="error"></span>
                    <input type="password" class="form-control" id="new_pass" name="new_pass" value="">
                    <input type="hidden" class="form-control" id="user_pass" name="user_pass" value="">
                </div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="exampleInputEmail1">确认密码</label><span for="confirm_pass" class="error"></span>
                    <input type="password" class="form-control" name="confirm_pass" value="">
                </div>
            </div>
            <!-- /.box-body -->
            <div class="box-body">
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">提交</button>
                </div>
            </div>
        </form>
    </div>

    {{ javascript_include('js/lib/validate/jquery.validate.js') }}
    {{ javascript_include('js/lib/jquery.md5.js') }}
    <script>
        $(function () {
            $("#edit_form").validate({
                submitHandler: submitHandler,
                errorElement: "span",
                rules: {
                    old_pass: {
                        required: true,
                        minlength: 6,
                        remote:{
                            url: "{{url('/admin/authpwd')}}",
                            type: "post",               //数据发送方式
                            dataType: "json",
                            data: {
                                "{{ _csrfKey }}": "{{ _csrf }}",
                                old_pass: function () {
                                    return $.md5($("input[name='old_pass']").val());
                                }
                            },
                            dataFilter: function (data) {
                                var json = $.parseJSON(data);
                                if (json.status == 200) {
                                    return json.flag;
                                } else {
                                    return false;
                                }
                            },
                            error: function(){
                                layer.msg("系统错误", {icon: 2});
                            }
                        }
                    },
                    new_pass: {
                        required: true,
                        minlength: 6
                    },
                    confirm_pass: {
                        required: true,
                        minlength: 6,
                        equalTo:"#new_pass"
                    }
                },
                messages: {
                    old_pass: {
                        required: "请输入你的原密码",
                        minlength: "密码长度不能小于 6 个字符",
                        remote : '原密码错误'
                    },
                    new_pass: {
                        required: "请输入密码",
                        minlength: "密码长度不能小于 6 个字符"
                    },
                    confirm_pass: {
                        required: "请确认密码",
                        minlength: "密码长度不能小于 6 个字符",
                        equalTo: "两次密码输入不一致"
                    }
                }
            });
        });

        function submitHandler() {
            $("input[name='user_pass']").val($.md5($("input[name='new_pass']").val()));
            $.ajax({
                type: 'post',
                url: '{{url('admin/updatepwd')}}',
                dataType: 'JSON',
                data: {
                    "{{ _csrfKey }}": "{{ _csrf }}",
                    'user_pass': $("input[name='user_pass']").val()
                },
                success: function (data) {
                    if (data.status == 201) {
                        layer.open({
                            title: '提示',
                            content: data.msg,
                            btn: ['确认'],
                            yes: function() {
                                location.href = "/admin/logout";
                            },
                            cancel: function() {
                                location.href = "/admin/logout";
                            }
                        });
                    } else {
                        layer.msg(data.msg, {icon: 5});
                    }
                },
                error: function () {
                    layer.msg('操作失败，请稍后重试！', {icon: 2});
                }
            });
        }
    </script>

{% endblock %}