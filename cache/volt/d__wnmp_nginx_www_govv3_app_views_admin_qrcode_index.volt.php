<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <title>橙视光标智慧科室牌-后台管理</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <?= $this->tag->stylesheetLink('/admin/org/bootstrap/css/bootstrap.min.css') ?>
    <?= $this->tag->stylesheetLink('/adminlte/dist/css/AdminLTE.min.css') ?>
    <?= $this->tag->stylesheetLink('/adminlte/dist/css/skins/_all-skins.min.css') ?>
    <?= $this->tag->stylesheetLink('/admin/style/css/ch-ui.admin.css') ?>
    <?= $this->tag->stylesheetLink('/admin/style/font/css/font-awesome.min.css') ?>
    <?= $this->tag->stylesheetLink('/admin/style/css/admin.admin.css') ?>

    <?= $this->tag->javascriptInclude('js/lib/jQuery/jquery-2.2.3.min.js') ?>
    <?= $this->tag->javascriptInclude('admin/org/bootstrap/js/bootstrap.min.js') ?>
    <?= $this->tag->javascriptInclude('admin/org/multiselect/bootstrap-multiselect.js') ?>
</head>
<script>
    //ueditor编辑器的工具栏配置
    var ueditor_toolbars = {
        toolbars: [["fullscreen","source","undo","redo","bold","indent","italic","underline","fontborder","strikethrough","superscript","formatmatch","pasteplain","subscript","preview","horizontal","insertunorderedlist","insertorderedlist","justifyleft","justifycenter","justifyright","justifyjustify","removeformat","simpleupload","snapscreen","emotion","inserttable","insertrow","insertcol","mergeright","mergedown","deleterow","deletecol","splittorows","splittocols","splittocells","deletecaption","inserttitle","mergecells","deletetable","cleardoc","fontsize","paragraph","link'","spechars","searchreplace","forecolor","backcolor","rowspacingtop","rowspacingbottom","imagecenter","lineheight","customstyle","autotypeset","background"]]};
    var multiselect_option = {
        // 自定义参数，按自己需求定义
        nonSelectedText : '--请选择--',
        enableFiltering: true,
        maxHeight : 350,
        includeSelectAllOption : true,
        numberDisplayed : 5
    };
    var multiselect_option_no_search = {
        // 自定义参数，按自己需求定义
        nonSelectedText : '--请选择--',
        maxHeight : 350,
        includeSelectAllOption : true,
        numberDisplayed : 5
    };
</script>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
    <style>
    #rightPos{
        position: absolute!important;
        right: -50px!important;
    }
