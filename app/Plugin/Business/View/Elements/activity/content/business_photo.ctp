<?php 
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
    $business_photos = $businessHelper->getBusinessAlbumPhotos($activity['Activity']['target_id']);
?>

<?php echo $this->Element('Photo.activity/content/photos_add', array(
    'activity' => $activity
));?>
