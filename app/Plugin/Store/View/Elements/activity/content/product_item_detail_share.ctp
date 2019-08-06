<?php
    $product_id = $activity['Activity']['item_id'];
    if($product_id < 1)
    {
        //$mActivity = MooCore::getInstance()->getModel('Activity');
        //$parentFeed = $mActivity->findById($activity['Activity']['parent_id']);
        //$product_id = $parentFeed['Activity']['item_id'];
        $product_id = $activity['Activity']['parent_id'];
    }
    $product = $this->requestAction('stores/share_product_content/'.$product_id);
    $productImages = $product['StoreProductImage'];
    $mainProductImage = !empty($productImages[0]) ? $productImages[0] : null;
    $store = $product['Store'];
    $product = $product['StoreProduct'];
    $storeHelper = MooCore::getInstance()->getHelper('Store_Store');
?>

<?php if (!empty($activity['Activity']['content'])): ?>
<div class="comment_message">
<?php echo $this->viewMore(h($activity['Activity']['content']),null, null, null, true, array('no_replace_ssl' => 1)); ?>
</div>
<?php endif; ?>

<div class="activity_item">

    <div class="activity_left">
        <a href="<?php echo $product['moo_href']?>">
        <img width="150" class="thum_activity" src="<?php echo $storeHelper->getProductImage($mainProductImage, array('prefix' => PRODUCT_PHOTO_THUMB_WIDTH));?>" />
        </a>
    </div>

    <div class="activity_right ">
        <div class="activity_header">
            <a href="<?php echo $product['moo_href']?>">
                <?php echo $product['name']?>
                
            </a>
        </div>
        <?php echo __d('store', 'Seller');?>: <?php echo $store['name']?><br/>
        <?php echo __d('store', 'Price');?>: <?php echo $this->Store->formatMoney($product['new_price']);?><br/>
        <?php echo $this->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $product['brief'])), 200, array('exact' => false)) ?>
      </div>
    <div class="clear"></div>
</div>