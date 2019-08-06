<?php
    $product = $this->requestAction('stores/share_product_content/'.$object['StoreProduct']['id']);
    $productImages = $product['StoreProductImage'];
    $mainProductImage = !empty($productImages[0]) ? $productImages[0] : null;
    $store = $product['Store'];
    $product = $product['StoreProduct'];
    $storeHelper = MooCore::getInstance()->getHelper('Store_Store');
?>

<div class="activity_item">

    <div class="activity_left">
        <a href="<?php echo $product['moo_href']?>" target="_blank">
        <img width="150" class="thum_activity" src="<?php echo $storeHelper->getProductImage($mainProductImage, array('prefix' => PRODUCT_PHOTO_THUMB_WIDTH));?>" />
        </a>
    </div>

    <div class="activity_right ">
        <div class="activity_header">
            <a href="<?php echo $product['moo_href']?>" target="_blank">
                <?php echo $product['name']?>
                
            </a>
        </div>
        <?php echo __d('store', 'Seller');?>: <?php echo $store['name']?><br/>
        <?php echo __d('store', 'Price');?>: <?php echo $this->Store->formatMoney($product['new_price']);?>
        <?php echo $this->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $product['brief'])), 200, array('exact' => false)) ?>
      </div>
    <div class="clear"></div>
</div>