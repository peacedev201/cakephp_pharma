<?php 
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
    $mActivity = MooCore::getInstance()->getModel('Activity');
    
    $parent_activity = $mActivity->findById($activity['Activity']['parent_id']);
    $business = $businessHelper->getOnlyBusiness($parent_activity['Activity']['target_id']);
    $business_photos = $businessHelper->getBusinessAlbumPhotos($parent_activity['Activity']['target_id']);
    $photo_count = $parent_activity['Activity']['items'] != null ? explode(',', $parent_activity['Activity']['items']) : array();
?>

<div class="comment_message">
    <?php echo $this->Business->viewMore(h($activity['Activity']['content']), null, null, null, true, array('no_replace_ssl' => 1)); ?>
    <?php if(!empty($activity['UserTagging']['users_taggings'])) $this->MooPeople->with($activity['UserTagging']['id'], $activity['UserTagging']['users_taggings']); ?>
    <div class="share-content">
        <div class="activity_feed_content">
            <div class="activity_text">
                <?php echo $this->Moo->getName($parent_activity['User']) ?>
                <?php if(count($business_photos) > 1) echo sprintf(__d('business', ' added %s photos for '), count($photo_count)); else echo sprintf(__d('business', ' added %s photo for '), count($business_photos));?>
                <a href="<?php echo $business['Business']['moo_href'];?>">
                    <?php echo $business['Business']['name'];?>
                </a>
            </div>
            <div class="parent_feed_time">
                <span class="date"><?php echo $this->Moo->getTime($parent_activity['Activity']['created'], Configure::read('core.date_format'), $utz) ?></span>
            </div>
        </div>
        <div class="clear"></div>
        <?php echo $this->Element('Photo.activity/content/photos_add', array(
            'activity' => $parent_activity
        ));?>
    </div>
</div>