<?php if ($_session['user_is_super'] || ($_session['user_is_admin'] && $_session['project_id'] == '')) { ?>
<th width="80" class="project_type">单位:</th>
<td class="project_type">
    <select onchange="get_options_by_project(this,'<?= $type ?>')" name="project_id" class="multiselect project_list">
        <option value="">全部</option>
        <?php if (isset($data['project_list'])) { ?>
            <?php foreach ($data['project_list'] as $v) { ?>
                <option value="<?= $v->project_id ?>" <?= (isset($input['project_id']) && ($input['project_id'] == $v->project_id) ? 'selected' : '') ?>><?= $v->project_name ?></option>
            <?php } ?>
        <?php } ?>
    </select>
</td>
<?php } else { ?>
<input type="hidden" name="project_id" value="<?= $_session['project_id'] ?>">
<?php } ?>

<?php if ($type >= 1) { ?>
<th width="80">部门:</th>
<td>
    <select onchange="" class="section_list multiselect" name="section_id"  >
        <option value="">请选择</option>
        <?php if (isset($data['section_list'])) { ?>
            <?php foreach ($data['section_list'] as $v) { ?>
                <option value="<?= $v->section_id ?>" <?= (isset($input['section_id']) && ($input['section_id'] == $v->section_id) ? 'selected' : '') ?>><?= $v->section_name ?></option>
            <?php } ?>
        <?php } ?>
    </select>
</td>
<?php } ?>

<?php if ($type >= 0) { ?>
<th width="80">科室:</th>
<td>
    <select onchange="" class="department_list multiselect" name="department_id">
        <option value="">请选择</option>
        <?php if (isset($data['department_list'])) { ?>
            <?php foreach ($data['department_list'] as $v) { ?>
                <option value="<?= $v->department_id ?>" <?= (isset($input['department_id']) && $input['department_id'] == $v->department_id ? 'selected' : '') ?>><?= $v->department_name ?></option>
            <?php } ?>
        <?php } ?>
    </select>
</td>
<?php } ?>

<script>
    $('.multiselect').multiselect({enableFiltering: true,maxHeight: 350});
</script>