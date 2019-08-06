<div class="mo_breadcrumb">
    <h1><?php echo __d('business', 'Reviews');?></h1>
</div>
<?php if(!$is_loggedin):?>
    <div class="login_mess">
    <?php echo __d('business', 'Login or register to post your review');?>
    </div>
<?php elseif($business['Business']['user_id'] != $uid && $is_loggedin && !$is_reviewed):?>
    <a class="btn btn-action btnReview btn_write_review pull-right" href="javascript:void(0)" data-business_id="<?php echo $business_id;?>" data-view_detail="1">
        <?php echo __d('business', 'Write review');?>
    </a>
<?php endif;?>
<div class="clear"></div>
<ul id="review_content" class="list6 "></ul>