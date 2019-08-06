<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <?php if (!$bIsEdit) : ?>
        <h4 class="modal-title"><?php echo __d('business', 'Add New Business Type');?></h4>
    <?php else: ?>
        <h4 class="modal-title"><?php echo __d('business', 'Edit Business Type');?></h4>
    <?php endif;?>
</div>
<div class="modal-body">
    <form id="createForm" class="form-horizontal" role="form">
        <?php echo $this->Form->hidden('id', array('value' => $type['BusinessType']['id'])); ?>
        <div class="form-body">
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('business', 'Name');?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('name', array('placeholder' => 'Enter text', 'class' => 'form-control', 'value' => $type['BusinessType']['name'])); ?>

                </div>
                <?php if (!$bIsEdit) : ?>
                    <div class="tips" style="margin-left: 165px;">*<?php echo  __d('business', 'You can add translation language after adding new business type') ?></div>
                <?php else : ?>
                    <div class="tips" style="margin-left: 165px;">
                        <?php
                                $this->MooPopup->tag(array(
                                       'href'=>$this->Html->url(array("controller" => "business_types",
                                                                      "action" => "admin_translate",
                                                                      "plugin" => 'business',
                                                                      $type['BusinessType']['id']
                                                                  )),
                                       'title' => __d('business', 'Translation'),
                                       'innerHtml'=> __d('business', 'Translation'),
                               ));
                           ?>
                       
                    </div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('business', 'Enable');?></label>
                <div class="col-md-9">
                    <div class="checkbox-list">
                        <?php echo $this->Form->checkbox('enable', array('checked' => $type['BusinessType']['enable'])); ?>
                    </div>
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
       jQuery.admin.initCreateItem("<?php echo  $this->request->base ?>/admin/business/business_types/save"); 
    });
<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd(); ?>
<?php endif; 