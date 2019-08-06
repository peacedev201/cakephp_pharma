<form id="form_reg_step_3_student">
    <div class="form-group required">
        <label class="col-md-3 control-label">
            <?php echo __('Grade')?>
        </label>
        <div class="col-md-9 form-inline">
            <?php echo $this->Form->radio('uni_grade', $grade, array('class' => ' ','id' => false, 'label' => false, 'value' => $cuser['uni_grade'],'hiddenField'=>true, 'legend'=> false, 'separator'=>'&nbsp;&nbsp;&nbsp;')); ?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="form-group required">
        <label class="col-md-3 control-label">
            <?php echo __('Confirm request to')?>
        </label>
        <div class="col-md-9 form-inline">
            <span><?php echo __('Student Representative'); ?>&nbsp;&nbsp;&nbsp;</span>
            <?php echo $this->Form->text('represent_email', array('value' => $cuser['represent_email'], 'placeholder' => __('Email'))); ?>
            (<a data-placement="top" data-html="true" original-title="<?php echo __('Please enter email of your Student representative to get confirmation. Your account will be activate if it’s confirmed by Student representative.');?>" class="tip" href="javascript:void(0);">?</a>)
        </div>
        <div class="clear"></div>
    </div>
</form>