<?php
echo $this->Html->css(array(
    'Business.star-rating'), array('block' => 'css', 'minify'=>false));
?>
<script type="text/javascript">var switchTo5x=true;</script>
<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
<script type="text/javascript">
    stLight.options({
        publisher: "178eca1f-c600-4c8a-8db3-64a04c5f87f7", 
        doNotHash: false, 
        doNotCopy: false, 
        hashAddressBar: false,
        servicePopup:true
    });
</script>

<?php 
    $business = $review['Business'];
    $review_photos = $review['Photo'];
    $user_review = $review['User'];
    $review = $review['BusinessReview'];
    $photoHelper = MooCore::getInstance()->getHelper('Photo_Photo');
?>

<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'mooBusiness', 'business_star_rating'), 
    'object' => array('$', 'mooBusiness')
));?>
    mooBusiness.initReviewDetail()
<?php $this->Html->scriptEnd(); ?>
<?php if(!empty($uid) && ($review['user_id'] == $uid)): ?>
<h3 class="review_detail_header"><?php echo sprintf(__d('business', 'Your review for %s is now live!'), $business['name']);?></h3>
<?php endif; ?>
<div class="box2 review_detail_me">
    
    <div class="box_content">
        <div class="col-md-5">
            <div class="bus_top_latest_review bus_review_item tip_review" id="top_latest_review_<?php echo $business['id'];?>" style="display: block;">
                <?php
                    echo $this->Moo->getItemPhoto(array('User' => $user_review),array( 'prefix' => '100_square'), array('class' => 'img_wrapper2 user_avatar_96 '));
                ?>
                <div class="bus_review_info">
                    <div class="user_review">
                        <?php echo $this->Moo->getName($user_review)?>
                    </div>
                    <div class="tip_review_location">London, United Kingdom</div>
                    <div class="bus_user_info">
                        <div>
                            <span class="review_star">
                                <i class="material-icons">mood</i>
                            </span>
                            <?php echo __d('business', 'Reviews');?>
                            <div><?php echo $total_review; ?></div>
                        </div>
                        <div>
                            <span class="grey_bg">
                                <i class="material-icons">people</i>
                            </span>
                            <?php echo __d('business', 'Friends');?>
                            <div><?php echo $user_review['friend_count'];?></div>
                        </div>
                    </div>
                    <div class="review_star" style="height: 50px;">
                        <input readonly id="input-21e" value="<?php echo $review['rating']; ?>" type="number" class="rating form-control hide" min="0" max="5" step="0.5" data-size="sm">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="business_review" style="font-size: 24px; font-weight: 700">
                <a href="<?php echo $business['moo_href']; ?>"><?php echo $business['moo_title']; ?></a>
            </div>
            <div class="business_review_location" style="color: rgba(0, 0, 0, 0.5); font-size: 14px"><?php echo $business['address']; ?></div>
            <div class="review_mini_detail">
                <span class="review_star">
                    <i class="material-icons">mood</i>
                    <?php echo $business['review_count']; ?> <?php echo $business['review_count'] == 1 ? __d('business', 'review') : __d('business', 'reviews');?>
                </span>
                <?php /*if($is_first_review):?>
                    <!--span><i class="green_content">1</i> <?php echo __d('business', 'First to review'); ?></span-->
                <?php endif;*/?>
                <?php if($business['checkin_count'] > 0):?>
                    <span>
                        <i class="material-icons review_detail_frs_check">check</i> 
                        <?php echo $business['checkin_count'];?>
                        <?php echo $business['checkin_count'] > 1 ? __d('business', 'check-ins') : __d('business', 'check-in'); ?>
                    </span>
                <?php endif;?>
                <div class="pull-right">
                    <?php echo date('M d, Y', strtotime($business['created']));?>
                </div>
            </div>
            <div class="bus_review_text" id="item_feed_comment_text_<?php echo $review['id']?>" style="padding: 0px">
                <?php echo $this->Business->viewMore($review['content'],null, null, null, true, array('no_replace_ssl' => 1)); ?>
                <?php if ($review_photos):?>
                    <div class="bus_review_photo">
                        <?php foreach($review_photos as $photo):?>
                            <div class="bus_review_photo_item">
                                <a class="layer_square photoModal" href="<?php echo $this->request->base.'/photos/view/'.$photo['id'];?>">
                                    <img src="<?php echo $photoHelper->getImage(array('Photo' => $photo), array('prefix' => '150_square'));?>" />
                                </a>
                            </div>
                        <?php endforeach;?>
                    </div>
                <?php endif;?>
                <div class="share_yr_review">	
                    <strong>
                        <?php echo __d('business', 'Share your Review'); ?>
                    </strong>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="clear" style="margin-top: 20px">
                <?php if(!empty($uid) && ($review['user_id'] == $uid)): ?>
                <a class="edit_review btn btn-action" href="javascript:void(0)" data-business_id="<?php echo $review['business_id'];?>" data-id="<?php echo $review['id']?>" data-parent_id="<?php echo $review['parent_id']?>" data-view_detail="1">
                    <?php echo __d('business', 'Edit review'); ?>
                </a>
                <?php endif; ?>
                <?php if (!empty($uid) && (($review['user_id'] == $uid) || (!empty($cuser) && $cuser['Role']['is_admin']))): ?>
                <a class="delete_review btn btn-action" href="javascript:void(0)" data-review_id="<?php echo $review['id']?>" data-parent_id="<?php echo $review['parent_id']?>" data-redirect="1">
                    <?php echo __d('business', 'Delete review'); ?>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-7">
            <div class="share_yr_review">
                <div class="share_review_social">
                    <span class='st_facebook_large' displayText='Facebook' st_url='<?php echo $sBusinessHref; ?>'></span>
                    <span class='st_twitter_large' displayText='Tweet' st_url='<?php echo $sBusinessHref; ?>'></span>
                    <?php echo $this->Form->text('review_link', array(
                        'value' => $sBusinessHref
                    ));?>
                    <button type="button" id="copy_link">
                        <?php echo __d('business', 'Copy'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>