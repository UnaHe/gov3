<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html" charset="utf-8">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta http-equiv="Cache-Control" content="no-cache">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    {{ stylesheet_link('home/style/css/style.css') }}
    {{ stylesheet_link('js/lib/dropload/dropload.css') }}
</head>

{#<script src="{{asset('js/lib/jQuery/jquery-3.2.1.min.js')}}"></script>#}
{{ javascript_include('js/lib/jQuery/jquery-2.2.3.min.js') }}
{#<script src="{{asset('js/lib/validate/jquery.validate.js')}}"></script>#}
{{ javascript_include('js/lib/dropload/dropload.min.js') }}
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

<body style="background:#f4f4f4">
{% block content %}

{% endblock %}
</body>

{#<script type="text/javascript" src="{{asset('admin/style/js/ch-ui.admin.js')}}"></script>#}
{{ javascript_include('org/layer/layer.js') }}
{#<script>#}
{#myScroll1 = new iScroll('wrapper1', { checkDOMChanges: true });#}
{#</script>#}

</html>
