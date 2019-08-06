<?php 
    echo $this->Html->css(array(
        'Business.star-rating',
        'Business.business-widget'
    ));
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
    $mActivity = MooCore::getInstance()->getModel('Activity');
    $mPhoto = MooCore::getInstance()->getModel('Photo_Photo');
    
    $parent_activity = $mActivity->findById($activity['Activity']['parent_id']);
    $business_review = $businessHelper->getReviewDetail($parent_activity['Activity']['item_id'], $parent_activity['Activity']['target_id']);
    $photos_total = count($business_review['Photo']);
    $business_review = $business_review['BusinessReview'];
    $business = $businessHelper->getOnlyBusiness($parent_activity['Activity']['target_id']);
    $photos = $mPhoto->find('all', array(
        'conditions' => array(
            'Photo.type' => 'Business_Review',
            'Photo.target_id' => $business_review['id']
        ),
        'limit' => 4
    ));
    
?>
<div class="comment_message">
    <?php echo $this->Business->viewMore(h($activity['Activity']['content']), null, null, null, true, array('no_replace_ssl' => 1)); ?>
    <?php if(!empty($activity['UserTagging']['users_taggings'])) $this->MooPeople->with($activity['UserTagging']['id'], $activity['UserTagging']['users_taggings']); ?>
    <div class="share-content">
        <div class="activity_feed_content">
            <div class="activity_text">
                <?php echo $this->Moo->getName($parent_activity['User']) ?>
                <?php echo __d('business', 'wrote a review for');?>
                <a href="<?php echo $business['Business']['parent_id'] > 0 ? $business['Business']['moo_href'] : $business['Business']['moo_hrefreview']."?review=".$parent_activity['Activity']['item_id'];?>">
                    <?php echo $business['Business']['name'];?>
                </a>
            </div>
            <div class="parent_feed_time">
                <span class="date"><?php echo $this->Moo->getTime($parent_activity['Activity']['created'], Configure::read('core.date_format'), $utz) ?></span>
            </div>
        </div>
        <div class="clear"></div>
        <span class="review_star feed-wrote-review">
            <input readonly value="<?php echo $business_review['rating']; ?>" type="number" class="rating form-control hide" data-size="xs">
        </span>
        <?php echo $this->Business->viewMore($business_review['content'],null, null, null, true, array('no_replace_ssl' => 1)); ?>
        <?php echo $this->Element('Business.review_photo_activity', array(
            'photos' => $photos,
            'photos_total' => $photos_total
        ));?>
    </div>
</div>