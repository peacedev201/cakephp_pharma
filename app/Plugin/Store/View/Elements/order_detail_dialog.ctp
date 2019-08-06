<?php 
    $orderDetail = $order['StoreOrderDetail'];
	$store_payment = $order['StorePayment'];
    $order = $order['StoreOrder'];
    $shipping = $order['store_shipping_id'] > 0 ? $this->Store->getShippingDetail($order['store_shipping_id'], $order['store_id']) : null;
    $showMoneyType = $store_payment['key_name'] == ORDER_GATEWAY_CREDIT ? STORE_SHOW_MONEY_TYPE_CREDIT : STORE_SHOW_MONEY_TYPE_NORMAL;
?>
<div class="title-modal">
    <?php echo __d('store', 'Order detail');?>    
    <button data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <div class="col-md-6 st-mn-table">
        <h2 class="title"><?php echo  __d('store',  'Billing Info') ?></h2>
        <div class="form-group">
            <div class="col-md-4">
                <label><?php echo __d('store',  'Name')?></label>
            </div>
            <div class="col-md-8">
                <?php echo  $order['billing_first_name']; ?> <?php echo  $order['billing_last_name']; ?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="form-group">
            <div class="col-md-4">
                <label><?php echo __d('store',  'Email')?></label>
            </div>
            <div class="col-md-8">
                <?php echo  $order['billing_email']; ?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="form-group">
            <div class="col-md-4">
                <label><?php echo __d('store',  'Phone')?></label>
            </div>
            <div class="col-md-8">
                <?php echo  $order['billing_phone']; ?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="form-group">
            <div class="col-md-4">
                <label><?php echo __d('store',  'Country')?></label>
            </div>
            <div class="col-md-8">
                <?php echo  $order['billing_country']; ?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="form-group">
            <div class="col-md-4">
                <label><?php echo __d('store',  'City')?></label>
            </div>
            <div class="col-md-8">
                <?php echo  $order['billing_city']; ?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="form-group">
            <div class="col-md-4">
                <label><?php echo __d('store',  'Address')?></label>
            </div>
            <div class="col-md-8">
                <?php echo  $order['billing_address']; ?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="form-group">
            <div class="col-md-4">
                <label><?php echo __d('store',  'Postcode')?></label>
            </div>
            <div class="col-md-8">
                <?php echo  $order['billing_postcode']; ?>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="col-md-6 st-mn-table">
        <h2 class="title"><?php echo  __d('store',  'Shipping Info') ?></h2>
        <div class="form-group">
            <div class="col-md-4">
                <label><?php echo __d('store',  'Name')?></label>
            </div>
            <div class="col-md-8">
                <?php echo  $order['shipping_first_name']; ?> <?php echo  $order['shipping_last_name']; ?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="form-group">
            <div class="col-md-4">
                <label><?php echo __d('store',  'Email')?></label>
            </div>
            <div class="col-md-8">
                <?php echo  $order['shipping_email']; ?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="form-group">
            <div class="col-md-4">
                <label><?php echo __d('store',  'Phone')?></label>
            </div>
            <div class="col-md-8">
                <?php echo  $order['shipping_phone']; ?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="form-group">
            <div class="col-md-4">
                <label><?php echo __d('store',  'Country')?></label>
            </div>
            <div class="col-md-8">
                <?php echo  $order['shipping_country']; ?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="form-group">
            <div class="col-md-4">
                <label><?php echo __d('store',  'City')?></label>
            </div>
            <div class="col-md-8">
                <?php echo  $order['shipping_city']; ?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="form-group">
            <div class="col-md-4">
                <label><?php echo __d('store',  'Address')?></label>
            </div>
            <div class="col-md-8">
                <?php echo  $order['shipping_address']; ?>
            </div>
            <div class="clear"></div>
        </div>
        <div class="form-group">
            <div class="col-md-4">
                <label><?php echo __d('store',  'Postcode')?></label>
            </div>
            <div class="col-md-8">
                <?php echo  $order['shipping_postcode']; ?>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <?php if(!empty($order['order_comments'])):?>
    <div class="col-md-12 title st-mn-table" style="text-align:left">
        <h2 class="title">
            <?php echo  __d('store',  'Order notes') ?>
        </h2>
        <div class="form-group">
            <div class="col-md-12">
                <label><?php echo $order['order_comments']?></label>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <?php endif;?>
    <div class="col-md-12 title st-mn-table" style="text-align:left">
        <h2 class="title">
            <?php echo  __d('store',  'Order detail') ?>
            <?php if(!$is_app):?>
                <a class="print_order" data-id="<?php echo $order['id'];?>">
                    <i class="material-icons vmiddle">print</i> <?php echo  __d('store',  'Print') ?>
                </a>
            <?php endif;?>
        </h2>
        
    </div>
    <div class="col-md-6 st-mn-table">
        <div class="form-group">
            <div class="col-md-4">
                <?php echo __d('store', 'Payment');?>:
            </div>
            <div class="col-md-8">
                <?php echo $store_payment['name'];?>
                <?php echo $store_payment['is_online'] ? "(".$order['transaction_id'].")" : '';?>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-4">
                <?php echo __d('store', 'Status');?>:
            </div>
            <div class="col-md-8">
                <?php echo $this->Store->getOrderStatus($order['order_status']);?>
            </div>
        </div>
        <?php if($shipping != null): ?>
        <div class="form-group">
            <div class="col-md-4">
                <?php echo __d('store', 'Shipping');?>:
            </div>
            <div class="col-md-8">
                <?php echo $shipping['StoreShippingMethod']['name'];?>
            </div>
        </div>
        <?php endif;?>
    </div>
	<div class="clear"></div>
	<br/>
    <div class="div-detail-app">
        <div class="div-full-breabcrum">
            <div class="col-md-1">
                <div class="group-group">
                    <i class="text-app">
                        <?php echo  __d('store',  'Num') ?>
                    </i>
                </div>
            </div>
            <div class="col-md-2 ">
                <div class="group-group">
                    <i class="text-app">
                        <?php echo  __d('store',  'Product code') ?>
                    </i>
                </div>
            </div>
            <div class="col-md-4">
                <div class="group-group text-left">
                    <i class="text-app">
                        <?php echo  __d('store',  'Product name') ?>
                    </i>
                </div>
            </div>
            <div class="col-md-2">
                <div class="group-group">
                    <i class="text-app">
                        <?php echo  __d('store',  'Price') ?>
                    </i>
                </div>
            </div>
            <div class="col-md-1">
                <div class="group-group">
                    <i class="text-app">
                        <?php echo  __d('store',  'Quantity') ?>
                    </i>
                </div>
            </div>
            <div class="col-md-2">
                <div class="group-group">
                    <i class="text-app">
                        <?php echo  __d('store',  'Amount') ?>
                    </i>
                </div>
            </div>
        </div>
        <?php foreach($orderDetail as $k => $detail): 
            $product = $detail['product_id'] > 0 ? $this->Store->loadProductDetail($detail['product_id']) : array();
            $productImage = !empty($product['StoreProductImage'][0]) ? $product['StoreProductImage'][0] : null;
            $product = !empty($product['StoreProduct']) ? $product['StoreProduct'] : array();
        ?>
            <div class="div-detail-row">
                <div class="top-list-brb">
                    <?php echo __d('store', "Product");?>
                </div>
                <div class="col-xs-12 col-md-1">
                    <div class="group-group">
                        <i class="visible-sm visible-xs icon-app material-icons">business_center</i>
                        <i class="text-app"><?php echo ($k + 1);?></i>
                    </div>
                </div>
                <div class="col-xs-12 col-md-2">
                    <div class="group-group">
                        <i class="visible-sm visible-xs icon-app material-icons">business_center</i>
                        <i class="text-app"><?php echo $detail['product_code'];?></i>
                    </div>
                </div>
                <div class="col-xs-12 col-md-4 col-custom-4">
                    <div class="group-group group-group-name text-left">
                        <i class="visible-sm visible-xs icon-app material-icons">title</i>
                        <i class="text-app">
                            <a href="<?php echo $product['moo_href'];?>" class="group-group-img" title="<?php echo $product['name'];?>">
                                <img width="180" height="180" class="attachment-shop_thumbnail wp-post-image" src="<?php echo $this->Store->getProductImage($productImage, array('prefix' => PRODUCT_PHOTO_TINY_WIDTH));?>">                            
                            </a>
                            <a href="<?php echo !empty($product) ? $product['moo_href'] : 'javascript:void(0)';?>">
                                <?php echo  $detail['product_name']; ?>
                            </a>
                            <?php if(!empty($detail['attributes'])):?>
                            (<?php echo $detail['attributes'];?>)
                            <?php endif;?>
                        </i>
                    </div>
                </div>
                <div class="col-xs-12 col-md-2">
                    <div class="group-group">
                        <i class="visible-sm visible-xs icon-app material-icons">monetization_on</i>
                        <i class="text-app">
                            <?php 
                                $detail_amount = $store_payment['key_name'] == ORDER_GATEWAY_CREDIT ? $detail['amount_credit'] / $detail['quantity'] : $detail['price'];
                                echo $this->Store->formatMoney($detail_amount, $order['currency_position'], $order['currency_symbol'], $showMoneyType, false);
                            ?>
                        </i>
                    </div>
                </div>
                <div class="col-xs-12 col-md-1">
                    <div class="group-group">
                        <i class="visible-sm visible-xs icon-app material-icons">monetization_on</i>
                        <i class="text-app"><?php echo  $detail['quantity']; ?></i>
                    </div>
                </div>
                <div class="col-xs-12 col-md-2">
                    <div class="group-group">
                        <i class="visible-sm visible-xs icon-app material-icons">monetization_on</i>
                        <i class="text-app">
                            <?php 
                                $detail_amount = $store_payment['key_name'] == ORDER_GATEWAY_CREDIT ? $detail['amount_credit'] : $detail['amount'];
                                echo $this->Store->formatMoney($detail_amount, $order['currency_position'], $order['currency_symbol'], $showMoneyType, false);
                            ?>
                        </i>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if($order['shipping_fee'] > 0): ?>
            <div class="div-detail-row">
                <div class="col-md-10">
                    <div class="group-group text-right">
                        <i class="text-app">
                            <?php echo  __d('store',  'Shipping Fee'); ?>
                        </i>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="group-group">
                        <i class="text-app">
                            <?php 
                                $detail_amount = $store_payment['key_name'] == ORDER_GATEWAY_CREDIT ? $order['shipping_fee_credit'] : $order['shipping_fee'];
                                echo $this->Store->formatMoney($detail_amount, $order['currency_position'], $order['currency_symbol'], $showMoneyType, false);
                            ?>
                        </i>
                    </div>
                </div>
            </div>
        <?php endif;?>
        <div class="div-detail-row">
            <div class="col-md-10">
                <div class="group-group text-right">
                    <i class="text-app">
                        <?php echo  __d('store',  'Total'); ?>
                    </i>
                </div>
            </div>
            <div class="col-md-2">
                <div class="group-group">
                    <i class="text-app">
                        <?php 
                            $detail_amount = $store_payment['key_name'] == ORDER_GATEWAY_CREDIT ? $order['amount_credit'] : $order['amount'];
                            echo $this->Store->formatMoney($detail_amount, $order['currency_position'], $order['currency_symbol'], $showMoneyType, false);
                        ?>
                    </i>
                </div>
            </div>
        </div>
    </div>
</div>