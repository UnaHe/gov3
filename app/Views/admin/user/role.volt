{% extends "layout/main.volt" %}

{% block content %}

    <style>
     input[type=radio]{
         margin-top: 4px !important;
     }
    </style>
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-10 col-xs-6">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">角色列表</h3>
                    </div>
                    {{ content() }}
                    <p><?php $this->flashSession->output() ?></p>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <form action="{{ url('admin/users/role') }}" method="POST">
                            <input type="hidden" name="user_id" value="{{userRole.user_id}}"/>
                            <input type="hidden" name="{{ _csrfKey }}" value="{{ _csrf }}"/>
                            <div class="form-group">
                                {% for role in roles %}
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="role" {{ userRole.role_id == role.id ? 'checked' : null }} value="{{ role.id }}" >
                                            {{role.name}}
                                        </label>
                                    </div>
                                {% endfor %}
                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-primary">提交</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

{% endblock %}