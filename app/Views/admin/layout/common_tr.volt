{% if _session['user_is_super'] or (_session['user_is_admin'] and _session['project_id'] == '') %}
<tr class="project_type">
    <th width="150" class="project_type"><i class="require">*</i>单位:</th>
    <td class="project_type">
        <select onchange="get_options_by_project(this,'{{ type }}')" name="project_id" id="project_id" class="multiselect project_list project_id" required>
            <option value="0">请选择单位</option>
            {% for v in project %}
            <option value="{{ v.project_id }}" {{ project_id is defined and project_id == v.project_id ? 'selected' : '' }}>{{ v.project_name }}</option>
            {% endfor %}
        </select>
        <span class="error" for="project_id"></span>
    </td>
</tr>
{% else %}
<input type="hidden" name="project_id" value="{{ _session['project_id'] }}">
{% endif %}

{% if type >= 0 and type % 2 == 0 %}
<tr class="project_type">
    <th width="150"><i class="require">*</i>{{ cate_list_name is defined ? cate_list_name : ' 科室:' }}</th>
    <td>
        <select onchange="" class="department_list multiselect department_id" id="{{ cate_name is defined ? cate_name : 'department_id' }}" name="{{ cate_name is defined ? cate_name : 'department_id' }}">
            <option value="0">请选择科室</option>
            {% if department_list is defined %}
            {% for v in department_list %}
            <option value="{{ v.department_id }}" {{ department_id is defined and department_id == v.department_id ? 'selected' : '' }}>{{ v.department_name }}</option>
            {% endfor %}
            {% endif %}
        </select>
        <span class="error" for="{{ cate_name is defined ? cate_name : 'department_id' }}"></span>
    </td>
</tr>
{% endif %}

{% if type >= 2 or (type > 0 and type % 2) %}
<tr class="project_type">
    <th width="150">{{ section_list_name is defined ? section_list_name : '部门:' }}</th>
    <td>
        <select onchange="" class="section_list multiselect" id="section_id" name="{{ section_name is defined ? section_name : 'section_id' }}">
            <option value="0">请选择部门</option>
            {% if section_list is defined %}
            {% for v in section_list %}
            <option value="{{ v.section_id }}" {{ section_id is defined and section_id == v.section_id ? 'selected' : '' }}>{{ v.section_name }}</option>
            {% endfor %}
            {% endif %}
        </select>
    </td>
</tr>
{% endif %}
<script>
    $('.multiselect').multiselect(multiselect_option);
</script>