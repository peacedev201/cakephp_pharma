<?php 
    $mStoreReview = MooCore::getInstance()->getModel('Store.StoreReview');
    $mStoreProduct = MooCore::getInstance()->getModel('Store.StoreProduct');
    
    $review = $mStoreReview->getReviewDetail($activity['Activity']['item_id'], $activity['Activity']['target_id']);
    $product = $mStoreProduct->loadOnlyProduct($activity['Activity']['target_id']);

    echo $this->Element('Store.activity_review_item', array(
        'review' => $review,
        'product' => !empty($product) ? $product : null,
        'is_activity_view' => true,
        'is_share_popup' => true
    ));
?>
