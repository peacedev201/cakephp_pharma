<?php
$this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'store_manager'), 
    'object' => array('$', 'store_manager')
));?>
store_manager.initCreateOrder();
<?php $this->Html->scriptEnd(); ?>

<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>
    <?php echo $this->Element('manager_menu'); ?>
<?php $this->end(); ?>
<div class="bar-content">
    <?php echo $this->Element('Store.mobile/mobile_manager_menu'); ?>
    <div class="content_center">
        <ul class="breadcrumb">
            <li>
                <a href="<?php echo STORE_MANAGER_URL;?>">
                    <i class="material-icons">home</i>
                </a>
                <span class="divider"></span>
            </li>
            <li>
                <a href="<?php echo $url;?>">
                    <?php echo __d('store', "Manage Orders");?>
                </a>
                <span class="divider"></span>
            </li>
            <li class="first">
                <a class="active" href="<?php echo STORE_MANAGER_URL;?>orders/create">
                    <?php if($order['StoreOrder']['id'] > 0):?>
                        <?php echo __d('store', "Edit Order");?>
                    <?php else:?>
                        <?php echo __d('store', "Create Order");?>
                    <?php endif;?>
                </a>
                <span class="divider-last"></span>
            </li>
        </ul>
        <form class="form-horizontal" id='createForm' action="<?php echo  STORE_MANAGER_URL; ?>orders/save" method="post">
            <?php echo $this->Form->hidden('save_type', array(
                'value' => 0
            ));?>
            <?php echo $this->Form->hidden('except_product_ids', array(
                'value' => $except_product_ids
            ));?>
            <?php if(Configure::read('Store.store_enable_shipping') || $order['StoreOrder']['store_shipping_id'] > 0):?>
                <?php echo $this->Form->hidden('select_store_shipping_id', array(
                    'value' => $order['StoreOrder']['store_shipping_id']
                ));?>
            <?php endif;?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?php if($order['StoreOrder']['id'] > 0):?>
                        <?php echo __d('store', "Edit Order");?>
                    <?php else:?>
                        <?php echo __d('store', "Create Order");?>
                    <?php endif;?>
                    <div class="pull-right">
                        <input id="btnSave" type="button" class="btn btn-primary" value="<?php echo __d('store', 'Save');?>"/>
                        <input id="btnApply" type="button" class="btn btn-primary" value="<?php echo __d('store', 'Apply');?>"/>
                        <input id="btnCancel" type="button" class="btn btn-primary" value="<?php echo __d('store', 'Cancel');?>" onclick="<?php echo $is_app ? "window.mobileAction.backAndRefesh();" : "window.location = '".$url."'"?>"/>
                    </div>
                    <div class="clear"></div>
                </div>
                <?php echo $this->Form->hidden('id', array('value' => $order['StoreOrder']['id']));?>
                <div class="panel-body">
                    <div class="error-message" id="errorMessage" style="display: none"></div>
                    <div class="Metronic-alerts alert alert-success fade in" style="display: none"></div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <div class="col-md-12">
                                <label class="order-title"><?php echo __d('store', 'Billing Info')?></label>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-4">
                                <label><?php echo __d('store',  'Email')?> <span class="required">*</span></label>
                            </div>
                            <div class="col-sm-8">
                                <?php echo $this->Form->text('billing_email', array('value' => $order['StoreOrder']['billing_email'])); ?>
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4">
                                <label><?php echo __d('store',  'First Name')?> <span class="required">*</span></label>
                            </div>
                            <div class="col-sm-8">
                                <?php echo $this->Form->text('billing_first_name', array('value' => $order['StoreOrder']['billing_first_name'])); ?>
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4">
                                <label><?php echo __d('store',  'Last Name')?> <span class="required">*</span></label>
                            </div>
                            <div class="col-sm-8">
                                <?php echo $this->Form->text('billing_last_name', array('value' => $order['StoreOrder']['billing_last_name'])); ?>
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4">
                                <label><?php echo __d('store',  'Company')?></label>
                            </div>
                            <div class="col-sm-8">
                                <?php echo $this->Form->text('billing_company', array('value' => $order['StoreOrder']['billing_company'])); ?>
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4">
                                <label><?php echo __d('store',  'Phone')?> <span class="required">*</span></label>
                            </div>
                            <div class="col-sm-8">
                                <?php echo $this->Form->text('billing_phone', array('value' => $order['StoreOrder']['billing_phone'])); ?>
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4">
                                <label><?php echo __d('store',  'Address')?> <span class="required">*</span></label>
                            </div>
                            <div class="col-sm-8">
                                <?php echo $this->Form->text('billing_address', array('value' => $order['StoreOrder']['billing_address'])); ?>
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4">
                                <label><?php echo __d('store',  'Country')?> <span class="required">*</span></label>
                            </div>
                            <div class="col-sm-6">
                                <?php echo $this->Form->select('billing_country_id', $this->Store->getCountryList(), array(
                                    'label' => false,
                                    'div' => false,
                                    'style' => 'width:100%',
                                    'empty' => array('' => __d('store', 'Select country')),
                                    'class' => 'input-text',
                                    'value' => $order['StoreOrder']['billing_country_id']
                                ));?>
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4">
                                <label><?php echo __d('store',  'City')?> <span class="required">*</span></label>
                            </div>
                            <div class="col-sm-8">
                                <?php echo $this->Form->text('billing_city', array('value' => $order['StoreOrder']['billing_city'])); ?>
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4">
                                <label><?php echo __d('store',  'Postcode')?></label>
                            </div>
                            <div class="col-sm-8">
                                <?php echo $this->Form->text('billing_postcode', array('value' => $order['StoreOrder']['billing_postcode'])); ?>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <input class="copy_order_btn" type="button" value="<?php echo __d('store', 'Copy');?>" style="margin-top:200px;" />
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <div class="col-md-12">
                                <label  class="order-title"><?php echo __d('store', 'Shipping Info')?></label>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-4">
                                <label><?php echo __d('store',  'Email')?> <span class="required">*</span></label>
                            </div>
                            <div class="col-sm-8">
                                <?php echo $this->Form->text('shipping_email', array('value' => $order['StoreOrder']['shipping_email'])); ?>
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4">
                                <label><?php echo __d('store',  'First Name')?> <span class="required">*</span></label>
                            </div>
                            <div class="col-sm-8">
                                <?php echo $this->Form->text('shipping_first_name', array('value' => $order['StoreOrder']['shipping_first_name'])); ?>
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4">
                                <label><?php echo __d('store',  'Last Name')?> <span class="required">*</span></label>
                            </div>
                            <div class="col-sm-8">
                                <?php echo $this->Form->text('shipping_last_name', array('value' => $order['StoreOrder']['shipping_last_name'])); ?>
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4">
                                <label><?php echo __d('store',  'Company')?></label>
                            </div>
                            <div class="col-sm-8">
                                <?php echo $this->Form->text('shipping_company', array('value' => $order['StoreOrder']['shipping_company'])); ?>
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4">
                                <label><?php echo __d('store',  'Phone')?> <span class="required">*</span></label>
                            </div>
                            <div class="col-sm-8">
                                <?php echo $this->Form->text('shipping_phone', array('value' => $order['StoreOrder']['shipping_phone'])); ?>
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4">
                                <label><?php echo __d('store',  'Address')?> <span class="required">*</span></label>
                            </div>
                            <div class="col-sm-8">
                                <?php echo $this->Form->text('shipping_address', array('value' => $order['StoreOrder']['shipping_address'])); ?>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-4">
                                <label><?php echo __d('store',  'Country')?> <span class="required">*</span></label>
                            </div>
                            <div class="col-sm-6">
                                <?php echo $this->Form->select('shipping_country_id', $this->Store->getCountryList(), array(
                                    'label' => false,
                                    'div' => false,
                                    'style' => 'width:100%',
                                    'empty' => array('' => __d('store', 'Select country')),
                                    'class' => 'input-text',
                                    'value' => $order['StoreOrder']['shipping_country_id']
                                ));?>
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4">
                                <label><?php echo __d('store',  'City')?> <span class="required">*</span></label>
                            </div>
                            <div class="col-sm-8">
                                <?php echo $this->Form->text('shipping_city', array('value' => $order['StoreOrder']['shipping_city'])); ?>
                            </div>
                            <div class="clear"></div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4">
                                <label><?php echo __d('store',  'Postcode')?></label>
                            </div>
                            <div class="col-sm-8">
                                <?php echo $this->Form->text('shipping_postcode', array('value' => $order['StoreOrder']['shipping_postcode'])); ?>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    <div class="col-md-10">
                        <div class="form-group">
                            <div class="col-md-2">
                                <label><?php echo __d('store',  'Order notes')?>: </label>
                            </div>
                            <div class="col-sm-8">
								<?php echo $this->Form->textarea('order_comments', array(
                                    'value' => $order['StoreOrder']['order_comments'],
                                    'style' => 'width:100%'
                                )); ?>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    <div class="col-md-5 order-payment">
                        <div class="form-group">
                            <div class="col-md-4">
                                <label><?php echo __d('store',  'Payment')?>: </label>
                            </div>
                            <div class="col-sm-8">
								<select id="payment" name="data[store_payment_id]">
									<?php foreach($payments as $payment):
										$payment = $payment['StorePayment'];
									?>
										<option value="<?php echo $payment['id'];?>" data-online="<?php echo $payment['is_online'];?>" <?php if($payment['id'] == $order['StoreOrder']['store_payment_id']):?>selected="selected"<?php endif;?>>
											<?php echo $payment['name'];?>
										</option>
									<?php endforeach;?>
								</select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 order-payment" id="payment_transaction_id" style="display: none">
                        <div class="form-group">
                            <div class="col-md-4">
                                <label><?php echo __d('store',  'Transaction Id')?>: </label>
                            </div>
                            <div class="col-sm-8">
                        <?php echo $this->Form->text('transaction_id' ,array(
                            'value' => $order['StoreOrder']['transaction_id'],
                            'div' => false,
                            'lable' => false
                        ))?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 order-status">
                        <div class="form-group">
                            <div class="col-md-4">
                                <label><?php echo __d('store',  'Status')?>: </label>
                            </div>
                            <div class="col-sm-8">
                        <?php echo $this->Form->select('order_status', $order_status,array(
                            'value' => $order['StoreOrder']['order_status'],
                            'empty' => false
                        ))?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <div class="form-group">
                            <div class="col-md-2">
                                <label><?php echo __d('store',  'Shipping')?>: </label>
                            </div>
                            <div class="col-sm-10" id="order_shipping">
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                    <a href="javascript:void(0)" class="order-add-detaildata btn btn-action order_detail_dialog"> 
                        <?php echo  __d('store',  'Add') ?>
                    </a>
                    <br/><br/>
                    <div class="div-detail-app" id="table-order-details">
                        <div class="div-full-breabcrum">
                            <div class="col-md-2">
                                <div class="group-group text-left">
                                    <i class="text-app"><?php echo __d('store', 'Code');?></i>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="group-group text-left">
                                    <i class="text-app"><?php echo __d('store', 'Product name');?></i>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="group-group">
                                    <i class="text-app"><?php echo __d('store', 'Price');?></i>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="group-group">
                                    <i class="text-app"><?php echo __d('store', 'Quantity');?></i>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="group-group">
                                    <i class="text-app"><?php echo __d('store', 'Amount');?></i>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="group-group">
                                    <i class="text-app"><?php echo __d('store', 'Action');?></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>		
            </div>
        </form>
    </div>
