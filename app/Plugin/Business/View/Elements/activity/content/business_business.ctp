<?php 
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
    $business = $businessHelper->getOnlyBusiness($activity['Activity']['target_id']);
?>

<?php echo $this->Element('Business.misc/business_item', array(
    'business' => $business,
));?>
