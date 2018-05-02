{% extends "layout/main.volt" %} 

{% block content %}

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-10 col-xs-6">
                <div class="box">

                    <div class="box-header with-border">
                        <h3 class="box-title">权限列表</h3>
                        {{ content() }}
                        <p><?php $this->flashSession->output() ?></p>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <form action="{{ url('/admin/roles/permission') }}" method="POST">
                            <input type="hidden" name="role_id" value="{{ roleId }}"/>
                            <input type="hidden" name="{{ _csrfKey }}" value="{{ _csrf }}"/>
                            <div class="form-group">
                                {% for permission in permissions %}
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="permissions[]" value="{{permission.id}}"
                                                <?php if (array_key_exists($permission->id, $myPermissionsArray)) { ?>
                                                    checked
                                                <?php } ?>
                                            />
                                            {{permission.name}}
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