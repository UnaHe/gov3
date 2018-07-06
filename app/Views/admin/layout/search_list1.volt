{% if _session['user_is_super'] or (_session['user_is_admin'] and _session['project_id'] == '') %}
<th width="80" class="project_type">单位:</th>
<td class="project_type">
    <select onchange="get_options_by_project(this,'{{ type }}')" name="project_id" class="multiselect project_list">
        <option value="">全部</option>
        {% if data['project_list'] is defined %}
            {% for v in data['project_list'] %}
                <option value="{{ v.project_id }}" {{ input['project_id'] is defined and (input['project_id'] == v.project_id) ? 'selected' : null }}>{{ v.project_name }}</option>
            {% endfor %}
        {% endif %}
    </select>
</td>
{% else %}
<input type="hidden" name="project_id" value="{{ _session['project_id'] }}">
{% endif %}

{% if type >= 1 %}
<th width="80">部门:</th>
<td>
    <select onchange="" class="section_list multiselect" name="section_id"  >
        <option value="">请选择</option>
        {% if data['section_list'] is defined %}
            {% for v in data['section_list'] %}
                <option value="{{ v.section_id }}" {{ input['section_id'] is defined and (input['section_id'] == v.section_id) ? 'selected' : null }}>{{ v.section_name }}</option>
            {% endfor %}
        {% endif  %}
    </select>
</td>
{% endif  %}

{% if type >= 0 %}
<th width="80">科室:</th>
<td>
    <select onchange="" class="department_list multiselect" name="department_id">
        <option value="">请选择</option>
        {% if data['department_list'] is defined %}
            {% for v in data['department_list'] %}
                <option value="{{ v.department_id }}" {{ input['department_id'] is defined and input['department_id'] == v.department_id ? 'selected' : null}}>{{ v.department_name }}</option>
            {% endfor %}
        {% endif  %}
    </select>
</td>
{% endif  %}

<script>
    $('.multiselect').multiselect({enableFiltering: true,maxHeight: 350});
</script>