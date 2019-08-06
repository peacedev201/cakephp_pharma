<?php
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
    $business = $businessHelper->getOnlyBusiness($activity['Activity']['target_id']);
    $business = $business['Business'];
?>
<?php echo __d('business', 'wrote a review for');?>
<a href="<?php echo $business['parent_id'] > 0 ? $business['moo_href'] : $business['moo_hrefreview']."?review=".$activity['Activity']['item_id'];?>">
    <?php echo $business['name'];?>
</a>