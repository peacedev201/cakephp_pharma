<?php

echo $this->Html->css(array( 
        'Store.admin',
    ), array('block' => 'css', 'minify'=>false));
?>
<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'store_store', 'store_metismenu'), 
    'object' => array('$', 'store_store')
));?>
store_store.initCreateStore();
<?php $this->Html->scriptEnd(); ?> 
<div class="bar-content">
    <?php echo $this->Element('Store.mobile/mobile_manager_menu'); ?>
    <div class="content_center">
        <div class="panel-body">
            <form id="createForm" class="createStore">
                <h1 class="store-form-name"><?php echo $store['id'] > 0 ? __d('store', 'Edit Stores') : __d('store', "Add New Stores");?></h1>	
                <div class="createStore-content">
                    <div class="form_content">
                        <form class="form-horizontal" id="formStore" method="post">
                            <?php echo $this->Form->hidden('store_id', array(
                                'value' => $store['id']
                            ));?>
                            <ul>
                                <?php if ($is_integrate_to_business): ?>
                                    <li class="form-row">
                                        <div class="col-md-2">
                                            <label><?php echo __d('store', "Select business");?> <a href="javascript:void(0)" class="tip required" title="<?php echo __d('store', "Products that you added into your store will also appear in the selected business page under 'Products' tab");?>">(?)</a></label>
                                        </div>
                                        <div class="col-md-10">
                                            <?php echo $this->Form->select("business_id", $listBusiness, array(
                                                'div' => false,
                                                'label' => false,
                                                'class' => 'form-control',
                                                'value' => $store['business_id'],
                                                'empty' => __d('store', 'Select business page')
                                            ));?>
                                        </div>
                                        <div class="clear"></div>
                                    </li>
                                <?php endif; ?>
                                <li class="form-row">
                                    <div class="col-md-2">
                                        <label><?php echo __d('store', "Name");?> <span class="required" style="color: red">(*)</span></label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->input("name", array(
                                            'div' => false,
                                            'label' => false,
                                            'class' => 'form-control',
                                            'value' => $store['name']
                                        ));?>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li class="form-row">
                                    <div class="col-md-2">
                                        <label><?php echo __d('store', "Email");?> <span class="required" style="color: red">(*)</span></label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->input("email", array(
                                            'div' => false,
                                            'label' => false,
                                            'class' => 'form-control',
                                            'value' => $store['email']
                                        ));?>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li class="form-row">
                                    <div class="col-md-2">
                                        <label><?php echo __d('store', "Address");?> <span class="required" style="color: red">(*)</span></label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->input("address", array(
                                            'div' => false,
                                            'label' => false,
                                            'class' => 'form-control',
                                            'value' => $store['address']
                                        ));?>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li class="form-row">
                                    <div class="col-md-2">
                                        <label><?php echo __d('store', "Phone");?> <span class="required" style="color: red">(*)</span></label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->input("phone", array(
                                            'div' => false,
                                            'label' => false,
                                            'class' => 'form-control',
                                            'value' => $store['phone']
                                        ));?>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li class="form-row">
                                    <div class="col-md-2">
                                        <label><?php echo __d('store', "Image");?> <span class="required" style="color: red">(*)</span></label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->hidden("image", array(
                                            'value' => $store['image'],
                                            'id' => 'store_image_value'
                                        ));?>
                                        <div id="store_image"></div>
                                        <div id="store_image_preview">
                                            <?php if (!empty($store['image'])): ?>
                                                <img width="200" src="<?php echo $this->Store->getStoreImage($store) ?>" />
                                            <?php else: ?>
                                                <img width="200" style="display: none;" src="" />
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li>
                                    <div class="col-md-2">
                                        <label><?php echo __d('store', "Payment types accepted");?></label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php if($payments != null):
                                            $select_payment = !empty($store['payments']) ? explode(',', $store['payments']) : array();
                                        ?>
                                            <?php foreach($payments as $payment):
                                                if($payment['StorePayment']['key_name'] == ORDER_GATEWAY_CREDIT && !$this->Store->storePermission(STORE_PERMISSION_CREDIT))
                                                {
                                                    continue;
                                                }
                                            ?>
                                            <div>
                                                <label for="payment_id_<?php echo $payment['StorePayment']['id'];?>">
                                                    <?php echo $this->Form->checkbox("store_payment.", array(
                                                        'hiddenField' => false,
                                                        'value' => $payment['StorePayment']['id'],
                                                        'id' => 'payment_id_'.$payment['StorePayment']['id'],
                                                        'class' => 'select_payment',
                                                        'data-is_online' => $payment['StorePayment']['is_online'],
                                                        'checked' => in_array($payment['StorePayment']['id'], $select_payment) ? 'checked' : ''
                                                    ));?>
                                                    <?php echo $payment['StorePayment']['name'];?> -
                                                    (<?php echo $payment['StorePayment']['description'];?>)
                                                </label>
                                            </div>
                                            <?php endforeach;?>
                                        <?php endif;?>
                                        <?php if(Configure::read('Store.store_site_profit') > 0):?>
                                        <p>
											<?php echo sprintf(__d('store', 'Note: Each order will be charged %s%% for online transaction.'), Configure::read('Store.store_site_profit'));?>
                                        </p>
										<?php endif;?>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li class="form-row online_info" style="display: none">
                                    <div class="col-md-2">
                                        <label><?php echo __d('store', "Paypal Email");?></label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->input("paypal_email", array(
                                            'div' => false,
                                            'label' => false,
                                            'class' => 'form-control',
                                            'value' => $store['paypal_email']
                                        ));?>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li class="form-row online_info" style="display: none">
                                    <div class="col-md-2">
                                        <label><?php echo __d('store', "Paypal First Name");?></label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->input("paypal_first_name", array(
                                            'div' => false,
                                            'label' => false,
                                            'class' => 'form-control',
                                            'value' => $store['paypal_first_name']
                                        ));?>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li class="form-row online_info" style="display: none">
                                    <div class="col-md-2">
                                        <label><?php echo __d('store', "Paypal Last Name");?></label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->input("paypal_last_name", array(
                                            'div' => false,
                                            'label' => false,
                                            'class' => 'form-control',
                                            'value' => $store['paypal_last_name']
                                        ));?>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                                <li class="form-row">
                                    <div class="col-md-2">
                                        <label><?php echo __d('store', "Return & Refund Policy");?></label>
                                    </div>
                                    <div class="col-md-10">
                                        <?php echo $this->Form->tinyMCE('policy', array(
                                            'value' => $store['policy'], 
                                            'id' => 'editor2',
                                            'plugins' => 'emoticons link image code',
                                            'toolbar1' => (!$is_app && !$is_mobile) ? 'bold italic underline strikethrough | bullist numlist | link unlink image emoticons blockquote code' : 'bold italic underline strikethrough | bullist numlist | unlink emoticons blockquote'
                                        )); ?>
                                        <?php echo __d('store', "Policies are shown on product detail page");?>
                                    </div>
                                    <div class="clear"></div>
                                </li>
                            </ul>
                        </form>
                        <div class="col-md-2">&nbsp;</div>
                        <div class="col-md-10">
                            <div style="margin:20px 0">           
                                <a href="javascript:void(0)" class="btn btn-primary" id="createStoreButton">
                                    <?php echo __d('store', 'Save')?>
                                </a>
                                <a href="javascript:void(0)" onclick="<?php echo $is_app ? "window.mobileAction.backOnly();" : "window.history.go(-1);"?>" class="btn btn-primary" id="cancelStoreButton">
                                    <?php echo __d('store', 'Cancel')?>
                                </a>
                            </div>
                            <div class="error-message" id="errorMessage" style="display:none"></div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
if($is_app)
{
    $this->MooGzip->script(array('zip'=>'mobile.action.bundle.js.gz','unzip'=>'MooApp.mobile.action.bundle'));
}
?>