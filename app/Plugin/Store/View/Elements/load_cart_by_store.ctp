<?php if(!empty($cart['items'])): ?>
    <?php foreach($cart['items'] as $cart_item):
        $store = $cart_item['Store'];
        $products = !empty($cart_item['Products']) ? $cart_item['Products'] : null;
    ?>
    <div class="store_area">
        <div class="store_name <?php if(!empty($warning_store) && in_array($store['name'], $warning_store)):?>highlight<?php endif;?>">
        <?php echo __d('store', 'Store');?>: <?php echo $store['name'];?>
        <?php if(!empty($store['payments'])):?>
            <span>(<?php echo $this->Store->getPaymentName($store['payments']);?>)</span>
        <?php endif;?>
    </div>
        <?php if ($products != null): ?> 
            <?php
                foreach ($products as $key => $product):
                    $productImage = !empty($product['StoreProductImage'][0]) ? $product['StoreProductImage'][0] : null;
                    $product = $product['StoreProduct'];
            ?>
                <div class="div-detail-row cart_item" id="<?php echo $product['cart_id']; ?>" >
                    <div class="top-list-brb">
                        <?php echo __d('store', "Cart Listing"); ?>
                    </div>
                    <div class="col-xs-12 col-md-5">
                        <div class="group-group group-group-name text-left">
                            <i class="visible-sm visible-xs icon-app material-icons">title</i>
                            <i class="text-app">
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
                                <a href="<?php echo $product['moo_href']; ?>" class="group-group-img">
                                    <img width="45" height="45" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="attachment-shop_thumbnail wp-post-image" src="<?php echo $this->Store->getProductImage($productImage, array('prefix' => PRODUCT_PHOTO_TINY_WIDTH)); ?>">
                                </a>
                                <a class="item_name" href="<?php echo $product['moo_href']; ?>">
                                        <?php echo $product['name']; ?>
                                </a>
                                <?php if ($product['attributes'] != null): ?>
                                <div class="variation">
                                    <div class="variation-Size">
                                        <?php echo __d('store', 'Attributes'); ?>:
                                        &nbsp;<?php echo implode(', ', $product['attributes']); ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </i>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-1">
                        <div class="group-group product-price">
                            <i class="visible-sm visible-xs icon-app material-icons">monetization_on</i>
                            <i class="text-app product-quantity">
                                <?php echo $this->Store->loadProductType($product['product_type']); ?>
                            </i>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-2">
                        <div class="group-group product-price store-price-enhance">
                            <i class="visible-sm visible-xs icon-app material-icons">monetization_on</i>
                            <i class="text-app product-quantity">
                                <span class="amount" data-price="<?php echo $product['new_price']; ?>" data-id="<?php echo $store['id'].'_'.$key; ?>">
                                    <?php echo $this->Store->formatMoney($product['new_price']); ?>
                                </span>	
                            </i>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-1">
                        <div class="group-group">
                            <i class="visible-sm visible-xs icon-app material-icons">keyboard_hide</i>
                            <i class="text-app">
                                <div class="quantity product-quantity">
                                    <?php if($product['product_type'] == STORE_PRODUCT_TYPE_REGULAR):?>
                                        <input type="number" size="2" id="quantity<?php echo $store['id'].'_'.$key; ?>" class="input-text qty text quantity_cart" title="Qty" value="<?php echo $product['quantity']; ?>" name="cart[<?php echo $product['cart_id']; ?>]" min="1" max="99" step="1">
                                    <?php else:?>
                                        <input id="quantity<?php echo $store['id'].'_'.$key; ?>" class="input-text qty text quantity_cart" value="<?php echo $product['quantity']; ?>" name="cart[<?php echo $product['cart_id']; ?>]" readonly="readonly">
                                    <?php endif;?>
                                </div>
                            </i>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-2">
                        <div class="group-group store-price-enhance">
                            <i class="visible-sm visible-xs icon-app material-icons">receipt</i>
                            <i class="text-app product-subtotal text-right">
                                <span class="amount" id="subtotal<?php echo $store['id'].'_'.$key; ?>"></span>
                            </i>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-1 remove-btn">


                            <a title="<?php echo __d('store', 'Remove this item'); ?>" class="remove remove_cart_product" href="javascript:void(0)" data-store_id="<?php echo $product['store_id']; ?>" data-cart_id="<?php echo $product['cart_id']; ?>">
                                    <i class="icon-app material-icons hidden-xs hidden-sm">close</i>
                                </a>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php endforeach;?>
    <div class="div-detail-row cart_item cart_total" >
        <div class="col-xs-12 col-md-10 text-right">
            <div class="group-group ">
                <i class="text-app subtotal">
                    <?php echo __d('store', 'Total'); ?>
                </i>
            </div>
        </div>
        <div class="col-xs-12 col-md-2 text-center" style="text-align: left">
            <div class="group-group store-price-enhance ">
                <i class="visible-sm visible-xs icon-app material-icons">monetization_on</i>
                <i class="text-app">
                    <span class="amount" id="subtotal"></span>
                </i>
            </div>
        </div>
    </div>
    <div class="buttons-cart">
        <a href="<?php echo $this->request->base; ?>/stores" class="button continue">
            <?php echo __d('store', 'Continue Shopping'); ?>
        </a>
        <a class="checkout-button button alt wc-forward" href="<?php echo STORE_URL.'orders/checkout/'.$store_id;?>">
            <?php echo __d('store', 'Proceed to Checkout'); ?>
        </a>
        <a href="javascript:void(0)" class="button alt" id="update_cart">
            <?php echo __d('store', 'Update Quantity'); ?>
        </a>
        <a href="javascript:void(0)" class="button" id="clear_all_cart">
            <?php echo __d('store', 'Clear Cart'); ?>
        </a>
    </div>
<?php endif;?>