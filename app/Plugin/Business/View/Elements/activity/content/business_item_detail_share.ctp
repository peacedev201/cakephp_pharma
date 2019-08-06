<?php 
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
    $business = $businessHelper->getOnlyBusiness($activity['Activity']['parent_id']);
?>

<div class="comment_message">
    <?php echo $this->Business->viewMore(h($activity['Activity']['content']), null, null, null, true, array('no_replace_ssl' => 1)); ?>
    <?php if(!empty($activity['UserTagging']['users_taggings'])) $this->MooPeople->with($activity['UserTagging']['id'], $activity['UserTagging']['users_taggings']); ?>
    <div class="share-content">
        <?php echo $this->Element('Business.misc/business_activity_item', array(
            'business' => $business,
            'new_page' => false
        ));?>
    </div>
</div>