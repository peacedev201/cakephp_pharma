<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo  __d('business', "Translation") ?></h4>
</div>
<div class="modal-body">
    <form id="tCreateForm" class="form-horizontal" role="form">
        <?php echo $this->Form->hidden('id', array('value' => $item['BusinessType']['id'])); ?>
        <?php foreach ($languages as $key => $language) : ?>
            <?php $lval = ""; ?>
            <div class="form-body">
                <div class="form-group">
                    <label class="col-md-3 control-label"><?php echo $language; ?></label>
                    <div class="col-md-9">
                        <?php foreach ($item['nameTranslation'] as $translation) : ?>
                            <?php if ($translation['locale'] == $key) : ?>
                                <?php $lval = $translation['content'];
                                break; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php echo $this->Form->text('name.' . $key, array('placeholder' => 'Enter text', 'class' => 'form-control', 'value' => $lval)); ?>
                    </div>
                </div>
            </div>
<?php endforeach; ?>

    </form>

    <div class="alert alert-danger error-message" style="display:none;margin-top:10px;">

    </div>

</div>
<div class="modal-footer">
    <button type="button" class="btn default" data-dismiss="modal">Close</button>
    <a href="#" id="tCreateButton" class="btn btn-action">Save Change</a>

</div>
<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
<?php endif; ?>
    jQuery(document).ready(function() {
       jQuery.admin.initTranslate("<?php echo  $this->request->base ?>/admin/business/business_types/translate_save"); 
    });
<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd(); ?>
<?php endif; 