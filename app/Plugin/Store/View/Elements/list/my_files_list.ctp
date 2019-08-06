<?php if($products != null):?>
    <?php 
    foreach($products as $k => $product):
        $productImage = !empty($product['StoreProductImage'][0]) ? $product['StoreProductImage'][0] : null;
        $product = $product['StoreProduct'];
    ?> 
        <div class="div-detail-row <?php if ($k == count($products) - 1): ?> add-border-bottom <?php endif;?>" id="product-wishlist-<?php echo $product['id'];?>" >
        <div class="top-list-brb">
            <?php echo __d('store', "My Files Listing"); ?>
        </div>
        <div class="col-xs-12 col-md-9">
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
            <div class="group-group ">
                <?php if($product['product_type'] == STORE_PRODUCT_TYPE_DIGITAL && $this->Store->isBoughtDigitalProduct($product['id'])):?> 
                    <a class="button add_to_cart_button product_type_simple" href="<?php echo $this->request->base.'/stores/products/download_product/'.$product['id'];?>">
                        <?php echo __d('store', 'Download');?>
                    </a>
                <?php elseif($product['product_type'] == STORE_PRODUCT_TYPE_LINK && $this->Store->isBoughtDigitalProduct($product['id'])):?>  
                    <a class="button add_to_cart_button product_type_simple" href="<?php echo $this->request->base.'/stores/products/download_product/'.$product['id'];?>" target="_blank">
                        <?php echo __d('store', 'View Link');?>
                    </a>
                <?php endif;?>
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