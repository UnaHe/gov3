
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!--其他页-->
    <link rel="stylesheet" href="{{asset('staff/style/css/style_2.css')}}">
    <link rel="stylesheet" href="{{asset('staff/style/css/content.css')}}">
    <link rel="stylesheet" href="{{asset('staff/style/css/style.css')}}">
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
                    console.log(12);
                    docEl.style.fontSize = '45px';
                } else {
                    console.log(23);
                    // docEl.style.fontSize = 50 * (clientWidth / 1080) + 'px';
                }
            };
        if (!docEl.addEventListener) { return; }
        win.addEventListener(resizeEvt, recalc, false);
        doc.addEventListener('DOMContentLoaded', recalc, false);
    })(document, window);
</script>
<script src="{{asset('js/lib/jQuery/jquery-2.2.3.min.js')}}"></script>

<script src="{{asset('js/lib/validate/jquery.validate.js')}}"></script>
<script src="{{asset('js/lib/jquery.md5.js')}}"></script>
<body style="">
@yield("content")
@include("staff.pc.footer")
