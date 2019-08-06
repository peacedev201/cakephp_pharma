<?php
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
    $mUserTagging = MooCore::getInstance()->getModel('UserTagging');
    $business = $businessHelper->getOnlyBusiness($activity['Activity']['target_id']);
    $business = $business['Business'];
    $user_tagging = $mUserTagging->findById($activity['Activity']['item_id']);
?>
<?php echo __d('business', ' at ');?>
<a href="<?php echo $business['moo_href'];?>">
    <?php echo $business['name'];?>
</a>

<?php if(!empty($user_tagging['UserTagging']['users_taggings'])) $businessHelper->with($user_tagging['UserTagging']['id'], $user_tagging['UserTagging']['users_taggings']); ?>