</div>

<script type="text/template" id="data-products">
    <?php echo !empty($order['StoreOrderDetail']) ? json_encode($order['StoreOrderDetail']) : '';?>
</script>

<script type="text/template" id="data-template">
    <div class="div-detail-row"  data-value="product">
        <?php echo $this->Form->hidden('product_id.', array(
            'class' => 'product_id_value'
        ));?>
        <?php echo $this->Form->hidden('quantity.', array(
            'class' => 'quantity_value'
        ));?>
        <?php echo $this->Form->hidden('weight', array(
            'class' => 'weight'
        ));?>
        <div class="top-list-brb">
            <?php echo __d('store', "Order Listing");?>
        </div>
        <div class="col-xs-12 col-md-2">
            <div class="group-group  text-left">
                <i class="visible-sm visible-xs icon-app material-icons">business_center</i>
                <i class="text-app product_code" data-value=""></i>
            </div>
        </div>
        <div class="col-xs-12 col-md-4">
            <div class="group-group text-left">
                <i class="visible-sm visible-xs icon-app material-icons">payment</i>
                <i class="text-app product_name" data-value=""></i>
            </div>
        </div>
        <div class="col-xs-12 col-md-2">
            <div class="group-group text-center">
                <i class="visible-sm visible-xs icon-app material-icons">monetization_on</i>
                <i class="text-app price" data-value=""></i>
            </div>
        </div>
        <div class="col-xs-12 col-md-1">
            <div class="group-group text-center">
                <i class="visible-sm visible-xs icon-app material-icons">keyboard_hide</i>
                <i class="text-app quantity" data-value=""></i>
            </div>
        </div>
        <div class="col-xs-12 col-md-1">
            <div class="group-group text-center">
                <i class="visible-sm visible-xs icon-app material-icons">monetization_on</i>
                <i class="text-app amount" data-value=""></i>
            </div>
        </div>
        <div class="hidden-xs hidden-sm col-md-2 no-border-right">
            <a class="action-manage edit order_detail_dialog" href="javascript:void(0)" data-id="">
                <i class="text-full hidden-sm hidden-xs"> <?php echo __d('store', 'Edit');?></i>
            </a>
            <a class="action_delete delete clear_order_detail" href="javascript:void(0)" data-id="">
                <i class="text-full hidden-sm hidden-xs"><?php echo __d('store', 'Delete') ?></i>                                
            </a>
        </div>
        <div class="visible-xs visible-sm col-xs-6 iconnottext iscenter">
            <a href="javascript:void(0)" class="edit order_detail_dialog" data-id="">
                <i class="material-icons">create</i>
            </a>
        </div>
        <div class="visible-xs visible-sm col-xs-6 iconnottext">
            <a href="javascript:void(0)" class="delete clear_order_detail" data-id="">
                <i class="material-icons">delete_sweep</i>                                  
            </a>
        </div>
    </div>
</script>

<?php echo $this->Element('Store.detail_form_modal');?>

<!-- product short list modal -->
<div style="min-height: 400px;z-index: 9999" class="modal fade" id="product_short_list" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
        <?php echo  __d('store',  'Loading'); ?>
            </div>
        </div>
    </div>
</div>

<?php if($this->request->is('ajax')): ?>
<script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
<?php endif; ?>

    $(function () {
        $('.action').on('click', function () {
            $('.action').addClass('disable');
        })
    })

    jQuery(window).load(function () {

    })

<?php if($this->request->is('ajax')): ?>
</script>
<?php else: ?>
	<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>

<?php
if($is_app)
{
    $this->MooGzip->script(array('zip'=>'mobile.action.bundle.js.gz','unzip'=>'MooApp.mobile.action.bundle'));
}
?>