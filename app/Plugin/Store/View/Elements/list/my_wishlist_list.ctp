<?php if($products != null):?>
    <?php 
    $last_key = array_keys($products);
    $last_key = end($last_key);
    $i = 0;
    foreach($products as $product):
        $productImage = !empty($product['StoreProductImage'][0]) ? $product['StoreProductImage'][0] : null;
        $store = $product['Store'];
        $product = $product['StoreProduct'];
    ?> 
    <div class="div-detail-row <?php if ($i == $last_key): ?> add-border-bottom <?php endif;$i++; ?>" id="product-wishlist-<?php echo $product['id'];?>" >
        <div class="top-list-brb">
                    <?php echo __d('store', "Wishlist Listing"); ?>
        </div>

        <div class="col-xs-12 col-md-1 remove-btn">
            <a title="<?php echo __d('store', 'Remove this product');?>" class="remove remove_product_wishlist" href="javascript:void(0)" onclick="" data-id="<?php echo $product['id'];?>">
                <i class="icon-app material-icons">close</i>
            </a>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="group-group group-group-name text-left">
                <i class="visible-sm visible-xs icon-app material-icons">title</i>
                <i class="text-app">
                    <a href="<?php echo $product['moo_href'];?>" class="group-group-img" title="<?php echo $product['name'];?>">
                        <img width="180" height="180" alt="12_4" class="attachment-shop_thumbnail wp-post-image" src="<?php echo $this->Store->getProductImage($productImage, array('prefix' => PRODUCT_PHOTO_TINY_WIDTH));?>">                            
                    </a>
                    <a href="<?php echo $product['moo_href'];?>">
                        <?php echo $product['name'];?>
                    </a>
                </i>
            </div>
        </div>
        <div class="col-xs-12 col-md-3">
            <div class="group-group store-price-enhance">
                <i class="visible-sm visible-xs icon-app material-icons">monetization_on</i>
                <i class="text-app product-quantity">
                    <?php if($product['allow_promotion']):?>
                    <span class="old-price">
                        <span class="amount"><?php echo $this->Store->formatMoney($product['old_price']);?></span>
                    </span>
                <?php endif;?>
                    <span class="special-price">
                        <span class="amount"><?php echo $this->Store->formatMoney($product['new_price']);?></span>
                    </span>      
                </i>
            </div>
        </div>
        <div class="col-xs-12 col-md-2">
            <div class="group-group ">
                <i class="visible-sm visible-xs icon-app material-icons">remove_shopping_cart</i>
                <i class="text-app product-subtotal">
                    <span class="wishlist-in-stock">
                    <?php if($product['out_of_stock']):?>
                        <?php echo __d('store', 'Out of stock');?>
                    <?php else:?> 
                        <?php echo __d('store', 'In Stock');?>
                    <?php endif;?>
                    </span> 
                </i>
            </div>
        </div>
    </div>
    <?php endforeach;?>
<?php else:?>
    <div style="padding: 5px;text-align: center;">
        <?php echo __d('store', 'No more results found');?>
    </div>
<?php endif;?>
<?php if($this->Paginator->hasPage(2)):?>
    <div class="toolbar tb-bottom">
        <p class="store_plugin-result-count">
            <?php echo $this->Paginator->counter(sprintf(__d('store', 'Showing %s–%s of %s results'), '{:start}', '{:end}', '{:count}'));?>
        </p>
        <nav class="store_plugin-pagination">
            <ul class="page-numbers">
                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->first(__d('store', 'First'), array(
                    'class' => 'page-numbers previous wishlist_paging', 
                    'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('store', 'Previous'), array(
                    'class' => 'page-numbers previous wishlist_paging', 
                    'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                <?php echo $this->Paginator->numbers(array(
                    'class' => 'page-numbers wishlist_paging', 
                    'tag' => 'li', 
                    'separator' => '')); ?>
                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next(__d('store', 'Next'), array(
                    'class' => 'page-numbers next wishlist_paging', 
                    'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->last(__d('store', 'Last'), array(
                    'class' => 'page-numbers next wishlist_paging', 
                    'tag' => 'li'), null, array('class' => 'disabled')) : '';?>
            </ul>
        </nav>
        <div class="clearfix"></div>
    </div>
<?php endif;?>