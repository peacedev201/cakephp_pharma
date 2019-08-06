<?php
    $mStoreProduct = MooCore::getInstance()->getModel('Store.StoreProduct');
    $product = $mStoreProduct->loadOnlyProduct($activity['Activity']['target_id']);
    $product = $product['StoreProduct'];
?>
<?php echo __d('store', 'wrote a review for');?>
<a href="<?php echo $product['moo_href']."?review=".$activity['Activity']['item_id'].'&tab=3';?>">
    <?php echo $product['name'];?>
</a>