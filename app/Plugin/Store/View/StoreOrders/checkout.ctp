<?php
echo $this->Html->css(array(
    'Store.store',
    ), array('block' => 'css', 'minify'=>false));
?>
<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'store_store'), 
    'object' => array('$', 'store_store')
));?>
    store_store.initGlobal();
    store_store.initCheckout();
    <?php if(Configure::read('Store.store_enable_shipping')):?>
    store_store.initOrderShipping();
    <?php endif;?>
<?php $this->Html->scriptEnd(); ?> 
<div class="bar-content">  
    <?php echo $this->Element('Store.mobile/mobile_menu');?>
    <?php
        echo $this->Form->hidden('cart_currency_position', array(
            'value' => Configure::read('Store.currency_position')
        ));
    ?>
    <?php
        echo $this->Form->hidden('cart_currency_symbol', array(
            'value' => Configure::read('store.currency_symbol')
        ));
    ?>
    <?php echo $this->Form->hidden('allow_credit', array(
        'value' => $allow_credit
    ));?>
    <?php echo $this->Form->hidden('setting_show_money_type', array(
        'value' => $setting_show_money_type
    ));?>
    <div class="content_center">
        <div class="main-container default-page">
            <div class="container channel-container ">
                <div class="row">
                    <div class="col-xs-12 ">
                        <div class="page-content default-page">
                            <article class="post-7 page type-page status-publish hentry jsn-master" id="post-7">
                                <div class="checkout-wrap">
                                    <ul class="checkout-bar">
                                        <li class="visited first">
                                            <a href="javascript:void(0)"><?php echo  __d('store', 'Carts');?></a>
                                        </li>
                                        <li class="active">
                                            <a href="javascript:void(0)"><?php echo  __d('store', 'Billing & Payment');?></a>
                                        </li>
                                        <li><?php echo  __d('store', 'Complete');?></li>
                                    </ul>
                                </div>
                                <div class="entry-content">
                                    <div class="store_plugin">
                                        <div class="store_plugin-error" style="display: none"></div>
                                        <form enctype="multipart/form-data" id="checkoutForm" class="checkout store_plugin-checkout" method="post" name="checkout">
                                            <?php echo $this->Form->hidden('store_id', array(
                                                'value' => $store_id
                                            ));?>
                                            <div id="customer_details">
                                                <div class="store_plugin-billing-fields">
                                                    <h3><?php echo  __d('store', 'Billing Details');?></h3>
                                                    <p id="billing_first_name_field" class="form-row form-row form-row-first validate-required">
                                                        <label class="" for="billing_first_name"><?php echo __d('store', 'First Name');?> <abbr title="required" class="required">*</abbr></label>
                                                        <?php echo $this->Form->text('billing_first_name', array(
                                                            'label' => false,
                                                            'div' => false,
                                                            'class' => 'input-text'
                                                        ));?>
                                                    </p>
                                                    <p id="billing_last_name_field" class="form-row form-row form-row-last validate-required">
                                                        <label class="" for="billing_last_name"><?php echo __d('store', 'Last Name');?> <abbr title="required" class="required">*</abbr></label>
                                                        <?php echo $this->Form->text('billing_last_name', array(
                                                            'label' => false,
                                                            'div' => false,
                                                            'class' => 'input-text'
                                                        ));?>
                                                    </p>
                                                    <div class="clear"></div>
                                                    <p id="billing_company_field" class="form-row form-row form-row-wide">
                                                        <label class="" for="billing_company"><?php echo __d('store', 'Company Name');?></label>
                                                        <?php echo $this->Form->text('billing_company', array(
                                                            'label' => false,
                                                            'div' => false,
                                                            'class' => 'input-text'
                                                        ));?>
                                                    </p>
                                                    <p id="billing_email_field" class="form-row form-row form-row-first validate-required validate-email">
                                                        <label class="" for="billing_email"><?php echo __d('store', 'Email Address');?> <abbr title="required" class="required">*</abbr></label>
                                                        <?php echo $this->Form->text('billing_email', array(
                                                            'label' => false,
                                                            'div' => false,
                                                            'class' => 'input-text'
                                                        ));?>
                                                    </p>
                                                    <p id="billing_phone_field" class="form-row form-row form-row-last validate-required validate-phone">
                                                        <label class="" for="billing_phone"><?php echo __d('store', 'Phone');?> <abbr title="required" class="required">*</abbr></label>
                                                        <?php echo $this->Form->text('billing_phone', array(
                                                            'label' => false,
                                                            'div' => false,
                                                            'class' => 'input-text'
                                                        ));?>
                                                    </p>
                                                    <div class="clear"></div>
                                                    <p id="billing_country_field" class="form-row form-row form-row-wide validate-required validate-country">
                                                        <label class="" for="billing_country_id"><?php echo __d('store', 'Country');?> <abbr title="required" class="required">*</abbr></label>
                                                        <?php echo $this->Form->select('billing_country_id', $this->Store->getCountryList(), array(
                                                            'label' => false,
                                                            'div' => false,
                                                            'empty' => array('' => __d('store', 'Select country')),
                                                            'class' => 'input-text'
                                                        ));?>
                                                    </p>
                                                    <p id="billing_address_field" class="form-row form-row form-row-wide address-field validate-required">
                                                        <label class="" for="billing_address"><?php echo __d('store', 'Address');?> <abbr title="required" class="required">*</abbr></label>
                                                        <?php echo $this->Form->text('billing_address', array(
                                                            'label' => false,
                                                            'div' => false,
                                                            'class' => 'input-text'
                                                        ));?>
                                                    </p>
                                                    <p id="billing_postcode_field" class="form-row form-row address-field validate-postcode form-row-first" data-o_class="form-row form-row form-row-last address-field validate-postcode">
                                                        <label class="" for="billing_postcode"><?php echo __d('store', 'Postcode / Zip');?></label>
                                                        <?php echo $this->Form->text('billing_postcode', array(
                                                            'label' => false,
                                                            'div' => false,
                                                            'class' => 'input-text'
                                                        ));?>
                                                    </p>
                                                    <p id="billing_city_field" class="form-row form-row address-field validate-required form-row-last" data-o_class="form-row form-row form-row-wide address-field validate-required">
                                                        <label class="" for="billing_city"><?php echo __d('store', 'Town / City');?> <abbr title="required" class="required">*</abbr></label>
                                                        <?php echo $this->Form->text('billing_city', array(
                                                            'label' => false,
                                                            'div' => false,
                                                            'class' => 'input-text'
                                                        ));?>
                                                    </p>
                                                    <div class="clear"></div>
                                                </div>
                                                <div class="store_plugin-shipping-fields">
                                                    <h3 id="ship-to-different-address">
                                                        <label class="checkbox" for="ship-to-different-address-checkbox"><?php echo __d('store', "Ship to a different address?")?></label>
                                                        <input type="checkbox" value="1" name="ship_to_different_address" class="input-checkbox" id="ship-to-different-address-checkbox">
                                                    </h3>
                                                    <div class="shipping_address" style="display: none;">
                                                        <p id="shipping_first_name_field" class="form-row form-row form-row-first validate-required">
                                                            <label class="" for="shipping_first_name"><?php echo __d('store', 'First Name');?> <abbr title="required" class="required">*</abbr></label>
                                                            <?php echo $this->Form->text('shipping_first_name', array(
                                                                'label' => false,
                                                                'div' => false,
                                                                'class' => 'input-text'
                                                            ));?>
                                                        </p>
                                                        <p id="shipping_last_name_field" class="form-row form-row form-row-last validate-required">
                                                            <label class="" for="shipping_last_name"><?php echo __d('store', 'Last Name');?> <abbr title="required" class="required">*</abbr></label>
                                                            <?php echo $this->Form->text('shipping_last_name', array(
                                                                'label' => false,
                                                                'div' => false,
                                                                'class' => 'input-text'
                                                            ));?>
                                                        </p>
                                                        <div class="clear"></div>
                                                        <p id="shipping_company_field" class="form-row form-row form-row-wide">
                                                            <label class="" for="shipping_company"><?php echo __d('store', 'Company Name');?></label>
                                                            <?php echo $this->Form->text('shipping_company', array(
                                                                'label' => false,
                                                                'div' => false,
                                                                'class' => 'input-text'
                                                            ));?>
                                                        </p>
                                                        <p id="shipping_email_field" class="form-row form-row form-row-first validate-required validate-email">
                                                            <label class="" for="shipping_email"><?php echo __d('store', 'Email Address');?> <abbr title="required" class="required">*</abbr></label>
                                                            <?php echo $this->Form->text('shipping_email', array(
                                                                'label' => false,
                                                                'div' => false,
                                                                'class' => 'input-text'
                                                            ));?>
                                                        </p>
                                                        <p id="shipping_phone_field" class="form-row form-row form-row-last validate-required validate-phone">
                                                            <label class="" for="shipping_phone"><?php echo __d('store', 'Phone');?> <abbr title="required" class="required">*</abbr></label>
                                                            <?php echo $this->Form->text('shipping_phone', array(
                                                                'label' => false,
                                                                'div' => false,
                                                                'class' => 'input-text'
                                                            ));?>
                                                        </p>
                                                        <div class="clear"></div>
                                                        <p id="shipping_country_field" class="form-row form-row form-row-wide validate-required validate-country">
                                                            <label class="" for="shipping_country_id"><?php echo __d('store', 'Country');?> <abbr title="required" class="required">*</abbr></label>
                                                            <?php echo $this->Form->select('shipping_country_id', $this->Store->getCountryList(), array(
                                                                'label' => false,
                                                                'div' => false,
                                                                'empty' => array('' => __d('store', 'Select country')),
                                                                'class' => 'input-text'
                                                            ));?>
                                                        </p>
                                                        <p id="shipping_address_field" class="form-row form-row form-row-wide address-field validate-required">
                                                            <label class="" for="shipping_address"><?php echo __d('store', 'Address');?> <abbr title="required" class="required">*</abbr></label>
                                                            <?php echo $this->Form->text('shipping_address', array(
                                                                'label' => false,
                                                                'div' => false,
                                                                'class' => 'input-text'
                                                            ));?>
                                                        </p>
                                                        <p id="shipping_postcode_field" class="form-row form-row address-field validate-postcode form-row-first" data-o_class="form-row form-row form-row-last address-field validate-postcode">
                                                            <label class="" for="shipping_postcode"><?php echo __d('store', 'Postcode / Zip');?></label>
                                                            <?php echo $this->Form->text('shipping_postcode', array(
                                                                'label' => false,
                                                                'div' => false,
                                                                'class' => 'input-text'
                                                            ));?>
                                                        </p>
                                                        <p id="shipping_city_field" class="form-row form-row address-field validate-required form-row-last" data-o_class="form-row form-row form-row-wide address-field validate-required">
                                                            <label class="" for="shipping_city"><?php echo __d('store', 'Town / City');?> <abbr title="required" class="required">*</abbr></label>
                                                            <?php echo $this->Form->text('shipping_city', array(
                                                                'label' => false,
                                                                'div' => false,
                                                                'class' => 'input-text'
                                                            ));?>
                                                        </p>
                                                        <div class="clear"></div>
                                                    </div>
                                                    <p id="order_comments_field" class="form-row form-row notes"><label class="" for="order_comments"><?php echo __d('store', "Order Notes"); ?></label><textarea cols="5" rows="2" placeholder="<?php echo __d('store', 'Notes about your order, e.g. special notes for delivery.');?>" id="order_comments" class="input-text " name="order_comments"></textarea></p>
                                                </div>
                                            </div>
                                            <h3 id="order_review_heading"><?php echo __d('store', 'Your order');?></h3>
                                            <div class="store_plugin-checkout-review-order" id="order_review">
                                                <?php if(!empty($cart['items'])):?>
                                                    <table class="shop_table store_plugin-checkout-review-order-table">
                                                        <thead>
                                                            <tr>
                                                                <th class="product-name" width="60%"><?php echo __d('store', 'Products');?></th>
                                                                <th class="product-price text-right">
                                                                    <?php echo __d('store', 'Price');?>
                                                                </th>
                                                                <th class="product-total text-right" width="15%"><?php echo __d('store', 'Total');?></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php 
                                                            foreach($cart['items'] as $cart_item):
                                                                $store = $cart_item['Store'];
                                                                $products = $cart_item['Products'];
                                                            ?>
                                                                <tr>
                                                                    <td colspan="3" class="store_name">
                                                                        <?php echo __d('store', 'Store').': '.$store['name'];?>
                                                                        <?php if(!empty($store['payments'])):?>
                                                                            <span>(<?php echo $this->Store->getPaymentName($store['payments']);?>)</span>
                                                                        <?php endif;?>
                                                                    </td>
                                                                </tr>
                                                                <?php if($products != null):?>
                                                                    <?php foreach($products as $product):
                                                                        $product = $product['StoreProduct'];
                                                                    ?>
                                                                    <tr class="cart_item store_product_<?php echo $store['id'];?>" data-quantity="<?php echo $product['quantity'];?>" data-weight="<?php echo $product['weight'];?>">
                                                                        <td class="product-name">
                                                                            <?php echo $product['name'];?>						 
                                                                            <?php if($product['attributes'] != null):?>
                                                                            <div class="variation">
                                                                                <div class="variation-Size">
                                                                                    <?php echo __d('store', 'Attributes');?>: 
                                                                                    <?php echo implode(', ', $product['attributes']);?>
                                                                                </div>
                                                                            </div>
                                                                            <?php endif;?>
                                                                            <?php if($product['weight'] > 0):?>
                                                                            <div class="variation">
                                                                                <?php echo sprintf(__d('store', 'Weight: %s kg'), $product['weight']);?>
                                                                            </div>
                                                                            <?php endif;?>
                                                                        </td>
                                                                        <td class="total-price text-right">
                                                                            <div class="variation">
                                                                                <div class="variation-Size">
                                                                                    <?php echo $this->Store->formatMoney($product['new_price']);?>
                                                                                    <strong class="product-quantity">× <?php echo $product['quantity'];?></strong>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td class="product-total text-right">
                                                                            <span class="amount"><?php echo $this->Store->formatMoney($product['total_price']);?></span>						
                                                                        </td>
                                                                    </tr>
                                                                    <?php endforeach;?>
                                                                <?php endif;?>
                                                                <tr>
                                                                    <td colspan="2" class="text-right">
                                                                        <?php echo __d('store', 'Sub total');?>
                                                                        <?php echo $this->Form->hidden('sub_total_value_'.$store['id'], array(
                                                                            'value' => $store['total_price']
                                                                        ));?>
                                                                    </td>
                                                                    <td class="product-total text-right" id="sub_total_<?php echo $store['id'];?>">
                                                                        <?php echo $this->Store->formatMoney($store['total_price']);?>
                                                                    </td>
                                                                </tr>
                                                                <?php if(Configure::read('Store.store_enable_shipping')):?>
                                                                <tr class="cart_item">
                                                                    <td colspan="2" class="text-right">
                                                                        <?php echo __d('store', 'Shipping Fee');?>
                                                                        <div id="shipping_<?php echo $store['id'];?>"></div>
                                                                    </td>
                                                                    <td class="product-total text-right" id="shipping_fee_<?php echo $store['id'];?>">
                                                                        <?php echo $this->Store->formatMoney(0);?>
                                                                    </td>
                                                                </tr>
                                                                <?php endif;?>
                                                            <?php endforeach;?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr class="order-total text-right">
                                                                <td colspan="2" ><?php echo __d('store', 'Order Total');?></td>
                                                                <td>
                                                                    <?php echo $this->Form->hidden('total_amount_value', array(
                                                                        'value' => $cart['total']
                                                                    ));?>
                                                                    <strong>
                                                                        <span class="amount" id="total_amount">
                                                                            <?php echo $this->Store->formatMoney($cart['total']);?>
                                                                        </span>
                                                                    </strong> 
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                    <div class="store_plugin-checkout-payment" id="payment">
                                                        <ul class="payment_methods methods">
                                                            <?php if($store_payments != null):?>
                                                                <?php foreach($store_payments as $k => $store_payment):
                                                                    $store_payment = $store_payment['StorePayment'];
                                                                    if($store_payment['key_name'] == ORDER_GATEWAY_CREDIT && !$this->Store->storePermission(STORE_PERMISSION_CREDIT))
                                                                    {
                                                                        continue;
                                                                    }
                                                                ?>
                                                                    <li>
                                                                        <input type="radio" <?php if($k == 0):?>checked="checked"<?php endif;?> value="<?php echo $store_payment['id'];?>" name="store_payment_id" class="input-radio" id="store_payment_<?php echo $store_payment['id'];?>">
                                                                        <label for="store_payment_<?php echo $store_payment['id'];?>">
                                                                            <?php echo $store_payment['name'];?>
                                                                        </label>
                                                                        <?php if(!empty($store_payment['information'])):?>
                                                                        <div class="additional_info">
                                                                            <?php echo $store_payment['information'];?>
                                                                        </div>
                                                                        <?php endif;?>
                                                                        <div class="payment_box payment_method_cheque">
                                                                            <p><?php echo $store_payment['description'];?>	</p>
                                                                        </div>
                                                                    </li>
                                                                <?php endforeach;?>
                                                            <?php endif;?>
                                                        </ul>
                                                        <div class="form-row place-order">
                                                            <input type="button" data-value="Place order" value="<?php echo __d('store', 'Proceed');?>" id="place_order" class="button alt">
                                                        </div>
                                                        <div class="clear"></div>
                                                    </div>
                                                <?php else:?> 
                                                    <?php echo __d('store', 'No products');?>
                                                <?php endif;?>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <footer class="entry-meta">
                                </footer>
                            </article>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    
<?php if(Configure::read('Store.store_enable_shipping')):?>
<script type="text/template" id="shippingTemplate">
    <div class="form-group">
        <div class="col-md-9 shipping_name text-left">
            <input type="radio" class="shipping_method" name="store_shipping_id[]"/>
            <label style="text-transform: capitalize; color:#333333"></label>
        </div>
        <div class="col-md-3 shipping_price"></div>
    </div>
</script>
<?php endif;?>