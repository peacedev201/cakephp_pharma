<?php $helper = MooCore::getInstance()->getHelper('Contest_Contest');?>
<?php if(!empty($contest)): ?>
<?php if($this->request->is('ajax')): ?>
<script>
    require(["jquery","mooContest"], function($,mooContest) {
        mooContest.initOnPolicy();
    });
</script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false,'requires'=>array('jquery','mooContest'),'object'=>array('$','mooContest'))); ?>
        mooContest.initOnPolicy();
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>
<h2><?php echo __d('contest', 'Terms & Conditions') ?></h2>
<form style="display: none;" action="<?php echo  $this->request->base; ?>/contests/save_info" enctype="multipart/form-data" id="createForm" method="post">
    <div class="mo_breadcrumb">
        <h1><?php echo __d('contest','Edit Terms & Conditions');?></h1>
    </div>
    <div class="full_content p_m_10">
        <?php echo $this->Form->hidden('id', array('value' => $contest['Contest']['id'])); ?>
        <div class="form_content">
            <ul>	
                <li>
                    <div class='col-md-10'>
                        <?php echo $this->Form->textarea('term_and_condition', array('style' => 'height:200px;','value' => $contest['Contest']['term_and_condition'])); ?>
                    </div>
                    <div class="clear"></div>
                </li>            
                <li style="margin-top: 8px;">
                    <div class="col-md-10">
                        <button type="button" id="updateBtn" class='mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored'><?php echo __d('contest' ,'Save')?></button>
                        <a href="javascript:void(0);" class="button btn-cancel"><?php echo __('Cancel');?></a>	               
                    </div>
                    <div class="clear"></div>		                            
                </li>
                <li>
                    <div class="error-message" id="errorMessage" style="display:none"></div>
                </li>
        </div>
    </div>            
</form>
<div id="contest_policy">
    <div class="policy_desc">
        <?php echo $contest['Contest']['term_and_condition']; ?>
    </div>
    <?php if($helper->canEdit($contest, $viewer)): ?>
        <a id="edit_policy" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" href="javascript:void(0)">
            <?php echo __d('contest', 'Edit Terms & Conditions'); ?>
        </a>
    <?php endif; ?>
</div>
<?php else : ?>
<p><?php echo __d('contest', 'Contest does not exist') ?></p>
<?php endif; ?>
