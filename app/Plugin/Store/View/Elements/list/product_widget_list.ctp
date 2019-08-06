<?php
    echo $this->Html->css(array(
        'Store.store_widget',
        'Store.star-rating'
    ), array('block' => 'css', 'minify'=>false));
?>
<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'store_store'), 
    'object' => array('$', 'store_store')
));?>
    store_store.initReviewStar();
<?php $this->Html->scriptEnd(); ?> 
    
<aside class="left_widget_box widget store_plugin widget_top_rated_products" id="store_plugin_top_rated_products-2">
    <div class="store-you-may-like">
        <ul class="product_list_widget">
            <?php foreach($products as $product):
                $productImage = !empty($product['StoreProductImage'][0]) ? $product['StoreProductImage'][0] : null;
                $product = $product['StoreProduct'];
            ?>
            <li>
                <a class="product-title" title="<?php echo $product['name'];?>" href="<?php echo $product['moo_href'];?>">
                    <?php if($product['featured']):?>
                    <div class="featured_product_small">
                        <span><?php echo __d('store', 'Featured');?></span>
                    </div>
                    <?php endif;?>
                    <img width="180" height="180" alt="<?php echo $product['name'];?>" class="attachment-shop_thumbnail wp-post-image" src="<?php echo $this->Store->getProductImage($productImage, array('prefix' => PRODUCT_PHOTO_TINY_WIDTH));?>">		
                </a>
                <div class="content-random-product">
                    <a title="<?php echo $product['name'];?>" href="<?php echo $product['moo_href'];?>">
                        <p>
                            <?php echo  $this->Text->truncate(strip_tags(str_replace(array('<br>','&nbsp;'), array(' ',''), $product['name'])), 40, array('eclipse' => ''));?>
                        </p>
                    </a>
                    <span class="review_star">
                        <input readonly value="<?php echo $product['rating']; ?>" type="number" class="rating form-control hide">
                    </span>
                    <div class="price">
                        <?php if($product['allow_promotion']):?>
                            <span class="old-price">
                                <span class="amount"><?php echo $this->Store->formatMoney($product['old_price']);?></span>
                            </span>
                        <?php endif;?>
                        <span class="special-price">
                            <span class="amount"><?php echo $this->Store->formatMoney($product['new_price']);?></span>
                        </span>
                    </div>
                </div>
            </li>
            <?php endforeach;?> 
        </ul>
    </div>
</aside>