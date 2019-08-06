<?php
    $user = $review['User'];
    $review_photos = $review['Photo'];
    $replies = !empty($review['children']) ? $review['children'] : null;
    $review = $review['BusinessReview'];
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
    $photoHelper = MooCore::getInstance()->getHelper('Photo_Photo');
?>
<li class="bus_review_item" id="<?php if($review['parent_id'] == 0):?>itemreview_<?php echo $review['id']?><?php else:?>itemreply_<?php echo $review['parent_id']?>_<?php echo $review['id']?><?php endif;?>" style="position: relative">
    
    <div class="dropdown list_option">
        <a href="javascript:void(0)" data-toggle="dropdown" class="cross-icon">
            <i class="material-icons">more_vert</i>
        </a>
        <ul class="dropdown-menu">
            <?php if(!empty($uid)) : ?>
                <?php if(($review['parent_id'] == 0 && $uid == $user['id']) || ($review['parent_id'] > 0 && $can_response_review)):?>
                <li>
                    <a class="edit_review" href="javascript:void(0)" data-business_id="<?php echo $review['business_id'];?>" data-id="<?php echo $review['id']?>" data-parent_id="<?php echo $review['parent_id']?>">
                        <?php if($review['parent_id'] == 0):?>
                            <?php echo __d('business', 'Edit Review'); ?>
                        <?php else:?>
                            <?php echo __d('business', 'Edit Reply'); ?>
                        <?php endif;?>
                    </a>	
                </li>
                <?php endif;?>
                <?php if(($review['parent_id'] == 0 && $uid == $user['id']) || ($review['parent_id'] > 0 && $can_response_review) || $can_response_review):?>
                <li>
                    <a href="javascript:void(0)" class="delete_review" data-review_id="<?php echo $review['id']?>" data-parent_id="<?php echo $review['parent_id']?>" >
                        <?php if($review['parent_id'] == 0):?>
                            <?php echo __d('business', 'Delete Review'); ?>
                        <?php else:?>
                            <?php echo __d('business', 'Delete Reply'); ?>
                        <?php endif;?>
                    </a>
                </li>
                <?php endif;?>
                <?php if($review['parent_id'] == 0/* && $can_reply_review*/ && $can_response_review):?>
                    <li>
                        <a id="btnReply_<?php echo $review['id'];?>" class="btn_reply" href="javascript:void(0)" data-business_id="<?php echo $review['business_id'];?>" data-id="<?php echo $review['id'];?>" <?php if($replies != null):?>style="display: none"<?php endif;?>>
                            <?php echo __d('business', 'Reply');?>
                        </a>
                    </li>
                <?php endif;?>
            <?php endif;?>
                    
            <?php //if(empty($uid) || (!empty($uid) && $uid != $user['id'])):?>
            <li>
                <a href="javascript:void(0)" class="business_review_report" data-id="<?php echo $review['id'];?>">
                    <?php echo __d('business', 'Report');?>
                </a>
            </li>
            <?php //endif;?>
        </ul>
    </div>
    
   
    <div class="clear visible-xs visible-sm"></div>
    <?php if(empty($is_reply)):?>
        <?php
            echo $this->Moo->getItemPhoto(array('User' => $user),array( 'prefix' => '100_square'), array('class' => 'img_wrapper2 user_avatar_96 '));
        ?>
    <?php endif;?>
    <div class="bus_review_info bus_review_reply_info">
        <?php if(empty($is_reply)):?>
            <div class="comment_message <?php if(isset($review['first_review']) && $review['first_review']):?> first_review_comment <?php endif; ?>">
                <?php echo $this->Moo->getName($user)?>
            </div>
            
            <span class="feed-time date">
                <?php if(!empty($just_now)):?>
                    <?php echo __d('business', 'Just now')?>
                <?php else:?>
                    <?php echo $this->Moo->getTime($review['created'], Configure::read('core.date_format'), $utz )?>
                <?php endif;?>
            </span>
        <?php endif;?>
         <div class="bus_review_action ">
    <?php /*if(isset($review['first_review']) && $review['first_review']):?>
        <span><i class="green_content">1</i> <?php echo __d('business', 'First to review'); ?></span>
        <?php endif;*/?>
        <?php if($review['parent_id'] == 0):?>
        <span class="btn-feed">
        <a class="<?php if(isset($review['set_useful']) && $review['set_useful']):?><?php endif;?> <?php if($review['user_id'] != $uid):?>review_useful<?php endif;?>" id="btnUseful<?php echo $review['id'];?>" href="javascript:void(0)" data-review_id="<?php echo $review['id'];?>">
                &nbsp;<i class="material-icons">thumb_up</i>
                
            </a>
        <a id="useful_count_<?php echo $review['id'];?>" href="<?php echo $this->request->base."/business_review/show_like/".$review['id'];?>" data-target="#businessModal" data-toggle="modal" title="<?php echo __d('business', 'People who like this');?>" data-dismiss="modal" data-backdrop="true">
           
                    <?php echo $review['useful_count'];?>
               </a>
        </span>
        <?php endif;?>
    </div>
        
        <?php if($review['parent_id'] == 0):?>
            <span class="review_star">
                <input id="input-21e" readonly value="<?php echo $review['rating']; ?>" type="number" class="rating form-control hide" min="0" max="5" step="0.5" data-size="md">
            </span>
        <?php endif;?>
        <div class="bus_review_text main_review" id="item_feed_comment_text_<?php echo $review['id']?>">
            <?php if(!empty($is_reply) && $is_reply):?>
                <span class="reply_owner"><?php echo __d('business', 'Company response:');?> </span>
            <?php endif;?>
            <?php echo $this->Business->viewMore($review['content'],null, null, null, true, array('no_replace_ssl' => 1)); ?>
                <?php if ($review_photos):?>
                    <div class="bus_review_photo">
                        <?php foreach($review_photos as $photo):?>
                            <div class="bus_review_photo_item">
                                <a class="layer_square" href="<?php echo $photoHelper->getImage(array('Photo' => $photo), array());?>">
                                    <img src="<?php echo $photoHelper->getImage(array('Photo' => $photo), array('prefix' => '150_square'));?>" />
                                </a>
                            </div>
                        <?php endforeach;?>
                    </div>
                <?php endif;?>
            <div class="clear"></div>
            
        </div>
        <ul class="bus_review_reply" id="reply_content<?php echo $review['id'];?>">
            <?php if($replies != null):?>
                <?php foreach($replies as $reply):?>
                    <?php echo $this->Element('Business.misc/review_item', array(
                        'review' => $reply,
                        'is_reply' => true
                    ));?>
                <?php endforeach;?>
            <?php endif;?>
        </ul>
    </div>
</li>