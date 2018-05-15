<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    {{ stylesheet_link('staff/style/css/style_2.css') }}
    {{ stylesheet_link('staff/style/css/style.css') }}
    {#<link rel="stylesheet" href="{{asset('staff/style/css/content.css')}}"> //pc端的样式#}
</head>
{{ javascript_include('js/lib/jQuery/jquery-2.2.3.min.js') }}
{{ javascript_include('js/lib/validate/jquery.validate.js') }}
{{ javascript_include('js/lib/jquery.md5.js') }}
<script>
    (function(doc, win){
        var docEl = doc.documentElement,
            resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
            recalc = function(){
                var clientWidth = docEl.clientWidth;
                if (!clientWidth) {
                    return;
                }else if (clientWidth >= 1080) {
                    docEl.style.fontSize = '100px';
                } else {
                    docEl.style.fontSize = 100 * (clientWidth / 1080) + 'px';
                }
            };
        if (!docEl.addEventListener) { return; }
        win.addEventListener(resizeEvt, recalc, false);
        doc.addEventListener('DOMContentLoaded', recalc, false);
    })(document, window);

</script>
<body style="background-color: #f4f4f4">
{% block content %}

{% endblock %}
{{ javascript_include('org/layer/layer.js') }}
{#<script src="{{asset('staff/style/js/custom.js')}}"></script> //pc端的样式#}
</body>
</html>