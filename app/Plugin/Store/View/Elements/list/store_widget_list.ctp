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
    
<aside class="left_widget_box widget product_plugin widget_top_rated_products">
    <div class="product-you-may-like">
        <ul class="product_list_widget">
            <?php foreach($stores as $store):
                $store = $store['Store'];
            ?>
            <li>
                <a class="product-title" title="<?php echo $store['name'];?>" href="<?php echo $store['moo_href'];?>">
                    <?php if($store['featured']):?>
                    <div class="featured_product_small">
                        <span><?php echo __d('store', 'Featured');?></span>
                    </div>
                    <?php endif;?>
                    <img width="180" height="180" alt="<?php echo $store['name'];?>" class="attachment-shop_thumbnail wp-post-image" src="<?php echo $this->Store->getStoreImage($store, array('prefix' => STORE_PHOTO_TINY_WIDTH));?>">		
                </a>
                <div class="content-random-product">
                    <a title="<?php echo $store['name'];?>" href="<?php echo $store['moo_href'];?>">
                        <p>
                            <?php echo  $this->Text->truncate(strip_tags(str_replace(array('<br>','&nbsp;'), array(' ',''), $store['name'])), 40, array('eclipse' => ''));?>
                        </p>
                    </a>
                    <div class="price">
                        <?php echo $store['email'];?>
                    </div>
                </div>
                <div class="clear"></div>
            </li>
            <?php endforeach;?> 
        </ul>
    </div>
</aside>