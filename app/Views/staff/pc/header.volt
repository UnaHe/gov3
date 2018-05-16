<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!--其他页-->
    {{ stylesheet_link('staff/style/css/style_2.css') }}
    {{ stylesheet_link('staff/style/css/content.css') }}
    {{ stylesheet_link('staff/style/css/style.css') }}
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
{{ javascript_include('js/lib/jQuery/jquery-2.2.3.min.js') }}
{{ javascript_include('js/lib/validate/jquery.validate.js') }}
{{ javascript_include('js/lib/jquery.md5.js') }}
<body style="">
{% block content %}

{% endblock %}
{{ javascript_include('org/layer/layer.js') }}
{{ javascript_include('staff/style/js/custom.js') }}
</body>
</html>