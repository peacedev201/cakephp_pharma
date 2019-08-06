<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooBehavior"], function($, mooBehavior) {
        mooBehavior.initMoreResults();
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery', 'mooBehavior'), 'object' => array('$', 'mooBehavior'))); ?>
mooBehavior.initMoreResults();
<?php $this->Html->scriptEnd(); ?>
<?php endif; ?>

<?php if ($products != null): ?>
    <ul class="blog-content-list">
        <?php
            $storeHelper = MooCore::getInstance()->getHelper('Store_Store');
            $mStore = MooCore::getInstance()->getModel('Store.Store');
            $currency = $mStore->loadDefaultGlobalCurrency();
            $currency = $currency['Currency'];
            foreach ($products as $product):
                $productImage = !empty($product['StoreProductImage'][0]) ? $product['StoreProductImage'][0] : null;
                $store = $product['Store'];
                $user = $product['User'];
                $product = $product['StoreProduct'];
        ?> 
        <li class="full_content p_m_10">
            <a href="<?php echo $product['moo_href']; ?>">
                <img width="140" class="img_wrapper2 user_list thumb_mobile" src="<?php echo $storeHelper->getProductImage($productImage, array('prefix' => PRODUCT_PHOTO_THUMB_WIDTH));?>">
            </a>
            <div class="blog-info">
                <a href="<?php echo $product['moo_href']; ?>" class="title">
                    <?php echo $product['name']; ?>                
                </a>
                <div class="extra_info">
                    <?php echo __d('store', 'Seller'); ?> <a href="<?php echo $user['moo_href']; ?>"><?php echo $store['name']; ?></a>
                    <?php echo $this->Moo->getTime($product['created'], Configure::read('core.date_format'), $utz )?>
                </div>
                <div class="blog-description-truncate">
                    <div class="price-box">
                            <?php if($product['allow_promotion']):?>
                        <span class="old-price">
                            <span class="amount"><?php echo $storeHelper->formatMoney($product['old_price'], null, $currency['symbol']);?></span>
                        </span>
                            <?php endif;?>
                        <span class="special-price">
                            <span class="amount"><?php echo $storeHelper->formatMoney($product['new_price'], null, $currency['symbol']);?></span>
                        </span>
                    </div>
                    <div class="product-desc">
                        <p><?php echo $product['brief'];?></p>
                    </div>
                    <div class="like-section">
                        <div class="like-action">
                            <a href="<?php echo $product['moo_href']; ?>">
                                <i class="material-icons">comment</i>
                                <span id="comment_count"><?php echo $product['comment_count']; ?></span>
                            </a>

                            <a class="" href="<?php echo $product['moo_href']; ?>">
                                <i class="material-icons">thumb_up</i>
                            </a>
                            <a title="<?php echo __d('store', 'People Who Like This'); ?>" href="<?php echo $product['moo_href']; ?>">
                                <span id="like_count"><?php echo $product['like_count']; ?></span>
                            </a> 
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
                <div class="extra_info">
                </div>
            </div>
        </li>
        <?php endforeach;?>
        <?php if (count($products) > 0 && !empty($more_url)): ?>
            <?php $this->Html->viewMore($more_url) ?>
        <?php endif; ?>
    </ul>   
<?php else:?> 
    <?php echo '<div class="clear" align="center">'.__d('store', 'No more results found').'</div>';?>
<?php endif;?>
