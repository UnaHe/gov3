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


    <title>用户登录</title>
    <div class="warp" style="margin-top: 0rem;">
        <p class="lf_title">工作状态管理系统</p>
        <form id="login_form" name="login_form" method="POST" action="<?= $this->url->get('staff/login') ?>">
            <div class="phone">
                <input type="hidden" name="<?= $_csrfKey ?>" value="<?= $_csrf ?>"/>
                <input type="text" id="number" name="user_phone" placeholder="输入手机号" >
                <input type="password" id="password" name="password" placeholder="输入密码" >
                <input type="hidden"  name="user_pass">
                <input type="hidden"  name="tpl" value="<?= (isset($tpl) ? $tpl : 'p') ?>">
            </div>
            <div class="click_pwd">
                <span class="ResidentialClosely">
                    <span class="yes">
                        
                    </span>
                </span>
                <span class="jizhu" name="is_remember">记住密码</span>
                
                <input type="hidden"  name="remember" value="">
            </div>
            <input  type="submit"  class="submit" value="登录" id="login">
        </form>
        <p class="wangji">忘记密码请联系管理员</p>
    </div>
    <script>
        $(function(){
            $("#login_form").validate({
                submitHandler: submitHandler,
                errorElement: "span",
                rules: {
                    user_phone: {
                        required: true,
                        isMobile: true
                    },
                    password: {
                        required: true,
                        minlength: 6
                    }
                },
                messages: {
                    user_phone: {
                        required: "请输入你的手机号",
                        isMobile: "请填写正确的手机号",
                    },
                    password: {
                        required: "请输入密码",
                        minlength: "密码长度不能小于 6 个字符"
                    }
                }
            });

            $('.ResidentialClosely').click(function(){
                if($(this).find('i.gou').length>0){
                    $("input[name='remember']").val('');
                    $('span.yes').empty();
                }else{
                    $("input[name='remember']").val(true);
                    $('span.yes').append('<i class="gou"></i>');
                }
            });
        });

        function submitHandler(){
            $("input[name='user_pass']").val($.md5($("input[name='password']").val()));
            $.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: '<?= $this->url->get('staff/login') ?>',
                beforeSubmit: function(){
                    layer('提交中...');
                },
                data:{
                    "<?= $_csrfKey ?>": "<?= $_csrf ?>",
                    'user_phone': $("input[name='user_phone']").val(),
                    'user_pass': $("input[name='user_pass']").val(),
                    'remember': $("input[name='remember']").val(),
                    'tpl': '<?= $tpl ?>',
                },success: function(data){
                    if (data.status == 200) {
                        layer.msg(data.msg, {
                            icon: 6,
                            time: 2000, //2s后自动关闭
                        },function (){
                            location.href = '<?= $this->url->get('staff/refresh') ?>';
                        });
                    } else {
                        layer.msg(data.msg, {icon: 5});
                    }
                },error: function(){
                    layer.msg('系统错误，请刷新后重试！', {icon: 2});
                }
            })
        }
    </script>


<?= $this->tag->javascriptInclude('org/layer/layer.js') ?>
<?= $this->tag->javascriptInclude('staff/style/js/custom.js') ?>
</body>
</html>