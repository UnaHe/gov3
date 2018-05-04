{% extends "layout/header.volt" %}

{% block content %}

    <style>
        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
        }

    </style>
    <title>公告详情</title>
    <div class="wrap">
        <div class="title_g">
            <a class="return center" href="{{ url('notice/index?pid=' ~ project_id ~ '&did=' ~ department_id) }}">
                <img src="{{ url('home/style/img/return_03.png') }}" />
            </a>
            <h5 class="tetle_font">公告详情</h5>
            <a class="Reserved"></a>
        </div>
        <div class="main">

            <div class="Noticedetails">
                <h5 class="Noticedetails_title">{{ data.notice_title }}</h5>
                <p class="Noticedetails_time">{{ date("Y/m/d", data.created_at) }}</p>
                <div class="Noticedetails_con">
                    <p>{!! $data->notice_content !!}</p>
                    {#推进工业产品生产许可证制度改革现场交流会日前在江苏省宿迁市召开。中共中央政治局常委、国务院总理李克强作出重要批示。批示指出：制造业是实体经济的重要基础。近年来随着中国制造发展和消费升级，工业产品种类更新加快，但企业市场准入制度性交易成本较高的问题凸显，与此同时，打击假冒伪劣产品、规范市场秩序的任务也十分迫切。要认真贯彻党中央、国务院决策部署，深入推进供给侧结构性改革，持续推动政府职能转变，把改革工业产品生产许可证制度作为深化简政放权、放管结合、优化服务改革的重要内容，适应市场需求，简化工业产品市场准入前置审批，促进新技术、新工艺发展，使产品更为丰富、品质不断提升、市场更加繁荣。同时，坚持放管结合，将许可管理更多聚焦在安全风险高的产品上，政府集中更大力量加强事中事后监管，让假冒伪劣产品无藏身之地，进一步营造公平公正的市场环境，为促进中国制造品质升级、迈向中高端作出更大贡献。#}
                </div>
            </div>
        </div>
    </div>

{% endblock %}