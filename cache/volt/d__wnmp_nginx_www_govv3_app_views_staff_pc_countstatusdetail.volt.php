<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!--其他页-->
    <?= $this->tag->stylesheetLink('staff/style/css/style_2.css') ?>
    <?= $this->tag->stylesheetLink('staff/style/css/content.css') ?>
    <?= $this->tag->stylesheetLink('staff/style/css/style.css') ?>
</head>
<script>
    (function(doc, win){
        var docEl = doc.documentElement,
            resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
            recalc = function(){
                var clientWidth = docEl.clientWidth;
                if (!clientWidth) {
                    return;
                }else if (clientWidth >= 750) {
                    docEl.style.fontSize = '45px';
                } else {
                    // docEl.style.fontSize = 50 * (clientWidth / 1080) + 'px';
                }
            };
        if (!docEl.addEventListener) { return; }
        win.addEventListener(resizeEvt, recalc, false);
        doc.addEventListener('DOMContentLoaded', recalc, false);
    })(document, window);
</script>
<?= $this->tag->javascriptInclude('js/lib/jQuery/jquery-2.2.3.min.js') ?>
<?= $this->tag->javascriptInclude('js/lib/validate/jquery.validate.js') ?>
<?= $this->tag->javascriptInclude('js/lib/jquery.md5.js') ?>
<body style="">


    <style>
        .more img{
            height: 20px;
        }
    </style>
    <title>下属状态详情</title>
    <div class="warp_1">
        <div class="title_g">
            <a class="return center" href="<?= $this->url->get('staff/count') ?>">
                <img src="<?= $this->url->get('staff/style/img/return_03.png') ?>" />
            </a>
            <h5 class="tetle_font">下属状态详情</h5>
            <a class="Reserved"></a>
        </div>
        <div class="main">
            <div class="bulletin">
                <ul class="" style="overflow: hidden">
                    <li>
                        <span class="bulletin_time text_overflow" >姓名</span>
                        <span class="bulletin_time text_overflow search_user" data-type="section">部门 <image class='xlIcon' src='<?= $this->url->get('staff/style/img/select_triangle.png') ?>'></image></span>
                        <span class="bulletin_time text_overflow search_user" data-type="department">科室 <image class='xlIcon' src='<?= $this->url->get('staff/style/img/select_triangle.png') ?>'></image></span>
                        <span class="dropdown-menu"></span>
                        <span class="bulletin_time text_overflow search_user" data-type="status">状态
                        <image class='xlIcon' src='<?= $this->url->get('staff/style/img/select_triangle.png') ?>'></image>
                        </span>
                    </li>
                </ul>
                <ul class="list" style="overflow: hidden">
                </ul>
            </div>
        </div>
        <div class="section_option departmentDiv bright_view_hide">
            <div class="departmentDiv_con">
                <ul>
                    <li data-id="0" data-type="section" class="listBtn1">— 部门 —</li>
                    <?php foreach ($section_list as $v) { ?>
                        <li data-id="<?= $v->section_id ?>" data-type="section" class="listBtn1"><?= $v->section_name ?></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="department_option departmentDiv bright_view_hide">
            <div class="departmentDiv_con">
                <ul>
                    <li data-id="0" data-type="department" class="listBtn1">— 科室 —</li>
                    <?php foreach ($department_list as $v) { ?>
                        <li data-id="<?= $v->department_id ?>" data-type="department" class="listBtn1"><?= $v->department_name ?></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="status_option departmentDiv bright_view_hide">
            <div class="departmentDiv_con">
                <ul>
                    <li data-id="0" data-type="status" class="listBtn1">— 状态 —</li>
                    <?php foreach ($status_list as $v) { ?>
                        <li data-id="<?= $v['status_id'] ?>" data-type="status" class="listBtn1"><?= $v['status_name'] ?></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
    <?= $this->tag->stylesheetLink('js/lib/dropload/dropload.css') ?>
    <?= $this->tag->javascriptInclude('js/lib/dropload/dropload.min.js') ?>
    <script>
        var section_id = department_id = 0;
        var status_id = '<?= $status_id ?>';
        var url = "<?= $this->url->get('staff/countstatusdetailbystatus') ?>";
        var load_obj;
        var page_size = 20;
        var page = 1;
        var have_data = true;

        $(function () {
            $(".listBtn1").click(function () {
                var id = $(this).data('id');
                var type = $(this).data('type');
                if(type == 'section'){
                    section_id = id;
                }
                if(type == 'department'){
                    department_id = id;
                }

                if(type == 'status'){
                    status_id = id;
                    url = !!status_id ?  "<?= $this->url->get('staff/countstatusdetailbystatus') ?>" : "<?= $this->url->get('staff/countstatusdetail') ?>";
                }
                search_user();
            });
            $('.departmentDiv').bind('click',function(){
                $(this).removeClass('bright_view_show').addClass('bright_view_hide');
            });
            $(".search_user").click(function () {
                var type = $(this).data('type');
                $("div."+type+"_option").removeClass('bright_view_hide').addClass('bright_view_show');
            });

            // dropload
            $('.main').dropload({
                scrollArea: window,
                domUp: {
                    domClass: 'dropload-up',
                    domRefresh: '<div class="dropload-refresh">↓下拉刷新</div>',
                    domUpdate: '<div class="dropload-update">↑释放更新</div>',
                    domLoad: '<div class="dropload-load"><span class="loading"></span>加载中...</div>'
                },
                domDown: {
                    domClass: 'dropload-down',
                    domRefresh: '<div class="dropload-refresh">↑上拉加载更多</div>',
                    domLoad: '<div class="dropload-load"><span class="loading"></span>加载中...</div>',
                    domNoData: '<div class="dropload-noData">没有更多了</div>'
                },
                loadUpFn: function (me) {
                    load_obj = me;
                    page = 2;
                    have_data = true;
                    var section_id = $("select[name='section_id']").val();
                    var department_id = $("select[name='department_id']").val();
                    $.ajax({
                        type: 'POST',
                        dataType: 'JSON',
                        url: url,
                        data: {
                            "<?= $_csrfKey ?>": "<?= $_csrf ?>",
                            'type' : '<?= $type ?>',
                            section_id: section_id,
                            department_id: department_id,
                            status_id: status_id
                        },
                        success: function (data) {
                            if(data.status == 200){
                                $('.list').empty();
                                if (!data.data.data) {
                                    have_data = false;
                                    me.resetload();
                                    me.unlock();
                                    me.noData(true);
                                }else{
                                    var result = set_list(data.data.data);
                                    $('.list').html(result);
                                    me.resetload();
                                    me.unlock();
                                    me.noData(false);
                                }
                            }else{
                                layer.msg('服务器错误，请刷新后重试！', {icon: 5});
                            }
                        },
                        error: function (xhr, type) {
                            layer.msg('网络错误，请刷新后重试！', {icon: 2});
                            // 即使加载出错，也得重置
//                            me.resetload();
                        }
                    });
                },
                loadDownFn: function (me) {
                    load_obj = me;
                    var flag = true;
                    len = $('.list li').length * 1;
                    if ((len % page_size == 0) && have_data && flag) {
                        flag = false;
                        var section_id = $("select[name='section_id']").val();
                        var department_id = $("select[name='department_id']").val();
                        $.ajax({
                            type: 'POST',
                            dataType: 'JSON',
                            url: url,
                            data: {
                                "<?= $_csrfKey ?>": "<?= $_csrf ?>",
                                'type' : "<?= $type ?>",
                                page: page,
                                section_id: section_id,
                                department_id: department_id,
                                status_id: status_id
                            },
                            success: function (data) {
                                if(data.status == 200) {
                                    page++;
                                    if (!data.data.data) {
                                        have_data = false;
                                        me.noData(true);
                                        me.resetload();
                                        me.unlock();
                                        return false;
                                    }else{
                                        var result = set_list(data.data.data);
                                        $('.list').append(result);
                                        // 每次数据加载完，必须重置
                                        me.resetload();
                                    }
                                }else{
                                    layer.msg('服务器错误，请刷新后重试！', {icon: 5});
                                }
                            },
                            error: function (xhr, type) {
                                layer.msg('网络错误，请刷新后重试！', {icon: 2});
                                me.lock();
                                // 即使加载出错，也得重置
//                                me.resetload();
                            }
                        });
                    } else {
                        me.unlock();
                        me.noData(true);
                        me.resetload();
                    }
                },
                threshold: 50
            });
        });

        $(function () {
            $('.show_detail').click(function () {
                var status_id = $(this).parent('li').data('id');
                //ajax
                $.ajax({
                    type: 'POST',
                    dataType: 'JSON',
                    url: '<?= $this->url->get('staff/countdetail') ?>',
                    data: {
                        'type': type,
                        'status_id': status_id,
                    },
                    success: function (data) {

                    },
                    error: function (data) {
                        layer.msg('加载失败，请重试！', {icon: 2});
                    }
                })
            })
        });

        function set_list(data){
            var result = '';
            $.each(data, function (k, v) {
                var link = "location.href='<?= $this->url->get('staff/userstatuslist?user_id=') ?>"+v.user_id+"'" ;
                result += ' <li data-id="">'+
                    ' <span class="bulletin_font">'+v.user_name+'</span>'+
                    '<span class="bulletin_font text_overflow">'+(v.section_name ?v.section_name : '--')+'</span>'+
                    '<span class="bulletin_font text_overflow">'+(v.department_name ?v.department_name : '--')+'</span>'+
                    '<span class="bulletin_font text_overflow" onclick="'+link+'">'+v.status_name+'' +
                    '<span class="more" ><img src="<?= $this->url->get('staff/style/img/zl.png') ?>" alt=""></span>' +
                    '</span>'+
                    ' </li>';
            });
            return result;
        }
        function search_user() {
            page = 2;
            have_data = true;
            $("div.departmentDiv").removeClass('bright_view_show').addClass('bright_view_hide');
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: url,
                data: {
                    "<?= $_csrfKey ?>": "<?= $_csrf ?>",
                    'type': '<?= $type ?>',
                    section_id: section_id,
                    department_id: department_id,
                    status_id: status_id
                },
                success: function (data) {
                    if(data.status == 200) {
                        $('.list').empty();
                        if (!data.data.data) {
                            have_data = false;
                            load_obj.unlock();
                            load_obj.noData(true);
                            load_obj.resetload();
                            return false;
                        }else{
                            var result = set_list(data.data.data);
                            $('.list').html(result);
                            load_obj.resetload();
                        }
                    }else{
                        layer.msg('服务器错误，请刷新后重试！', {icon: 5});
                    }
                },
                error: function (xhr, type) {
                    layer.msg('网络错误，请刷新后重试！', {icon: 2});
                    // 即使加载出错，也得重置
                }
            })
        }
    </script>


<?= $this->tag->javascriptInclude('org/layer/layer.js') ?>
<?= $this->tag->javascriptInclude('staff/style/js/custom.js') ?>
</body>
</html>