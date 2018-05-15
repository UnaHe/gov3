<style>
    #rightPos{
        position: absolute!important;
        right: -50px!important;
    }
</style>
<header class="main-header">
    <!-- Logo -->
    <a href="{{ url('admin/home') }}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini">CL</span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg">
            {{ _session['project_id'] is empty ? '后台管理中心' : _session['project_name'] }}
        </span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <a href="" class="dropdown-toggle" data-toggle="dropdown">
                        <img src="{{ _session['user_image'] is defined and _session['user_image'] is not empty ? _config['upload_url'] ~ _session['user_image'] : _config['default_staff_img'] }}" class="user-image" alt="User Image">
                        <span class="hidden-xs">{{_session['user_name']}}</span>
                    </a>
                    <ul id="rightPos" class="dropdown-menu" style="width: 85px;margin: 0px">
                        <!-- User image -->
                        <!-- Menu Footer-->

                        <li class="user-footer">
                            <div class="" style="width: 100px">
                                <a href="/admin/changepwd" class="btn btn-default btn-flat">修改密码</a>
                            </div>
                            <div class="" style="width: 100px;margin-top: 5px">
                                <a href="/admin/logout" class="btn btn-default btn-flat">退出登录</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>