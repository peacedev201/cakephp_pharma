<?php 
    $productImages = $product['StoreProductImage'];
    $mainProductImage = !empty($productImages[0]) ? $productImages[0] : null;
    $product = $product['StoreProduct'];
?>
<form class="share-area" id="shareForm">
    <?php echo $this->Form->hidden('product_id', array(
        'value' => $product['id']
    ));?>
    <?php echo $this->Form->textarea('content', array(
        'placeholder' => __d('store',  'Write something about this product')
    ))?>
    <?php echo $this->Form->select('privacy', $privacy, array(
        'empty' => false
    ));?>
    <a onclick="jQuery.store.shareProduct('<?php echo __d('store', 'Successfully share');?>')" href="javascript:void(0)" class="button btn-share" id="share-button">
        <?php echo __d('store', 'Share');?>
    </a>
</form>