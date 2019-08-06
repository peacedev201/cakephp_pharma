<?php 
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
    $business = $businessHelper->getOnlyBusiness($activity['Activity']['target_id']);
?>
<?php if($activity['Activity']['content'] != null):?>
    <p class="feed_text">
        <?php echo $this->viewMore(h($activity['Activity']['content']),null, null, null, true, array('no_replace_ssl' => 1));?>
    </p>
<?php endif;?>
<iframe
    width="100%"
    height="180px"
    style="display:none"
    frameborder="0" style="border:0"
    src="" allowfullscreen id="activity_map_<?php echo $activity['Activity']['id'];?>">
</iframe>
<?php echo $this->Element('Business.misc/business_activity_item', array(
    'business' => $business,
    'no_btn_checkin' => true,
    'map_item' => '#activity_map_'.$activity['Activity']['id'],
    'new_page' => false
));?>
