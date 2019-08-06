<div class="bar-content">
    <div class="content_center full_content p_m_10">
        <div class="mo_breadcrumb">
            <h1><?php echo __('Join Pharmatalk 2/2')?></h1>
        </div>
        <?php if($cuser['specialty'] == SPECIALTY_STUDENT){
                echo $this->element('user/step_student');
            }else{
                echo $this->element('user/step_company');
            }
            ?>
        <div class="form-group required">
            <label class="col-md-3 control-label"></label>
            <div class="col-md-9 form-inline">
                <a class="btn btn-action" href="<?php echo $this->request->base.'/users/step_2';?>"><?php echo __('Previous');?></a>
                <a class="btn btn-action" id="btn_submit_step_3"><?php echo __('Done');?></a>
            </div>
            <div class="clear"></div>
        </div>
        <div class="error-message" style="display:none;"></div>
    </div>
</div>

<?php if($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery","mooUser"], function($,mooUser) {
            mooUser.initRegStep3();
        });
    </script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooUser'), 'object' => array('$','mooUser'))); ?>
    mooUser.initRegStep3();
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>