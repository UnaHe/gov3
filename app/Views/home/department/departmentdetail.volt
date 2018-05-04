{% extends "layout/header.volt" %}

{% block content %}

    <style>
        pre {
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .Noticedetails_con p{
            position: relative;
           /*left: -2em;*/
        }
    </style>
    <title>科室详情</title>
    {#<div class="outerScroller" id="outerScroller">#}
        {#<div id="slideDown">#}
            {#<div id="slideDown1">#}
                {#<p>松开刷新</p>#}
            {#</div>#}
            {#<div id="slideDown2">#}
                {#<p>正在刷新 ...</p>#}
            {#</div>#}
        {#</div>#}
        {#<div class="serech_list_title">部门介绍</div>#}
        {#<div class="serech_list">#}
            {#<div class="serech_list_con">#}
                {#{!! $department_info['department_desc']!!}#}
            {#</div>#}
        {#</div>#}
    {#</div>#}
    <div class="wrap">
        <div class="title_g">
            <a class="return center" href="{{ url('/status/workerStatusList?pid=' ~ project_id ~ '&did=' ~ department_id) }}">
                <img src="{{ url('home/style/img/return_03.png') }}" />
            </a>
            <h5 class="tetle_font">科室介绍（{{ department_info['department_name'] }}）</h5>
            <a class="Reserved"></a>
        </div>
        <div class="Noticedetails_con" style="margin-top: 1.28rem;padding: 10px">
            {{ department_info is defined and department_info['department_desc'] is not empty ? department_info['department_desc'] : '' }}
            {#<div class="DepartmentIntroduction">#}
                {#<img src="img/Departmentintroduction_02.jpg">#}
                {#<div class="DepartmentIntroduction_con">#}
                    {#<span class="DepartmentName">成都市民政局</span>#}
                    {#<span class="Responsibilities">成都市民政局是负责本市有关社会行政事务管理、社会保障救济、社会福利服务、基层政权建设以及双拥工作的市政府组成部门。</span>#}
                {#</div>#}
                {#<div class="DepartmentIntroduction_font">#}
                    {#<h5>主要职责</h5>#}
                    {#<span class="im_font">#}
                        {#（一）贯彻落实国家关于民政事业方面的法律、法规、规章和政策，起草本市相关地方性法规草案、政府规章草案，并组织实施；拟订民政事业中长期发展规划和政策，并监督实施。<br><br>#}
                        {#（二）承担依法对本市社会团体、民办非企业单位、基金会进行登记和监督管理责任；指导区县社会团体、民办非企业单位的登记管理工作。<br><br>#}
                        {#（三）拟订本市社会福利事业发展规划、政策和标准；拟订社会福利机构管理办法和福利彩票销售管理办法；指导福利彩票销售及彩票公益金的使用管理；组织拟订促进慈善事业发展的政策，组织、指导社会捐助工作；指导老年人、孤儿和残疾人等特殊困难群体的权益保障工作；负责建设征地超转人员管理工作。<br><br>#}
                        {#（四）参与拟订本市社区建设总体规划；拟订社区建设的相关配套政策并组织实施；指导社区服务站建设。<br><br>#}
                        {#（五）拟订本市城乡基层群众自治建设具体办法，并组织实施；提出加强和改进城乡基层政权建设的建议，推动基层民主政治建设。<br><br>#}
                        {#（六）组织拟订本市城乡社会救助规划、政策和标准；健全城乡社会救助体系，负责城乡居民最低生活保障、医疗救助、临时救助、城市生活无着的流浪乞讨人员救助工作；负责农村五保供养工作。<br><br>#}
                        {#（七）拟订本市抚恤优待政策、标准和办法，组织和指导拥军优属、抚恤优待和烈士褒扬工作。<br><br>#}
                        {#（八）拟订本市对见义勇为人员的奖励和保护政策，负责组织实施见义勇为人员的奖励和保护工作。<br><br>#}
                        {#（九）拟订本市退役士兵、复员干部、军队离退休干部和军队无军籍退休退职职工安置政策；指导军供工作和移交地方管理的军队离退休干部休养所工作。#}
                    {#</span>#}
                {#</div>#}
            {#</div>#}
        </div>
    </div>

{% endblock %}