<?php

echo $this->Html->css(array(
    'Business.business'), array('block' => 'css', 'minify'=>false));
?>

<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'mooBusiness', 'business_jqueryui'), 
    'object' => array('$', 'mooBusiness')
));?>
mooBusiness.initBusinessPermission(<?php echo $business_id;?>);
<?php $this->Html->scriptEnd(); ?>
<div class="permission-manager">
    <p class="manage-text"><?php echo __d('business', 'You can manage permissions that admins can do here');?></p>

    <form id="adminForm">

    <?php 
        $select_permission = !empty($business['Business']['permissions']) ? explode(",", $business['Business']['permissions']) : array();
        echo $this->Form->hidden('business_id', array(
            'value' => $business_id
        ));
    ?>
        <ul>
        <?php foreach($permissions as $key => $permission):?>
            <li>
                <?php echo $this->Form->checkbox('permissions.', array(
                    'value' => $key,
                    'hiddenField' => false,
                    'id' => $key,
                    'checked' => in_array($key, $select_permission) ? 'checked' : ''
                ));?>
                <label for="<?php echo $key;?>"><?php echo $permission;?></label>
            </li>
        <?php endforeach;?>
        </ul>
        <a id="saveButton" class="button" href="javascript:void(0)">
        <?php echo __d('business', 'Save');?>                                
        </a>
        <div style="display: none; margin-top: 10px" class="error-message" id="adminMessage"></div>
    </form>
</div>    
<ul id="admin_content" class="list6 comment_wrapper comment_list"></ul>