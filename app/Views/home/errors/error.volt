{% extends "layout/header.volt" %}

{% block content %}

    <div class="wrap">
        <div class="im_index">
            <h5 class="im_index_title">{{ msg }}</h5>
        </div>
    </div>
    <script>
        $(".im_index").height($(window).height()+"px");
    </script>

{% endblock %}