<?php 
    $productImages = $product['StoreProductImage'];
    $mainProductImage = !empty($productImages[0]) ? $productImages[0] : '';
    $product = $product['StoreProduct'];
?>
<div class="title-modal">
    <?php echo __d('store', 'Product');?>    
    <button data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <h3><?php echo __d('store', 'Product was added to cart');?></h3>
    <div class="product-wrapper">
        <div class="product-image">
            <img src="<?php echo $this->Store->getProductImage($mainProductImage, array('prefix' => PRODUCT_PHOTO_THUMB_WIDTH));?>" class="attachment-shop_thumbnail wp-post-image" alt="12_4" height="" width="">		
        </div>
        <div class="product-info">
            <h4><?php echo $product['name'];?></h4>
            <p class="price">
                <?php if($product['allow_promotion']):?>
                <span class="old-price">
                    <span class="amount"><?php echo $this->Store->formatMoney($product['old_price']);?></span>
                </span>
                <?php endif?>
                <span class="special-price">
                    <span class="amount"><?php echo $this->Store->formatMoney($product['new_price']);?></span>
                </span>
            </p>
        </div>
    </div>
    <div class="buttons">
        <a class="btn btn-action padding-button btn_view_cart" href="<?php echo STORE_URL;?>carts/">
            <?php echo __d('store', 'View Cart');?>
        </a>
    </div>
</div>