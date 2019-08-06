<?php 
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
    $business = $businessHelper->getOnlyBusiness($activity['Activity']['item_id']);
?>

<ul class="photo-list bus-center-block">
    <?php echo $this->Element('Business.misc/business_activity_item', array(
        'business' => $business,
    ));?>
</ul>