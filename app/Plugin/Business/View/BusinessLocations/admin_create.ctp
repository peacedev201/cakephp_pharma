<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <?php if(!$bIsEdit): ?>
        <h4 class="modal-title"><?php echo __d('business', 'Add New Country');?></h4>
    <?php else:?>
        <h4 class="modal-title"><?php echo __d('business', 'Edit Country');?></h4>
    <?php endif;?>
        <div><?php echo __d('business', 'Please fill out the form below to create a new country');?></div>
    
</div>
<div class="modal-body">
    <form id="createForm" class="form-horizontal" role="form">
        <?php echo $this->Form->hidden('id', array('value' => $country['BusinessLocation']['id'])); ?>
        <div class="form-body">
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('business', 'Name');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('name', array('placeholder' => __d('business', 'Enter text'), 'class' => 'form-control', 'value' => $country['BusinessLocation']['name'])); ?>

                </div>
            </div>      
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('business', 'Enabled');?></label>
                <div class="col-md-9">
                    <div class="checkbox-list">
                        <?php echo $this->Form->checkbox('enabled', array('checked' => $country['BusinessLocation']['id'] > 0 ? $country['BusinessLocation']['enabled'] : true)); ?>
                    </div>
                    <?php echo __d('business', 'If you enable/disable this location, all of child locations will be auto changed.');?>
                </div>
            </div>   
        </div>
    </form>
    <div class="alert alert-danger error-message" style="display:none;margin-top:10px;">
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn default" data-dismiss="modal"><?php echo  __d('business', 'Close') ?></button>
    <a href="#" id="createButton" class="btn btn-action"><?php echo  __d('business', 'Save') ?></a>
</div>
<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
<?php endif; ?>
    jQuery(document).ready(function() {
       jQuery.admin.initCreateItem("<?php echo  $this->request->base ?>/admin/business/business_locations/save"); 
    });
<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd(); ?>
<?php endif; 