<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>
    <div class="bar-content">
        <div class="profile-info-menu">
            <?php echo $this->element('profilenav', array("cmenu" => "profile"));?>
        </div>
    </div>
<?php $this->end(); ?>

<div class="bar-content">
    <div class="content_center full_content p_m_10">
        <div class="mo_breadcrumb">
            <h1><?php echo __('Profile Information')?></h1>
            <a href="<?php echo $this->request->base?>/users/view/<?php echo $uid?>" class="topButton button button-action button-mobi-top"><?php echo __('View Profile')?></a>
        </div>
        <div class="full_content">
            <div class="content_center">
                <h2><?php echo __('Job Information')?></h2>
                <?php if($cuser['specialty'] == SPECIALTY_STUDENT){
                    echo $this->element('user/step_student');
                }else{
                    echo $this->element('user/step_company');
                }
                ?>
                <div class="form-group required">
                    <label class="col-md-3 control-label"></label>
                    <div class="col-md-9 form-inline">
                        <a class="btn btn-action" id="btn_submit_step_3"><?php echo __('Save Changes');?></a>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="error-message" style="display:none;"></div>
            </div>
        </div>
    </div>
</div>

<?php if($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery","mooUser"], function($,mooUser) {
            mooUser.initRegStep3('profile');
        });
    </script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooUser'), 'object' => array('$','mooUser'))); ?>
    mooUser.initRegStep3('profile');
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>