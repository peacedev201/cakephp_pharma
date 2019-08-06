<style>
    #permission_list label {
        width: 100px;
    }
    
</style>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
$(document).ready(function(){
    $('#everyone').click(function(){
        if ( $('#everyone').is(':checked') )
        {
 
            $('#permission_list li').hide();
            $('#permission_list li:first').show();
        }
        else
            $('#permission_list li').show();
    });
});
<?php $this->Html->scriptEnd(); ?>
<ul  id="permission_list">
	<li><label>Everyone</label>
        <?php echo $this->Form->checkbox('everyone', array('hiddenField' => false,'checked' => ( $permission === '' ) ) ); ?>
    </li>
    <?php 
    foreach ( $roles as $role ): 
    ?>
    <li style="<?php if ( $permission === '' ) echo 'display:none'; ?>"><label><?php echo $role['Role']['name']?></label>
        <input type="checkbox" name="permissions[]" value="<?php echo $role['Role']['id']?>" <?php if ( in_array($role['Role']['id'], explode(',', $permission))) echo 'checked';?>>
    </li>
    <?php 
    endforeach; 
    ?>
    <span style="font-style: italic"><?php echo __d('ads', "Note: Guest user will not be affected by age and gender setting");?></span>
</ul>
