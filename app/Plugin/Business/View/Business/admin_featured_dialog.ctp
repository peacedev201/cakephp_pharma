<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo __d('business', 'Feature business');?></h4>
</div>
<div class="modal-body">
    <form id="featuredForm" class="form-horizontal" role="form">
        <?php echo $this->Form->hidden('business_id', array(
            'value' => $business_id
        ));?>
        <div class="form-body">
            <?php echo __d('business', 'How many days do you want to make this business as featured business?');?>
            <?php echo $this->Form->text('day', array(
                'placeholder' => __d('business', 'Enter day number'), 
                'class' => 'form-control', 
            )); ?>
        </div>
    </form>
    <div id="featuredMessage" class="alert alert-danger error-message" style="display:none;margin-top:10px;">
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn default" data-dismiss="modal"><?php echo  __d('business', 'Close') ?></button>
    <a href="javascript:void(0)" id="createButton" class="btn btn-action" onclick="jQuery.admin.setBusinessFeatured('<?php echo  $admin_url?>featured');">
        <?php echo  __d('business', 'Featured now') ?>
    </a>
</div>