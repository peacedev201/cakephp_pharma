<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <?php echo __d('store', 'Set Feature');?>
</div>
<div class="modal-body">
    <form id="setFeatureForm" class="form-horizontal">
        <?php echo $this->Form->hidden('id', array(
            'value' => $id
        ));?>
        <div class="form-body">
            <?php if(!empty($store)):?>
            <div class="form-group">
                <label class="col-md-3 control-label" style="padding-top: 0">
                    <?php echo __d('store', 'Store');?>
                </label>
                <div class="col-md-9">
                    <?php echo $store['Store']['name'];?>
                </div>            
            </div>
            <?php elseif(!empty($store_product)):?>
            <div class="form-group">
                <label class="col-md-3 control-label" style="padding-top: 0">
                    <?php echo __d('store', 'Product');?>
                </label>
                <div class="col-md-9">
                    <?php echo $store_product['StoreProduct']['name'];?>
                </div>            
            </div>
            <?php endif;?>
            <div class="form-group">
                <label class="col-md-3 control-label" style="padding-top: 0">
                    <?php echo __d('store', 'Day');?>
                </label>
                <div class="col-md-9">
                    <?php echo $this->Form->text('feature_day', array(
                        'class' => 'form-control'
                    ));?>
                </div>            
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"></label>
                <div class="col-md-9">
                    <label>
                        <?php echo $this->Form->checkbox('unlimited_feature', array(
                            'hiddenField' => false
                        ));?>
                        <?php echo __d('store', 'Unlimited');?>
                    </label>
                </div>            
            </div>
        </div>
    </form>
    <div style="display:none;" class="error-message" id="setFeatureMessage"></div>
</div>
<div class="modal-footer">
    <a id="setFeatureButton" class="btn btn-action" href="javascript:void(0)">
        <?php echo __d('store', 'Ok');?>
    </a>
    <button type="button" class="btn default" data-dismiss="modal">
        <?php echo __d('store', 'Cancel');?>
    </button>
    <div class="clear"></div>
</div>

<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
<?php endif; ?>
    
    //view detail
    jQuery(document).on('click', '#unlimited', function(){
        if(jQuery(this).is(':checked'))
        {
            jQuery('#feature_day').attr('disabled', 'disabled');
        }
        else
        {
            jQuery('#feature_day').removeAttr('disabled');
        }
    })
    
<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>