</style>
<header class="main-header">
    <!-- Logo -->
    <a href="<?= $this->url->get('admin/home') ?>" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini">CL</span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg">
            <?= (empty($_session['project_id']) ? '后台管理中心' : $_session['project_name']) ?>
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
                        <img src="<?= (isset($_session['user_image']) && !empty($_session['user_image']) ? $_config['upload_url'] . $_session['user_image'] : $_config['default_staff_img']) ?>" class="user-image" alt="User Image">
                        <span class="hidden-xs"><?= $_session['user_name'] ?></span>
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
    <aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu">
            <?php if ($_session['user_is_super'] || ($_session['user_is_admin'] && $_session['project_id'] == '')) { ?>
            <li class="treeview <?= ($_Controller == 'ProjectController' || $_Controller == 'HomeController' ? 'active' : '') ?>">
                <a href="#">
                    <i class="fa fa-university"></i> <span>单位管理</span>
                    <span class="pull-right-container"></span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/admin/project/create"><i class="fa fa-circle-o"></i> 添加单位</a></li>
                    <li><a href="/admin/project"><i class="fa fa-circle-o"></i> 单位列表</a></li>
                    <li><a href="/admin/project/createadmin"><i class="fa fa-circle-o"></i> 创建管理员</a></li>
                    <li><a href="/admin/project/adminuserlist"><i class="fa fa-circle-o"></i> 管理员列表</a></li>
                </ul>
            </li>
            <?php } else { ?>
            <li class="treeview <?= ($_Controller == 'ProjectController' || $_Controller == 'HomeController' ? 'active' : '') ?>">
                <a href="#">
                    <i class="fa fa-university"></i> <span>单位详情</span>
                    <span class="pull-right-container"></span>
                </a>
                <ul class="treeview-menu">
                    
                    <li><a href="/admin/project/<?= $_session['project_id'] ?>/edit"><i class="fa fa-circle-o"></i> 编辑单位详情</a></li>
                </ul>
            </li>
            <?php } ?>

            <?php if ($_session['user_is_super'] || $_session['user_is_admin']) { ?>
            <li class="treeview <?= (($_Controller == 'DepartmentController' || $_Controller == 'QrcodeController' || $_Controller == 'NoticeController') ? 'active' : '') ?>">
                <a href="#">
                    <i class="fa fa-th-list"></i> <span>科室管理</span>
                    <span class="pull-right-container"></span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/admin/department/create"><i class="fa fa-circle-o"></i> 添加科室</a></li>
                    <li><a href="/admin/department"><i class="fa fa-circle-o"></i> 科室列表</a></li>
                    <?php if ($_session['user_is_super'] || ($_session['user_is_admin'] && $_session['project_id'] == '')) { ?>
                        <li><a href="/admin/qrcode/0/edit"><i class="fa fa-qrcode"></i> 二维码绑定/编辑</a></li>
                    <?php } ?>
                    <li><a href="/admin/qrcode"><i class="fa fa-qrcode"></i> 二维码列表</a></li>
                    <li><a href="/admin/notice/create"><i class="fa fa-circle-o"></i> 添加告示</a></li>
                    <li><a href="/admin/notice"><i class="fa fa-circle-o"></i> 告示列表</a></li>
                </ul>
            </li>
            <?php } ?>

            <?php if ($_session['user_is_super'] || $_session['user_is_admin']) { ?>
            <li class="treeview <?= ($_Controller == 'SectionController' ? 'active' : '') ?>">
                <a href="#">
                    <i class="fa fa-list-alt"></i> <span>部门管理</span>
                    <span class="pull-right-container"></span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/admin/section/create"><i class="fa fa-circle-o"></i> 添加部门</a></li>
                    <li><a href="/admin/section"><i class="fa fa-circle-o"></i> 部门列表</a></li>
                </ul>
            </li>
            <?php } ?>

            <?php if ($_session['user_is_super'] || $_session['user_is_admin']) { ?>
            <li class="treeview <?= ($_Controller == 'StatusController' ? 'active' : '') ?>">
                <a href="#">
                    <i class="fa fa-calendar"></i> <span>事件管理</span>
                    <span class="pull-right-container"></span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/admin/status/settingworktimelist"><i class="fa fa-circle-o"></i> 设置工作时间</a>
                    </li>
                    <li><a href="/admin/status/create"><i class="fa fa-circle-o"></i> 添加事件</a></li>
                    <li><a href="/admin/status"><i class="fa fa-circle-o"></i> 事件列表</a></li>
                    <li><a href="/admin/status/userStatus"><i class="fa fa-circle-o"></i> 已设事件</a></li>
                    <li><a href="/admin/status/workerStatusList"><i class="fa fa-circle-o"></i> 员工状态</a>
                    </li>
                </ul>
            </li>
            <?php } ?>

            <?php if ($_session['user_is_super'] || $_session['user_is_admin']) { ?>
            <li class="treeview <?= ($_Controller == 'UserController' ? 'active' : '') ?>">
                <a href="#">
                    <i class="fa fa-male"></i> <span>人员管理</span>
                    <span class="pull-right-container"></span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/admin/users/create"><i class="fa fa-circle-o"></i> 添加人员</a></li>
                    <li><a href="/admin/users"><i class="fa fa-circle-o"></i> 人员列表</a></li>
                    <li><a href="/admin/users/belongs"><i class="fa fa-circle-o"></i> 归属管理</a></li>
                </ul>
            </li>
            <?php } ?>

            <?php if ($_session['user_is_super'] || $_session['user_is_admin']) { ?>
            <li class="treeview <?= ($_Controller == 'CommentController' ? 'active' : '') ?>">
                <a href="#">
                    <i class="fa fa-envelope"></i> <span>留言管理</span>
                    <span class="pull-right-container"></span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/admin/comment"><i class="fa fa-circle-o"></i> 留言列表</a></li>
                </ul>
            </li>
            <?php } ?>

            
            
                
                    
                    
                
                
                    
                    
                    
                
            
            
        </ul>
    </section>
