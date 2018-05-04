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
                '<li><a href="/admin/category/{{ _session['department_id'] }}/edit"><i class="fa fa-circle-o"></i> 科室编辑</a></li></li>'+
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
                "{{ _csrfKey }}": "{{ _csrf }}",
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