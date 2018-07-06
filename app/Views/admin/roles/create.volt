{% extends "layout/main.volt" %}

{% block content %}

    <div class="crumb_warp">
        <!--<i class="fa fa-bell"></i> 欢迎使用登陆网站后台，建站的首选工具。-->
        <i class="fa fa-home"></i> <a href="{{url('admin/home')}}">首页</a> &raquo; {{ role is defined ? '编辑角色' : '添加角色' }}
    </div>
    <!--结果集标题与导航组件 开始-->
    <div class="result_wrap">
        <div class="result_title">
            <h3>{{ role is defined ? '编辑角色' : '添加角色' }}</h3>
            {{ content() }}
            <p><?php $this->flashSession->output() ?></p>
        </div>

        <div class="result_wrap">
            <form role="form" action="{{ url('admin/roles/') ~ (role is defined ? 'update' : 'save') }}" method="POST">
                {% if role is defined and role is not empty %}
                    <input type="hidden" name="id" value="{{role.id}}">
                {% endif %}
                <input type="hidden" name="{{ _csrfKey }}" value="{{ _csrf }}"/>
                <div class="box-body">
                    <div class="form-group">
                        <label for="exampleInputEmail1">角色名</label>
                        <input type="text" class="form-control" name="name" value="{{ role is defined ? role.name : null }}">
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="exampleInputEmail1">角色编码</label>
                        <input type="text" class="form-control" name="code" value="{{ role is defined ? role.code : null }}">
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="exampleInputEmail1">描述</label>
                        <input type="text" class="form-control" name="description" value="{{ role is defined ? role.description : null }}">
                    </div>
                </div>
                <!-- /.box-body -->
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">提交</button>
                </div>
            </form>
        </div>
    </div>

{% endblock %}