<?php 
    $business = $review['Business'];
    $review_photos = $review['Photo'];
    $replies = !empty($review['children']) ? $review['children'] : null;
    $business_categories = !empty($review['Business']['BusinessCategory']) ? $review['Business']['BusinessCategory'] : null;
    $review = $review['BusinessReview'];
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
    $photoHelper = MooCore::getInstance()->getHelper('Photo_Photo');
?>
<li id="itemreview_<?php echo $review['id']?>" class="full_content p_m_10">
    <?php if(!empty($cuser) && $cuser['id'] == $review['user_id']):?>
    <div class="list_option">
        <div class="dropdown">
            <button id="dropdown-edit" data-target="#" data-toggle="dropdown" ><!--dropdown-user-box-->
                <i class="material-icons dp-18">more_vert</i>
            </button>
            <ul role="menu" class="dropdown-menu" aria-labelledby="dropdown-edit" style="float: right;">
                <li>
                    <a class="edit_review" href="javascript:void(0)" data-business_id="<?php echo $review['business_id'];?>" data-id="<?php echo $review['id']?>" data-parent_id="<?php echo $review['parent_id']?>" data-view_detail="0" data-my_review="1">
                        <?php echo __d('business', 'Edit Review') ?>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" class="delete_review" data-review_id="<?php echo $review['id']?>" data-parent_id="<?php echo $review['parent_id']?>" >
                        <?php echo __d('business', 'Delete Review') ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <?php endif;?>
    <?php if(empty($is_reply)):?>
    <a class="bus_img" href="<?php echo $business['moo_href'];?>" style="width: 80px">
        <?php if($business['featured']):?><i class="featured_small"></i><?php endif;?>
        <?php echo $businessHelper->getPhoto($business, array('prefix' => BUSINESS_IMAGE_THUMB_WIDTH.'_', 'class' => 'img_wrapper2 user_list thumb_mobile'));?>
    </a>
    <div class="bus_my_review_info">
        <?php if(!empty($business['moo_parent'])):?>
        <div class="bus_branch_head">
            <?php echo __d('business', 'Sub page of');?>: 
            <a href="<?php echo $business['moo_parent']['moo_href'];?>" class="title">
                <?php echo $business['moo_parent']['name'];?>                
            </a> 
        </div>
        <?php endif;?>
        <a href="<?php echo $business['moo_href'];?>" class="title">
            <?php echo $business['name'];?>                
        </a>
        <div class="extra_info">
            <?php if($business_categories != null):?>
                <?php foreach($business_categories as $k_cat => $business_category):?>
                    <?php if($business_category['user_create'] && !$business_category['enable']):?>
                        <?php echo $business_category['name'];?>
                    <?php else:?>
                        <a href="<?php echo $business_category['moo_href'];?>">
                            <?php echo $business_category['name'];?>
                        </a>
                    <?php endif;?>
                <?php if($k_cat < count($business_categories) - 1):?>,<?php endif;?>
                <?php endforeach;?>
            <?php endif;?>
            <div><?php echo $business['address'];?></div>
            <div class="review_star">
                <input readonly id="input-21e" value="<?php echo $business['total_score']; ?>" type="number" class="rating form-control hide" min="0" max="5" step="0.5" data-size="sm">
                <span class="total_rating"><?php echo sprintf($business['review_count'] == 1 ?  __d('business', '%s review') : __d('business', '%s reviews'), $business['review_count']);?></span>
            </div>
        </div>
    </div>
    <?php endif;?>
    <div class="bus_my_sub_review">
        <?php if(empty($is_reply)):?>
        <!-- <span class="review_star">
            <input id="input-21e" readonly value="<?php echo $review['rating']; ?>" type="number" class="rating form-control hide" min="0" max="5" step="0.5" data-size="md">
        </span> -->
        <?php endif;?>
        <div class="left">
        <div class="bus_review_text" id="item_feed_comment_text_<?php echo $review['id']?>">
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
                    <div class="clear"></div>
                </div>
            <?php endif;?>

        </div>
       
        </div>
        <div class="clear"></div>
         <?php if($user_id != $uid):?>
        <div class="bus_review_action pull-left">
            <?php if($review['parent_id'] == 0):?>
                <span class="btn-feed">
                    <a class="<?php if(isset($review['set_useful']) && $review['set_useful']):?><?php endif;?> <?php if($review['user_id'] != $uid):?>review_useful<?php endif;?>" id="btnUseful<?php echo $review['id'];?>" href="javascript:void(0)" data-review_id="<?php echo $review['id'];?>">
                        &nbsp;<i class="material-icons">thumb_up</i>
                    </a>
                    <a href="javascript:void(0)" id="useful_count_<?php echo $review['id'];?>">
                        <?php echo $review['useful_count'];?>
                   </a>
                </span>
                <a class="btn btn-default" href="<?php echo $this->request->base.'/business_review/report/'.$review['id'];?>"  data-backdrop="true" data-dismiss="" data-toggle="modal" data-target="#businessModal">
                    <?php echo __d('business', 'Report');?>
                </a>
            <?php endif;?>
        </div>

        <?php endif?>
        <span class="feed-time date pull-right">
            <?php echo $this->Moo->getTime($review['created'], Configure::read('core.date_format'), $utz )?>
        </span>
        <div class="clear"></div>
        <?php if($replies != null):?>
        <div class="company_review_profile">
        <span  class="reply_owner"><?php echo __d('business', 'Company response:');?> </span>
        <ul class="bus_review_reply" id="reply_content<?php echo $review['id'];?>">
            <?php foreach($replies as $reply):?>
                <?php echo $this->Element('Business.misc/my_review_item', array(
                    'review' => $reply,
                    'is_reply' => true
                ));?>
            <?php endforeach;?>
        </ul>
        </div>
        <?php endif;?>
    </div>
</li>