<?php 
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
    $branch = $businessHelper->getOnlyBusiness($activity['Activity']['item_id']);
?>

<?php echo $this->Element('Business.misc/business_activity_item', array(
    'business' => $branch,
    'is_branch' => 1
));?>
