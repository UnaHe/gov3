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


    <title>下属留言处理详情</title>
    <div class="warp_1">
        <div class="title_g">
            <a class="return center" href="<?= $this->url->get('staff/count') ?>">
                <img src="<?= $this->url->get('staff/style/img/return_03.png') ?>" />
            </a>
            <h5 class="tetle_font">下属留言处理详情</h5>
            <a class="Reserved"></a>
        </div>
        <div class="main">
            <div class="bulletin">
                <ul class="" style="overflow: hidden">
                    <li>
                        <span class="bulletin_time text_overflow" style="font-size: 0.5rem;">用户</span>
                        <span class="bulletin_time text_overflow" style="font-size: 0.5rem;">办公室</span>
                        <span class="bulletin_time text_overflow" style="font-size: 0.5rem;">时间</span>
                        <span class="bulletin_time text_overflow" style="font-size: 0.5rem;">内容</span>
                    </li>
                </ul>
                <ul class="list" style="overflow: hidden">
                    
                        
                            
                            
                            
                            
                                        
                                        
                        
                    
                </ul>
            </div>
        </div>
    </div>
    <?= $this->tag->stylesheetLink('js/lib/dropload/dropload.css') ?>
    <?= $this->tag->javascriptInclude('js/lib/dropload/dropload.min.js') ?>
    <script>
        $(function () {
            var counter = 0;
            var page_size = 20;
            var page = 1;
            var have_data = true;

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
                    page = 2;
                    have_data = true;
                    $.ajax({
                        type: 'POST',
                        dataType: 'JSON',
                        url: "<?= $this->url->get('staff/countcommentdetail') ?>",
                        data: {
                            "<?= $_csrfKey ?>": "<?= $_csrf ?>",
                            'type' : '<?= $type ?>',
                            'status_id' : '<?= $status_id ?>'
                        },
                        success: function (data) {
                            if(data.status == 200){
                                $('.list').empty();
                                if (!data.data.data.items) {
                                    have_data = false;
                                    me.resetload();
                                    me.unlock();
                                    me.noData(true);
                                }else{
                                    var result = set_list(data.data.data.items);
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
                    var flag = true;
                    len = $('.list li').length * 1;
                    if ((len % page_size == 0) && have_data && flag) {
                        flag = false;
                        $.ajax({
                            type: 'POST',
                            dataType: 'JSON',
                            url: "<?= $this->url->get('staff/countcommentdetail') ?>",
                            data: {
                                "<?= $_csrfKey ?>": "<?= $_csrf ?>",
                                'type' : '<?= $type ?>',
                                'status_id' : '<?= $status_id ?>'
                            },
                            success: function (data) {
                                if(data.status == 200) {
                                    page++;
                                    if (!data.data.data.items) {
                                        have_data = false;
                                        me.noData(true);
                                        me.resetload();
                                        me.unlock();
                                        return false;
                                    }else{
                                        var result = set_list(data.data.data.items);
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

        function set_list(data) {
            console.log(data);
            var result = '';
            $.each(data, function (k, v) {
                var created_time = '<?= date('Y/m/d') ?>';
                var content = v.comments.comment_content;
                var link = "<?= $this->url->get('staff/commentone?comment_id=') ?>"+v.comment_id;
                result += ' <li data-id="">'+
                    ' <span class="bulletin_font">'+v.user_name+'</span>'+
                    '<span class="bulletin_font text_overflow">'+v.department_name+'</span>'+
                    '<span class="bulletin_font text_overflow">'+created_time+'</span>'+
                    '<span class="bulletin_font text_overflow "><a class="comment_content"  href="'+link+'">'+content+'</a></span>'+
                    ' </li>';
            });
            return result;
        }
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
        })
    </script>


<?= $this->tag->javascriptInclude('org/layer/layer.js') ?>
<?= $this->tag->javascriptInclude('staff/style/js/custom.js') ?>
</body>
</html>