<?php 
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
    $business = $businessHelper->getOnlyBusiness($object['Business']['id']);
?>

<ul id="photo-content" class="photo-list">
    <?php echo $this->Element('Business.misc/business_activity_item', array(
        'business' => $business,
        'share' => true
    ));?>
</ul>