<?php
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
    $business = $businessHelper->getOnlyBusiness($activity['Activity']['target_id']);
    $business = $business['Business'];
    $business_photos = $businessHelper->getBusinessAlbumPhotos($business['id']);
    $photo_count = $activity['Activity']['items'] != null ? explode(',', $activity['Activity']['items']) : array();
?>
<?php if(count($business_photos) > 1) echo sprintf(__d('business', ' added %s photos for '), count($photo_count)); else echo sprintf(__d('business', ' added %s photo for '), count($business_photos));?>
<a href="<?php echo $business['moo_href'];?>">
    <?php echo $business['name'];?>
</a>