<?php

$mBusinessAdmin = MooCore::getInstance()->getModel('Business.BusinessAdmin');
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
    $business_categories = !empty($business['BusinessCategory']) ? $business['BusinessCategory'] : null;
    $business = $business['Business'];
    if(!empty($business_category_id) && $business_category_id > 0)
    {
        $business['moo_href'] = $business['moo_href'].'/cat:'.$business_category_id;
    }
?>

<li class="full_content p_m_10">
    <?php if(!empty($business['moo_parent'])):?>
    <div class="bus_branch_head">
        <?php echo __d('business', 'Sub page of');?>: 
        <a href="<?php echo $business['moo_parent']['moo_href'];?>" class="title">
            <?php echo $business['moo_parent']['name'];?>                
        </a> 
    </div>
    <?php endif;?>
    <?php if(isset($show_list_option) && $show_list_option === true):?>
    <div class="list_option">
        <div class="dropdown">
            <button class="button" data-toggle="dropdown" data-target="#" id="dropdown-edit"><!--dropdown-user-box-->
                <i class="material-icons dp-18">more_vert</i>
            </button>
            <ul aria-labelledby="dropdown-edit" class="dropdown-menu" role="menu">
                <li>
                    <a href="<?php echo $business['parent_id'] > 0 ? $url_dashboard.'create_branch/'.$business['parent_id'].'/'.$business['id'] : $url_dashboard.'edit/'.$business['id'];?>">
                            <?php echo __d('business', 'Edit');?>
                    </a>
                </li>
                <li>
                    <a class="delete_business" href="javascript:void(0)" data-id="<?php echo $business['id'];?>" data-parent_id="<?php echo $business['parent_id'];?>"> 
                            <?php echo __d('business', 'Delete');?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="clear"></div>
    <?php endif;?>
    <?php if(isset($show_list_option_favourite) && $show_list_option_favourite === true):?>
    <div class="list_option">
        <div class="dropdown">
            <button class="button" data-toggle="dropdown" data-target="#" id="dropdown-edit"><!--dropdown-user-box-->
                <i class="material-icons dp-18">more_vert</i>
            </button>
            <ul aria-labelledby="dropdown-edit" class="dropdown-menu" role="menu">
                <li>
                    <a class="add_favourite" href="javascript:void(0)" data-id="<?php echo $business['id'];?>" data-remove="1"> 
                            <?php echo __d('business', 'Remove favorite');?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="clear"></div>
    <?php endif;?>
    <?php if(isset($show_list_option_follow) && $show_list_option_follow === true):?>
    <div class="list_option">
        <div class="dropdown">
            <button class="button" data-toggle="dropdown" data-target="#" id="dropdown-edit"><!--dropdown-user-box-->
               <i class="material-icons dp-18">more_vert</i>
            </button>
            <ul aria-labelledby="dropdown-edit" class="dropdown-menu" role="menu">
                <li>
                    <a class="btn_follow" href="javascript:void(0)" data-id="<?php echo $business['id'];?>" data-remove="1"> 
                            <?php echo __d('business', 'Unfollow');?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="clear"></div>
    <?php endif;?>
    <a href="<?php echo $business['moo_href'];?>">
        <?php echo $businessHelper->getPhoto($business, array(
            'prefix' => BUSINESS_IMAGE_SMALL_WIDTH.'_', 
            'width' => '140px',
            'class' => 'img_wrapper2 user_list thumb_mobile'));?>
    </a>
    <div class="blog-info">
        <?php if($business['featured'] == 1):?>
        <sup class="featured_busieness_label"><?php echo __d('business', 'Featured');?></sup>
        <?php endif;?>
        <a href="<?php echo $business['moo_href'];?>" class="title">
            <?php echo $business['name'];?>                
        </a>
        <div class="extra_info">
            <?php if($business_categories != null):?>
                <?php foreach($business_categories as $k_cat => $business_category):?>
                    <a href="<?php echo $business_category['moo_href'];?>">
                        <?php echo $business_category['name'];?>
                    </a>
                <?php if($k_cat < count($business_categories) - 1):?>,<?php endif;?>
                <?php endforeach;?>
            <?php endif;?>
            <div class="feed_bus_address">
                <?php if(isset($index) && is_int($index)):?>
                    <a href="javascript:void(0)" data-index="<?php echo $index;?>" class="show_map_marker">
                            <?php echo $business['address'];?>  
                    </a>
                <?php else:?>
                    <?php echo $business['address'];?>  
                <?php endif;?>
            </div>
            <div class="feed_bus_review">
                <?php if(!isset($is_branch)):?>
                <div class="review_star">
                    <input readonly value="<?php echo $business['total_score']; ?>" type="number" class="rating form-control hide" data-stars="5" data-size="xs">
                    <span>
                            <?php echo sprintf($business['review_count'] == 1 ?  __d('business', '%s review') : __d('business', '%s reviews'), $business['review_count']);?>
                    </span>
                </div>
                <?php endif;?>
            </div>
        </div>
        <?php if(isset($show_status) && $show_status === true):?>
            <div class="my-business-status <?php if($business['status'] == BUSINESS_STATUS_APPROVED){ echo 'status-approved';}elseif($business['status'] == BUSINESS_STATUS_PENDING){ echo 'status-pending';}?>">
                <?php 
                    switch ($business['status'])
                    {
                        case BUSINESS_STATUS_APPROVED:
                            echo __d('business', 'Approved');
                            break;
                        case BUSINESS_STATUS_PENDING:
                            echo __d('business', 'Pending');
                            break;
                        case BUSINESS_STATUS_REJECTED:
                            echo __d('business', 'Rejected');
                            break;
                    }
                ?>
            </div>
            <div class="clear"></div>
        <?php endif;?>
        <?php if($business['claim_id'] > 0 && $business['is_claim'] == 3):?>
            &nbsp;
            <div class="my-business-status status-approved">
                <?php echo __d('business', 'Claim Request Sent');?>
            </div>
            <div class="clear"></div>
        <?php endif;?>
        <div class="clear"></div>
    </div>
</li>