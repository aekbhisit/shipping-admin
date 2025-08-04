<?php
     $all_check  =  (!empty($checked['all'])&&$checked['all']=='all')?"checked":'';
?>
 <div class="form-group form-check">
    <label class="form-label">{{ __('user_admin.permission') }}</label>
    <input class="form-check-input"
    name="all" type="checkbox" id="CheckAllPermission"
    value="all" <?=$all_check?> onclick="setCheckAllPermission(this);">Check All
 </div>
<hr>
<ul id="show_roles" class="treeview">
    <?php foreach($permissions as $module => $pg){
        $all_module_check   =  (!empty($checked['module'])&&isset($checked['module'][$module])&&$checked['module'][$module]['all']=='all')?"checked":'';
    ?>
    <li style="border:1px solid #ccc; padding:5px; margin-bottom:5px;">
        <a href="#">
            <div class="form-check form-check-inline">
            <input class="form-check-input module_<?=$module ?>"
                name="module[<?= $module ?>][all]" type="checkbox" id="<?=$module ?>_all"
                value="all" onclick="setCheckAllModulePermission(this,'<?= $module ?>');"
                <?=$all_module_check ?>>
            </div>
            <strong><?=$module?></strong>
        </a>
        <ul style="margin-left: 20px;">
            <?php foreach($pg as $group => $permissions){ 
				$all_group_check = (!empty($checked['group'])&&isset($checked['group'][$module][$group])&&$checked['group'][$module][$group]['all']=='all')?"checked":'';	
            ?>
            <li style="border-bottom: 1px dotted #ccc ; margin-bottom:5px;">
                <div class="row">
                    <div class="col-md-1">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input module_<?=$module ?> module_<?=$module ?>_<?= $group ?>"
                                name="group[<?=$module ?>][<?= $group ?>][all]" type="checkbox" id="<?= $module ?>_<?= $group ?>_all"
                                value="all" onclick="setCheckAllModuleGroupPermission(this,'<?= $module ?>,<?= $group ?>');"
                            <?=$all_group_check?>>
                        </div>
                        <strong><?=$group?></strong>
                    </div>
                    <div class="col-md-11">
                        <?php foreach($permissions as $pk => $permission){
                            $module_permission_checked =  (!empty($checked['permissions'])&&in_array($permission['id'],$checked['permissions']))?"checked":'';	
                        ?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input module_<?=$module ?> module_<?= $module ?>_<?= $group ?>"
                                name="permissions[]" type="checkbox"
                                id="<?= $module ?>_<?= $group ?>_<?= $permission['id']?>" value="<?= $permission['id'] ?>"
                                <?= $module_permission_checked ?> >
                            <label class="form-check-label" for="<?= $module ?>_<?= $group ?>"><?=$permission['name']?></label>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                {{-- .row --}}
            </li>
            <?php } ?>

        </ul>
    </li>
    <?php } ?>
</ul>
