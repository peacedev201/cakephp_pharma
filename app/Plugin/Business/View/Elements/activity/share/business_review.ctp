<?php 
    echo $this->Html->css(array(
        'Business.star-rating',
        'Business.business-widget'
    ));
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
    $mPhoto = MooCore::getInstance()->getModel('Photo_Photo');
    
    $business_review = $businessHelper->getReviewDetail($activity['Activity']['item_id'], $activity['Activity']['target_id']);
    $photos_total = count($business_review['Photo']);
    $business_review = $business_review['BusinessReview'];
    $photos = $mPhoto->find('all', array(
        'conditions' => array(
            'Photo.type' => 'Business_Review',
            'Photo.target_id' => $business_review['id']
        ),
        'limit' => 4
    ));
?>
<span class="review_star feed-wrote-review">
    <input readonly value="<?php echo $business_review['rating']; ?>" type="number" class="rating form-control hide" data-size="xs">
</span>
<?php echo $this->Business->viewMore($business_review['content'],null, null, null, true, array('no_replace_ssl' => 1)); ?>
<?php echo $this->Element('Business.review_photo_activity', array(
    'photos' => $photos,
    'photos_total' => $photos_total
));?>
