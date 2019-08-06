<!-- Modal order detail -->
<div class="modal fade" id="order_detail_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" style="z-index: 8888">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel"><?php echo  __d('store',  'Order Detail'); ?></h4>
        </div>
        <div class="modal-body">
            <div id="order-detail-form">
                <?php echo $this->Form->hidden('product_id'); ?>
                <div class="form-group">
                    <div class="col-md-4">
                        &nbsp;
                    </div>
                    <div class="col-md-8">
                        <span class="mrg-10"><?php echo  __d('store', 'Choose from the list provided'); ?></span>
                        <div>
                            <a class="btn btn-primary load_product_short_list" href="javascript:void(0)">
                                <?php echo  __d('store', "Choose"); ?>
                            </a>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="form-group">
                    <div class="col-md-4">
                        <label><?php echo __d('store',  'Product Code')?><span class="required">(*)</span></label>
                    </div>
                    <div class="col-md-8">
                        <?php echo $this->Form->text('product_code', array(
                            'readonly' => true,
                            'disabled' => true
                        )); ?>
                    </div>
                    <div class="clear"></div>
                </div>

                <div class="form-group">
                    <div class="col-md-4">
                        <label><?php echo __d('store', "Product Name")?><span class="required">(*)</span></label>
                    </div>
                    <div class="col-md-8">
                        <?php echo $this->Form->text('product_name', array(
                            'readonly' => true,
                            'disabled' => true
                        )); ?>
                    </div>
                    <div class="clear"></div>
                </div>

                <div class="form-group">
                    <div class="col-md-4">
                        <label><?php echo __d('store', 'Price')?><span class="required">(*)</span></label>
                    </div>
                    <div class="col-md-8">
                        <?php echo $this->Form->text('price', array(
                            'readonly' => true,
                            'disabled' => true
                        )); ?>
                    </div>
                    <div class="clear"></div>
                </div>

                <div class="form-group">
                    <div class="col-md-4">
                        <label><?php echo __d('store',  'Quantity')?><span class="required">(*)</span></label>
                    </div>
                    <div class="col-md-8">
                        <?php echo $this->Form->number('quantity', array(
                            "size" => 2,
                            "min" => 1
                        )); ?>
                    </div>
                    <div class="clear"></div>
                </div>

                <div class="form-group">
                    <div class="col-md-4">
                        <label><?php echo __d('store',  'Amount')?></label>
                    </div>
                    <div class="col-md-8">
                        <?php echo $this->Form->text('amount', array(
                            'readonly' => true,
                            'disabled' => true
                        )); ?>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a type="button" class="btn btn-default" data-dismiss="modal">
                <?php echo  __d('store',  "Close");?>
            </a>
            <a type="button" class="btn btn-primary add_order_details" id="add_order_detail">
                <?php echo  __d('store', 'Save');?>
            </a>
        </div>
    </div>
  </div>
</div>