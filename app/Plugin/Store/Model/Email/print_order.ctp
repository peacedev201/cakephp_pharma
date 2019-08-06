<?php 
    $storeHelper = MooCore::getInstance()->getHelper('Store_Store');
    $mooHelper = MooCore::getInstance()->getHelper('Core_Moo');
    $store = $order['Store'];
    $order_details = $order['StoreOrderDetail'];
	$store_payment = $order['StorePayment'];
    $order = $order['StoreOrder'];
    $logo = $mooHelper->logo();
    $logo = str_replace($this->webroot, '', $logo);
    $shipping = $order['store_shipping_id'] > 0 ? $storeHelper->getShippingDetail($order['store_shipping_id'], $store['id']) : null;
    $showMoneyType = $store_payment['key_name'] == ORDER_GATEWAY_CREDIT ? STORE_SHOW_MONEY_TYPE_CREDIT : STORE_SHOW_MONEY_TYPE_NORMAL;
?>
<table width="100%" bgcolor="#f6f8f1" border="0" cellpadding="0" cellspacing="0">
    <tr><td style="height:20px;"></td>
    </tr>
    <tr><td style="height:35px;"></td></tr>
    <tr>
        <td>
            <table class="content" align="center" cellpadding="0" cellspacing="0" border="0">
                <tbody style="width:100%;display:block;">
                    <tr style="width:100%;display:block;">
                        <td style="width:25px;"></td>
                        <td style="width:700px;">
                            <table align="none" cellpadding="0" cellspacing="0" border="0">
                                <tbody style="width:100%;display:block;">
                                    <tr style="width: 100%; display: block;">
                                        <td >
                                            <table align="none" cellpadding="0" cellspacing="0" border="0">
                                                <tr style="width:100%;display:block;">
                                                    <td style="font-size: 13px; color: rgb(101, 100, 97); font-family: sans-serif;width: 80px;">
                                                        <?php echo __d('store', 'Order code');?>
                                                    </td>
                                                    <td style="font-size: 13px; color: rgb(101, 100, 97); font-family: sans-serif;">
                                                        <?php echo $order['order_code'];?>
                                                    </td>
                                                </tr>
                                                <tr style="width:100%;display:block;">
                                                    <td style="font-size: 13px; color: rgb(101, 100, 97); font-family: sans-serif;width: 80px;">
                                                        <?php echo __d('store', 'Payment');?>
                                                    </td>
                                                    <td style="font-size: 13px; color: rgb(101, 100, 97); font-family: sans-serif;">
                                                        <?php echo $store_payment['name'];?> <?php if($store_payment['is_online']):?>(<?php echo $order['transaction_id'];?>)<?php endif;?>
                                                    </td>
                                                </tr>
                                                <tr style="width:100%;display:block;">
                                                    <td style="font-size: 13px; color: rgb(101, 100, 97); font-family: sans-serif;width: 80px;">
                                                        <?php echo __d('store', 'Shipping');?>
                                                    </td>
                                                    <td style="font-size: 13px; color: rgb(101, 100, 97); font-family: sans-serif;">
                                                        <?php echo $shipping['StoreShippingMethod']['name'];?>
                                                    </td>
                                                </tr>
                                                <tr style="width:100%;display:block;">
                                                    <td style="font-size: 13px; color: rgb(101, 100, 97); font-family: sans-serif;width: 80px;">
                                                        <?php echo __d('store', 'Create date');?>
                                                    </td>
                                                    <td style="font-size: 13px; color: rgb(101, 100, 97); font-family: sans-serif;">
                                                        <?php echo date('M d Y H:i a', strtotime($order['created']));?>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr style="width: 100%; display: block; height: 20px;"></tr>
                                    <tr style="width:100%;display:block;">
                                        <td style="width:350px;font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;border: 1px solid #e5e5e5;">
                                            <table width="275" cellspacing="0" cellpadding="0" border="0" align="none">
                                                <tbody style="width:100%;display:block;">
                                                    <tr style="width:100%;display:block;">
                                                        <td style="font-size: 14px; color: rgb(101, 100, 97); font-weight: 700; font-family: sans-serif;border-bottom: 1px solid #e5e5e5;padding:5px;"  width="275"><?php echo __d('store', 'Billing information');?>:</td>
                                                    </tr>
                                                    <tr style="width:100%;display:block;">
                                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;padding:5px;"><?php echo __d('store', 'Name');?>: </td>
                                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;">
                                                            <?php echo $order['billing_first_name'];?> <?php echo $order['billing_last_name'];?>
                                                        </td>
                                                    </tr>
                                                    <tr style="width:100%;display:block;">
                                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;padding:5px;"><?php echo __d('store', 'Email');?>: </td>
                                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;">
                                                            <?php echo $order['billing_email'];?>
                                                        </td>
                                                    </tr>
                                                    <tr style="width:100%;display:block;">
                                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;padding:5px;"><?php echo __d('store', 'Phone');?>: </td>
                                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;">
                                                            <?php echo $order['billing_phone'];?>
                                                        </td>
                                                    </tr>
                                                    <tr style="width:100%;display:block;">
                                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;padding:5px;"><?php echo __d('store', 'Address');?>: </td>
                                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;">
                                                            <?php echo $order['billing_address'];?>
                                                        </td>
                                                    </tr>
                                                    <tr style="width:100%;display:block;">
                                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;padding:5px;"><?php echo __d('store', 'Country');?>: </td>
                                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;">
                                                            <?php echo $order['billing_country'];?>
                                                        </td>
                                                    </tr>
                                                    <tr style="width:100%;display:block;">
                                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;padding:5px;"><?php echo __d('store', 'City');?>: </td>
                                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;">
                                                            <?php echo $order['billing_city'];?>
                                                        </td>
                                                    </tr>
                                                    <tr style="width:100%;display:block;">
                                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;padding:5px;"><?php echo __d('store', 'Postcode');?>: </td>
                                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;">
                                                            <?php echo $order['billing_postcode'];?>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td style="color: rgb(101, 100, 97);font-family: sans-serif;font-size: 13px;font-weight: 400;vertical-align: top;width: 350px;border: 1px solid #e5e5e5;">
                                            <table align="none" cellpadding="0" cellspacing="0" border="0">
                                                <tbody style="width:100%;display:block;">
                                                    <tr style="width:100%;display:block;">
                                                        <td style="font-size: 14px; color: rgb(101, 100, 97); font-weight: 700; font-family: sans-serif;border-bottom: 1px solid #e5e5e5;padding:5px;"  width="275"><?php echo __d('store', 'Shipping information');?>:</td>
                                                    </tr>
                                                    <tr style="width:100%;display:block;">
                                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;padding:5px;"><?php echo __d('store', 'Name');?>: </td>
                                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;">
                                                            <?php echo $order['shipping_first_name'];?> <?php echo $order['shipping_last_name'];?>
                                                        </td>
                                                    </tr>
                                                    <tr style="width:100%;display:block;">
                                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;padding:5px;"><?php echo __d('store', 'Email');?>: </td>
                                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;">
                                                            <?php echo $order['shipping_email'];?>
                                                        </td>
                                                    </tr>
                                                    <tr style="width:100%;display:block;">
                                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;padding:5px;"><?php echo __d('store', 'Phone');?>: </td>
                                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;">
                                                            <?php echo $order['shipping_phone'];?>
                                                        </td>
                                                    </tr>
                                                    <tr style="width:100%;display:block;">
                                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;padding:5px;"><?php echo __d('store', 'Address');?>: </td>
                                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;">
                                                            <?php echo $order['shipping_address'];?>
                                                        </td>
                                                    </tr>
                                                    <tr style="width:100%;display:block;">
                                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;padding:5px;"><?php echo __d('store', 'Country');?>: </td>
                                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;">
                                                            <?php echo $order['shipping_country'];?>
                                                        </td>
                                                    </tr>
                                                    <tr style="width:100%;display:block;">
                                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;padding:5px;"><?php echo __d('store', 'City');?>: </td>
                                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;">
                                                            <?php echo $order['shipping_city'];?>
                                                        </td>
                                                    </tr>
                                                    <tr style="width:100%;display:block;">
                                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;padding:5px;"><?php echo __d('store', 'Postcode');?>: </td>
                                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;">
                                                            <?php echo $order['shipping_postcode'];?>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr style="width: 100%; display: block; height: 20px;"></tr>
                                    <?php if(!empty($order['order_comments'])):?>
                                    <tr style="width:100%;display:block;">
                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 700; font-family: sans-serif;"><?php echo __d('store', 'Order notes');?>:</td>
                                    </tr>
                                    <tr style="width:100%;display:block;">
                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-family: sans-serif;">
                                            <?php echo $order['order_comments'];?>
                                        </td>
                                    </tr>
                                    <tr style="width: 100%; display: block; height: 20px;"></tr>
                                    <?php endif;?>
                                    <tr style="width:100%;display:block;">
                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 700; font-family: sans-serif;"><?php echo __d('store', 'Order detail');?>:</td>
                                    </tr>
                                    <tr style="width: 100%; display: block; height: 10px;"></tr>
                                    <tr style="width:100%;display:block;">
                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 700; font-family: sans-serif;">
                                            <table align="center" cellpadding="0" cellspacing="0" border="0" width="700px" style="table-layout: fixed;word-wrap: break-word;">
                                                <tbody style="width:100%;display:block;">
                                                    <tr>
                                                        <td style="text-align: left;width:5%;font-size: 13px; color: rgb(101, 100, 97); font-weight: 700; font-family: sans-serif;border:1px solid #e5e5e5;padding:5px;"></td>
                                                        <td style="text-align: left;width:53%;font-size: 13px; color: rgb(101, 100, 97); font-weight: 700; font-family: sans-serif;border:1px solid #e5e5e5;padding:5px;border-left:none;"><?php echo __d('store', 'Product Name');?></td>
                                                        <td style="text-align: right;width:14%;font-size: 13px; color: rgb(101, 100, 97); font-weight: 700; font-family: sans-serif;border:1px solid #e5e5e5;padding:5px;border-left:none;"><?php echo __d('store', 'Price');?></td>
                                                        <td style="text-align: center;width:14%;font-size: 13px; color: rgb(101, 100, 97); font-weight: 700; font-family: sans-serif;border:1px solid #e5e5e5;padding:5px;border-left:none;"><?php echo __d('store', 'Quantity');?></td>
                                                        <td style="text-align: right;width:14%;font-size: 13px; color: rgb(101, 100, 97); font-weight: 700; font-family: sans-serif;border:1px solid #e5e5e5;padding:5px;border-left:none;"><?php echo __d('store', 'Amount');?></td>
                                                    </tr>
                                                    <?php foreach($order_details as $k => $order_detail):?>
                                                        <tr>
                                                            <td style="text-align: center;font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;border:1px solid #e5e5e5;padding:5px;border-top:none;">
                                                                <?php echo ($k + 1)?>
                                                            </td>
                                                            <td style="text-align: left;font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;border:1px solid #e5e5e5;padding:5px;border-left:none;">
                                                                <?php echo $order_detail['product_name'];?> <?php if(!empty($order_detail['attributes'])):?>(<?php echo $order_detail['attributes'];?>)<?php endif;?>
                                                            </td>
                                                            <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;border:1px solid #e5e5e5;padding:5px;border-top:none;border-left:none;text-align: right">
                                                                <?php 
                                                                    $detail_amount = $store_payment['key_name'] == ORDER_GATEWAY_CREDIT ? $order_detail['amount_credit'] / $order_detail['quantity'] : $order_detail['price'];
                                                                    echo $storeHelper->formatMoney($detail_amount, $order['currency_position'], $order['currency_symbol'], $showMoneyType, false);
                                                                ?>
                                                            </td>
                                                            <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;border:1px solid #e5e5e5;padding:5px;border-top:none;border-left:none;text-align: center">
                                                                <?php echo $order_detail['quantity'];?>
                                                            </td>
                                                            <td style="text-align: right;font-size: 13px; color: rgb(101, 100, 97); font-weight: 400; font-family: sans-serif;border:1px solid #e5e5e5;padding:5px;border-top:none;border-left:none;">
                                                                <?php 
                                                                    $detail_amount = $store_payment['key_name'] == ORDER_GATEWAY_CREDIT ? $order_detail['amount_credit'] : $order_detail['amount'];
                                                                    echo $storeHelper->formatMoney($detail_amount, $order['currency_position'], $order['currency_symbol'], $showMoneyType, false);
                                                                ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach;?>
                                                    <?php if($order['shipping_fee'] > 0):?>
                                                        <tr>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 700; font-family: sans-serif;text-align:right;padding:5px;"><?php echo __d('store', 'Shipping Fee');?></td>
                                                            <td style="text-align: center;width:100px;font-size: 13px; color: rgb(101, 100, 97); font-weight: 700; font-family: sans-serif;padding-top:5px;text-align: right;padding:5px;">
                                                                <?php 
                                                                    $detail_amount = $store_payment['key_name'] == ORDER_GATEWAY_CREDIT ? $order['shipping_fee_credit'] : $order['shipping_fee'];
                                                                    echo $storeHelper->formatMoney($detail_amount, $order['currency_position'], $order['currency_symbol'], $showMoneyType, false);
                                                                ?>
                                                            </td>
                                                        </tr>
                                                    <?php endif;?>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td style="width:450px;font-size: 13px; color: rgb(101, 100, 97); font-weight: 700; font-family: sans-serif;text-align:right;padding:5px;"><?php echo __d('store', 'Total');?></td>
                                                        <td style="text-align: center;width:100px;font-size: 13px; color: rgb(101, 100, 97); font-weight: 700; font-family: sans-serif;padding-top:5px;text-align: right;padding:5px;">
                                                            <?php 
                                                                $detail_amount = $store_payment['key_name'] == ORDER_GATEWAY_CREDIT ? $order['amount_credit'] : $order['amount'];
                                                                echo $storeHelper->formatMoney($detail_amount, $order['currency_position'], $order['currency_symbol'], $showMoneyType, false);
                                                            ?>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr style="width:100%;display:block;">
                                        <td style="font-size: 13px; color: rgb(101, 100, 97); font-weight: 700; font-family: sans-serif;">
                                            <?php echo __d('store', 'Seller information');?>:
                                        </td>
                                    </tr>
                                    <tr style="width:100%;display:block">
                                        <td>
                                            <table cellspacing="0" cellpadding="0" border="0" align="none">
                                                <tr style="width:100%;display:block">
                                                    <td style="font-size:13px;color:rgb(101,100,97);font-family:sans-serif;width:80px">
                                                        <?php echo __d('store', 'Name');?>                                                    
                                                    </td>
                                                    <td style="font-size:13px;color:rgb(101,100,97);font-family:sans-serif">
                                                        <?php echo $store['name'];?>   
                                                    </td>
                                                </tr>
                                                <tr style="width:100%;display:block">
                                                    <td style="font-size:13px;color:rgb(101,100,97);font-family:sans-serif;width:80px">
                                                        <?php echo __d('store', 'Email');?>                                                    
                                                    </td>
                                                    <td style="font-size:13px;color:rgb(101,100,97);font-family:sans-serif">
                                                        <?php echo $store['email'];?>                                                     
                                                    </td>
                                                </tr>
                                                <tr style="width:100%;display:block">
                                                    <td style="font-size:13px;color:rgb(101,100,97);font-family:sans-serif;width:80px">
                                                        <?php echo __d('store', 'Address');?>                                            
                                                    </td>
                                                    <td style="font-size:13px;color:rgb(101,100,97);font-family:sans-serif">
                                                        <?php echo $store['address'];?>                                               
                                                    </td>
                                                </tr>
                                                <tr style="width:100%;display:block">
                                                    <td style="font-size:13px;color:rgb(101,100,97);font-family:sans-serif;width:80px">
                                                        <?php echo __d('store', 'Phone');?>                                            
                                                    </td>
                                                    <td style="font-size:13px;color:rgb(101,100,97);font-family:sans-serif">
                                                        <?php echo $store['phone'];?>                                          
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td style="width:25px;"></td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
</table>
