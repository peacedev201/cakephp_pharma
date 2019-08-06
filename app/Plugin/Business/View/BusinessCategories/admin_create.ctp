<?php 
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <?php if (!$bIsEdit): ?>
        <h4 class="modal-title"><?php echo __d('business', 'Add New Category'); ?></h4>
    <?php else: ?>
        <h4 class="modal-title"><?php echo __d('business', 'Edit Category'); ?></h4>
    <?php endif; ?>
    <div><?php echo __d('business', 'Please fill out the form below to create a new category business'); ?></div>

</div>
<div class="modal-body">
    <form id="createForm" class="form-horizontal" role="form">
        <?php echo $this->Form->hidden('id', array('value' => $cat['BusinessCategory']['id'])); ?>
        <div class="form-body">
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('business', 'Name'); ?></label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('name', array('placeholder' => __d('business', 'Enter text'), 'class' => 'form-control', 'value' => $cat['BusinessCategory']['name'])); ?>
                </div>
                <?php if (!$bIsEdit) : ?>
                    <div class="tips" style="margin-left: 165px;">*<?php echo __d('business', 'You can add translation language after creating category') ?></div>
                <?php else : ?>
                    <div class="tips" style="margin-left: 165px;">
                        <?php
                        $this->MooPopup->tag(array(
                            'href' => $this->Html->url(array("controller" => "business_categories",
                                "action" => "admin_ajax_translate",
                                "plugin" => "business",
                                $cat['BusinessCategory']['id']
                            )),
                            'title' => __d('business', 'Translation'),
                            'innerHtml' => __d('business', 'Translation'),
                        ));
                        ?>                       
                    </div>
                <?php endif; ?>
            </div>
            <?php if(count($parent_list) > 1): ?>
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo __d('business', 'Parent Category'); ?></label>
                    <div class="col-md-9">
                        <?php echo $this->Form->select('parent_id', $parent_list, array('class' => 'form-control', 'value' => $parent_id, 'escape' => false)); ?>
                    </div>
                </div>  
            <?php else : ?>
                <?php echo $this->Form->hidden('parent_id', array('value' => $parent_id)); ?> 
            <?php endif; ?>
                    
            <div class="form-group">
                <label class="col-md-3 control-label"><?php echo __d('business', 'Enable'); ?></label>
                <div class="col-md-9">
                    <div class="checkbox-list">
                        <?php echo $this->Form->checkbox('enable', array('checked' => $cat['BusinessCategory']['enable'])); ?>
                    </div>
                    <?php echo __d('business', 'If you enable/disable this category, all of child categories will be auto changed.');?>
                </div>
            </div>           
        </div>
    </form>
    <div class="alert alert-danger error-message" style="display:none;margin-top:10px;">
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn default" data-dismiss="modal"><?php echo __d('business', 'Close') ?></button>
    <a href="#" id="createButton" class="btn btn-action"><?php echo __d('business', 'Save') ?></a>

</div>
<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
<?php endif; ?>
    jQuery(document).ready(function() {
        jQuery.admin.initCreateItem("<?php echo $this->request->base ?>/admin/business/business_categories/save"); 
    });
<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd(); ?>
<?php endif; 