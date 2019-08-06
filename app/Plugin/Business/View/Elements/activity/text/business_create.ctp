<?php
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
    $business = $businessHelper->getOnlyBusiness($activity['Activity']['item_id']);
    $business = $business['Business'];
?>
<?php echo $business['parent_id'] > 0 ? __d('business', ' created a new sub page') : __d('business', ' created a new business');?>
<a href="<?php echo $business['moo_href'];?>">
    <?php echo $business['name'];?>
</a>