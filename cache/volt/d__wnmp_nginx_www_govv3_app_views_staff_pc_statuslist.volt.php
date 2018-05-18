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


    <style type="text/css">
        #wrapper {
            width: 100%;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            overflow: auto;
        }
        #scroller {
            position: absolute;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
            width: 100%;
            padding: 0;
        }

        /*下拉加载*/
        #pullDown {
            margin-top: 0;
            width: 100%;
            text-align: center;
        }

        .pullDownLabel {
            margin: 20px auto;
            text-align: center;
            font-size: 14px;
            /*color: #37bbf5;*/
        }

        .my_count {
            width: 1.45rem;
            height: 0.64rem;
            /*position: absolute;*/
            top: 0.22rem;
            right: 0.2rem;
            border-radius: 2px;
            overflow: hidden;
        }
    </style>
    <title>详情</title>
    <div class="warp_1">
        <div class="title_g">
            <a class="return center" href="#" onclick="history.go(-1)">
                <img src="<?= $this->url->get('staff/style/img/return_03.png') ?>" />
            </a>
            <h5 class="tetle_font">详情</h5>
            <a class="Reserved"></a>
        </div>
        <div class="wrap" id="wrap">
            <div id="pullDown" class="loading">
                <label class="pullDownLabel flip"></label>
            </div>
            <div class="main">
                <div class="personnel_info clear">
                    <div class="personnel_info_img"><img src="<?= $data['user_image'] ?>"></div>
                    <div class="personnel_info_font y_personnel_info_font">
                        <span class="PersonnelName y_PersonnelName"><?= $data['user_name'] ?></span>
                        <span class="PersonnelDuties"><?= (isset($data['user_job']) ? $data['user_job'] : '科员') ?></span>
                        <span class="PersonnelRole text_overflow"><?= (isset($data['user_intro']) ? $data['user_intro'] : '无个人简介') ?></span>
                    </div>
                </div>
                <div class="CurrentState y_CurrentState">
                    <div class="CurrentState_title y_CurrentState_title">
                        <span class="ghost"></span>
                        <span class="CurrentState_title_font">当前状态</span>
                    </div>
                    <div class="y_time clear">
                        <div class="y_time_left center">
                        <span class="y_xiuxi no_status"
                              style="background:<?= $data['nowstatus']['status_color'] ?>"><?= $data['nowstatus']['status_name'] ?></span>
                        </div>
                        <div class="y_time_con">
                            <p class="beginning"><a>始</a>&nbsp;&nbsp;<a><?= date('Y/m/d H:i', $data['nowstatus']['start_time']) ?></a></p>
                            <p class="expiry"><a>终</a>&nbsp;&nbsp;<a><?= date('Y/m/d H:i', $data['nowstatus']['end_time']) ?></a></p>
                        </div>
                        
                    </div>
                </div>
                <div class="CurrentState y_CurrentState">
                    <div class="CurrentState_title y_CurrentState_title">
                        <span class="ghost"></span>
                        <span class="CurrentState_title_font">计划列表</span>
                        
                                    
                    </div>
                    <?php if (!empty($data['statuslist'])) { ?>
                        <?php foreach ($data['statuslist'] as $v) { ?>
                            <div class="y_time clear">
                                <div class="y_time_left center edit_status" data-status="<?= $v['user_status_id'] ?>">
                                <span class="y_xiuxi no_status"
                                      style="background:<?= $v['status_color'] ?>"><?= $v['status_name'] ?></span>
                                </div>
                                <div class="y_time_con edit_status" data-status="<?= $v['user_status_id'] ?>">
                                    <p class="beginning"><a>始</a>&nbsp;&nbsp;<a><?= date('Y/m/d H:i', $v['start_time']) ?></a></p>
                                    <p class="expiry"><a>终</a>&nbsp;&nbsp;<a><?= date('Y/m/d H:i', $v['end_time']) ?></a></p>
                                </div>
                                
                                     
                                    
                                            
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function () {
//            $(".edit_status").click(function () {
//                var user_status_id = $(this).data('status');
//                edit_user_status(user_status_id);
//            })

        });
        
        
            
        #}
        //删除
        
            
                
            , function () 
                
                    
                    
                    
                    
                        
                    ,#}
                    
                        
                        
                    , success: function (data) 
                        
                            
                            
                                
                            , 3000);#}
                         else 
                            
                        #}
                    , error: function () 
                        
                    #}
                )#}
            );#}
        #}
    </script>
    <?= $this->tag->javascriptInclude('js/lib/iScroll/iscroll.js') ?>
    <script>
        function loaded() {
//            var myScroll = new iScroll("wrapper");
            pullDownEl = document.getElementById('pullDown');
            pullDownOffset = pullDownEl.offsetHeight;
            console.log(pullDownEl.querySelector('.pullDownLabel'));
            myScroll = new iScroll('wrapper', {
                scrollbarClass: 'myScrollbar',
                useTransition: false,
                topOffset: pullDownOffset,
                onRefresh: function () {
//                    if (pullDownEl.className.match('loading')) {
//                        pullDownEl.className = '';
//                        pullDownEl.querySelector('.pullDownLabel').innerHTML = '下拉刷新...';
//                    }
                },
                onScrollMove: function () {
                    if (this.y > 5 && !pullDownEl.className.match('flip')) {
                        pullDownEl.className = 'flip';
                        pullDownEl.querySelector('.pullDownLabel').innerHTML = '松手开始更新...';
                        this.minScrollY = 0;
                    } else if (this.y < 5 && pullDownEl.className.match('flip')) {
                        pullDownEl.className = '';
                        pullDownEl.querySelector('.pullDownLabel').innerHTML = '下拉刷新...';
                        this.minScrollY = -pullDownOffset;
                    }
                },
                onScrollEnd: function () {
                    if (pullDownEl.className.match('flip')) {
                        pullDownEl.className = 'loading';
                        pullDownEl.querySelector('.pullDownLabel').innerHTML = '加载中...';
                        pullDownAction();   // ajax call
                    }
                }
            });

            setTimeout(function () {
                document.getElementById('wrapper').style.left = '0';
            }, 800);

        }
        document.addEventListener('DOMContentLoaded', loaded, false);

        var myScroll,
            pullDownEl, pullDownOffset;
        /**
         * 下拉刷新 （自定义实现此方法）
         * myScroll.refresh(); 数据加载完成后，调用界面更新方法
         */
        function pullDownAction() {
            setTimeout(function () {
                var new_time = ((new Date()).getTime()), myhref = location.href;
                if (myhref.indexOf("&time") !== -1) {
                    myhref = myhref.substr(0, myhref.indexOf("&time"));
                }
                window.location.href = myhref + '&time=' + new_time;
            }, 600);
        }

    </script>


<?= $this->tag->javascriptInclude('org/layer/layer.js') ?>
<?= $this->tag->javascriptInclude('staff/style/js/custom.js') ?>
</body>
</html>