</aside>

    <div class="content-wrapper">
        

    <style>
        .wrap{
            width: 400px;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }
    </style>
    <!--面包屑导航 开始-->
    <?= $this->tag->stylesheetLink('admin/org/bigcolorpicker/css/jquery.bigcolorpicker.css') ?>
    <div class="crumb_warp">
        <i class="fa fa-home"></i> <a href="<?= $this->url->get('admin/home') ?>">首页</a> &raquo; 科室二维码列表
    </div>

    <!--面包屑导航 结束-->
    <!--普通科员不显示查询-->
    <?php if (empty($_session['project_id']) || $_session['user_is_admin']) { ?>
    <div class="search_wrap">
        <form action="<?= $this->url->get('admin/qrcode') ?>" method="get" name="search_form">
            <table class="search_tab">
                <tr>
                    
                    <?php $this->partial('layout/search_list1', ['type' => 0]); ?>
                    <td><input type="submit"  class="btn btn-info" value="查询"></td>
                </tr>
            </table>
        </form>
    </div>
    <?php } ?>
    <?= $this->getContent() ?>
    <p><?php $this->flashSession->output() ?></p>

    <div class="result_wrap">
        <div class="result_content">
            <table class="list_tab">
                <tr>
                    <th class="tc">QrCodeID</th>
                    <th>单位</th>
                    <th>科室</th>
                    <th class="wrap">Link</th>
                    <th>说明</th>
                    <th>操作</th>
                </tr>
                <?php if (!empty($data['list'])) { ?>
                    <?php foreach ($data['list']->items as $v) { ?>
                        <tr>
                            <td class="tc"><?= $v->forwards->forward_id ?></td>
                            <td><?= $v->project_name ?></td>
                            <td><?= $v->department_name ?></td>
                            <td ><a class="wrap" href="<?= $APP_URL . '/forward/' . $v->forwards->forward_id ?>" target="_blank" style="display:inline;" title="<?= $APP_URL . '/forward/' . $v->forwards->forward_id ?>"><?= $APP_URL . '/forward/' . $v->forwards->forward_id ?></a></td>
                            <td><?= $v->forwards->forward_introduction ?></td>
                            <td>
                                <?php if ($_session['user_is_super'] || ($_session['user_is_admin'] && $_session['project_id'] == '')) { ?>
                                <a href="<?= $this->url->get('admin/qrcode/' . $v->forwards->id . '/edit') ?>">编辑</a>
                                <a href="javascript:;" onclick="del(<?= $v->forwards->id ?>)">删除</a>
                                <?php } ?>
                                <a href="javascript:;" onclick="get_qrcode(<?= $v->forwards->id ?>)">二维码</a>
                            </td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td col="6">暂无数据</td>
                    </tr>
                <?php } ?>
            </table>

            <div class="page_list clear" >
                <label>共 <?= $data['list']->total_items ?> 条记录</label>
                <?php if ($data['list']->total_pages > 1) { ?>
                    <div style="float: right">
                        <ul class="paginate">
                            <li class="disabled"><span>总计: <?= $data['list']->total_pages ?> 页</span></li>
                            <li class="active"><span>当前第: <?= $data['list']->current ?> 页</span></li>
                            <?php if (isset($input['project_id']) || isset($input['department_id'])) { ?>
                                <?php if ($data['list']->current == 1) { ?>
                                    <li class="disabled"><span>第一页</span></li>
                                <?php } else { ?>
                                    <li><a href="/admin/qrcode?project_id=<?= (isset($input['project_id']) ? $input['project_id'] : '') ?>&department_id=<?= (isset($input['department_id']) ? $input['department_id'] : '') ?>&page=1">第一页</a></li>
                                <?php } ?>
                                <?php if ($data['list']->current == 1) { ?>
                                    <li class="disabled"><span>上一页</span></li>
                                <?php } else { ?>
                                    <li><a href="/admin/qrcode?project_id=<?= (isset($input['project_id']) ? $input['project_id'] : '') ?>&department_id=<?= (isset($input['department_id']) ? $input['department_id'] : '') ?>&page=<?= $data['list']->before ?>">上一页</a></li>
                                <?php } ?>
                                <?php if ($data['list']->current == $data['list']->last || $data['list']->last == 0) { ?>
                                    <li class="disabled"><span>下一页</span></li>
                                <?php } else { ?>
                                    <li><a href="/admin/qrcode?project_id=<?= (isset($input['project_id']) ? $input['project_id'] : '') ?>&department_id=<?= (isset($input['department_id']) ? $input['department_id'] : '') ?>&page=<?= $data['list']->next ?>">下一页</a></li>
                                <?php } ?>
                                <?php if ($data['list']->current == $data['list']->last || $data['list']->last == 0) { ?>
                                    <li class="disabled"><span>最后一页</span></li>
                                <?php } else { ?>
                                    <li><a href="/admin/qrcode?project_id=<?= (isset($input['project_id']) ? $input['project_id'] : '') ?>&department_id=<?= (isset($input['department_id']) ? $input['department_id'] : '') ?>&page=<?= $data['list']->last ?>">最后一页</a></li>
                                <?php } ?>
                            <?php } else { ?>
                                <?php if ($data['list']->current == 1) { ?>
                                    <li class="disabled"><span>第一页</span></li>
                                <?php } else { ?>
                                    <li><a href="/admin/qrcode">第一页</a></li>
                                <?php } ?>
                                <?php if ($data['list']->current == 1) { ?>
                                    <li class="disabled"><span>上一页</span></li>
                                <?php } else { ?>
                                    <li><a href="/admin/qrcode?page=<?= $data['list']->before ?>">上一页</a></li>
                                <?php } ?>
                                <?php if ($data['list']->current == $data['list']->last || $data['list']->last == 0) { ?>
                                    <li class="disabled"><span>下一页</span></li>
                                <?php } else { ?>
                                    <li><a href="/admin/qrcode?page=<?= $data['list']->next ?>">下一页</a></li>
                                <?php } ?>
                                <?php if ($data['list']->current == $data['list']->last || $data['list']->last == 0) { ?>
                                    <li class="disabled"><span>最后一页</span></li>
                                <?php } else { ?>
                                    <li><a href="/admin/qrcode?page=<?= $data['list']->last ?>">最后一页</a></li>
                                <?php } ?>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>
            </div>

        </div>
    </div>
    <!--搜索结果页面 列表 结束-->

    <style>
        .result_content ul li span {
            font-size: 15px;
            padding: 6px 12px;
        }
        .active {
            color: #fff;
            cursor: default;
            background-color: #337ab7;
            border-color: #337ab7;
        }
        .disabled {
            color: #777;
            cursor: not-allowed;
            background-color: #fff;
            border-color: #ddd;
        }
    </style>
    <script>

        //生成二维码
        function get_qrcode(id){
            $.ajax({
                url: '<?= $this->url->get('admin/qrcode/ajaxGetForwardQrCode') ?>',
                type: 'POST',
                dataType :'JSON',
                data: {
                    "<?= $_csrfKey ?>": "<?= $_csrf ?>",
                    'id': id,
                },
                success: function(data){
                    if(data.status == 200){
                        var content = '<div style="padding: 10px">' + '<img src="data:image/png;base64,'+data.msg+'" width="150px" height="150px">'+ '</div>';
                        layer.open({
                            title:'二维码',
                            type: 1,
                            area: ['168px', '215px'], //宽高
                            shadeClose: true,
                            content: content
                        });
                    }else{
                        layer.msg(data.msg, {icon: 5});
                    }
                },
                error: function() {
                    layer.msg('操作失败，请稍后重试！', {icon: 2});
                }
            })
        }

        //删除
        function del(id) {
            layer.confirm('您确定要删除吗？', {
                btn: ['确定','取消'] //按钮
            }, function(){
                $.ajax({
                    url: "<?= $this->url->get('admin/qrcode/delete') ?>",
                    type: 'POST',
                    dataType :'JSON',
                    data: {
                        '_method':'delete',
                        'id': id,
                        "<?= $_csrfKey ?>": "<?= $_csrf ?>",
                    },
                    success: function(data){
                        if(data.status == 201) {
                            layer.msg(data.msg, {
                                icon: 6,
                                time: 2000, //2s后自动关闭
                            },function (){
                                location.reload();
                            });
                        }else{
                            layer.msg(data.msg, {icon: 5});
                        }
                    },
                    error: function() {
                        layer.msg('操作失败，请稍后重试！', {icon: 2});
                    }
                })
            });
        }
    </script>


    </div>
    <script>
    $(function(){
        //分页url重写
        if($(".page_list").length > 0){
            var myhref = newhref = location.href;
            var flag = (myhref.indexOf("?") == -1) ? false : true;
            var https_flag = myhref.indexOf("https") == -1  ? false : true ;

            if(flag || https_flag){
                var has_page = myhref.indexOf("page") > -1 ? true : false;
                var link =  myhref.indexOf("?") > -1  &&  myhref.indexOf("?page") == -1 ? '&' : '?';
                if(has_page){
                    myhref = myhref.substring(0,myhref.indexOf("page")-1);
                }

                $.each($(".pagination a"),function(){
//                    if(flag){
                    var page = $(this).html();  //页码
                    if(isNaN(page)){   //标签按钮
                        if(page == '«'){ //上一页按钮
                            page = $(".pagination li.active span").html()*1-1;
                        }else{
                            page = $(".pagination li.active span").html()*1+1;
                        }
                    }
                    var newhref =  myhref+link+'page='+page;
//                    }
                    $(this).attr('href',newhref);
                })
            }
        }
        if($(".sidebar-menu li").length <= 0){
            var menu = '<li class="treeview active">'+
                '<a href="#">'+
                '<i class="fa fa-dashboard"></i> <span>科室管理</span>'+
                '<span class="pull-right-container"></span>'+
                '</a>'+
                '<ul class="treeview-menu">'+
                '<li><a href="/admin/category/<?= $_session['department_id'] ?>/edit"><i class="fa fa-circle-o"></i> 科室编辑</a></li></li>'+
                '<li><a href="/admin/qrcode"><i class="fa fa-circle-o"></i> 二维码列表</a></li>'+
                '<li><a href="/admin/notice/create"><i class="fa fa-circle-o"></i> 添加告示</a></li>'+
                '<li><a href="/admin/notice"><i class="fa fa-circle-o"></i> 告示列表</a></li>'+
                '</ul>'+
                '</li>';
            $(".sidebar-menu").append(menu);
        }

    });
    // 得到单位列表通过项目id
    function get_options_by_project(obj, type){
        if(type < 0){
            return false;
        }
        var project_id = $(obj).val();
        var url = '/admin/ajaxGetOptionsByProject';
        type = !!type ? type : 0;
        if(type >= 0){
            $('.department_list').html('<option value="0"> 请先选择单位</option>');
            $(".department_list").multiselect("destroy").multiselect(multiselect_option);
        }
        if(type >= 1){
            $('.section_list').html('<option value="0"> 请先选择单位</option>');
            $(".section_list").multiselect("destroy").multiselect(multiselect_option);
        }
        if(type >= 2){
            $('.status_list').html('<option value="0"> 请先选择单位</option>');
            $(".status_list").multiselect("destroy").multiselect(multiselect_option);
        }
        if(project_id == ''){
            return false;
        }

        $.ajax({
            url: url,
            type: 'POST',
            dataType :'JSON',
            data: {
                "<?= $_csrfKey ?>": "<?= $_csrf ?>",
                'project_id' : project_id,
                'type': type,
            },
            success: function(data){
                if(data.status == 200){
                    if(type >= 0) {
                        var department_list = section_list = status_list = '<option value="0">请选择</option>';
                        //科室列表
                        $.each(data.msg.department_list, function (k, v) {
                            department_list += '<option value="' + v.department_id + '"> ' + v.department_name + '</option>';
                        });
                        $('.department_list').html(department_list);
                        $(".department_list").multiselect("destroy").multiselect(multiselect_option);
                    }
                    if(type >= 1){
                        //部门列表
                        $.each(data.msg.section_list,function(k,v){
                            section_list += '<option value="'+v.section_id+'"> '+v.section_name+'</option>';
                        });
                        $('.section_list').html(section_list);
                        $(".section_list").multiselect("destroy").multiselect(multiselect_option);
                    }
                    if(type >= 2){
                        //事件列表
                        $.each(data.msg.status_list,function(k,v){
                            status_list += '<option value="'+v.status_id+'"> '+v.status_name+'</option>';
                        });
                        $('.status_list').html(status_list);
                        $(".status_list").multiselect("destroy").multiselect(multiselect_option);
                    }
                } else {
                    layer.msg(data.msg, {icon: 5});
                }
            },
            error: function() {
                layer.msg('操作失败，请稍后重试！', {icon: 2});
            }
        })
    }
</script>
    <div class="control-sidebar-bg"></div>
</div>

<?= $this->tag->javascriptInclude('adminlte/dist/js/app.min.js') ?>
<?= $this->tag->javascriptInclude('admin/style/js/jquery.form.js') ?>
<?= $this->tag->javascriptInclude('admin/style/js/ch-ui.admin.js') ?>
<?= $this->tag->javascriptInclude('org/layer/layer.js') ?>
<?= $this->tag->javascriptInclude('js/lib/validate/jquery.validate.js') ?>
<?= $this->tag->javascriptInclude('js/lib/validate/messages_cn.js') ?>
<?= $this->tag->javascriptInclude('admin/style/js/admin.js') ?>
</body>
</html>