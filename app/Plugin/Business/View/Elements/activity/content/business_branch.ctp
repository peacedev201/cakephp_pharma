<?php 
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
    $branch = $businessHelper->getOnlyBusiness($activity['Activity']['item_id']);
?>
<iframe
    width="100%"
    height="180px"
    style="display:none"
    frameborder="0" style="border:0"
    src="" allowfullscreen id="activity_map_<?php echo $activity['Activity']['id'];?>">
</iframe>
<?php echo $this->Element('Business.misc/business_activity_item', array(
    'business' => $branch,
    'is_branch' => 1,
    'map_item' => '#activity_map_'.$activity['Activity']['id'],
    'new_page' => false
));?>
