<?php
    $user = $business_admin['User'];
    $business_admin = $business_admin['BusinessAdmin'];
?>
<div class="user_mini" id="admin_item_<?php echo $business_admin['user_id'];?>">
<div>
	<?php
        echo $this->Moo->getItemPhoto(array('User' => $user),array( 'prefix' => '100_square'), array('class' => 'img_wrapper2 user_avatar_large'));
    ?>
    <div class="user-info">
		<?php echo $this->Moo->getName($user)?>	
        <?php if($user['id'] != $uid):?>
        <a class="extra_info remove_business_admin" href="javascript:void(0)" data-business_id="<?php echo $business_admin['business_id'];?>" data-user_id="<?php echo $business_admin['user_id'];?>">
            <?php echo __d('business', 'Remove');?>
        </a>
        <?php endif;?>
	</div>
</div>
</div>