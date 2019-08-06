<?php
    echo $this->Html->css(array(
        'Business.star-rating',
        'Business.business-widget'
    ));
?>
<?php

$mBusinessAdmin = MooCore::getInstance()->getModel('Business.BusinessAdmin');
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
    $business_categories = !empty($business['BusinessCategory']) ? $business['BusinessCategory'] : null;
    $business = $business['Business'];
    if(!empty($business_category_id) && $business_category_id > 0)
    {
        $business['moo_href'] = $business['moo_href'].'/cat:'.$business_category_id;
    }
    $link_map = $this->request->base.'/business/load_map/?address='.urlencode($business['name']).'&lat='.$business['lat'].'&lng='.$business['lng'].'&scrollwheel=0';
?>

<div class="activity_item">
    <div class="activity_left">
        <a href="<?php echo $business['moo_href'];?>" <?php if(!isset($new_page) || $new_page != false):?>target="_blank"<?php endif;?>>
            <?php echo $businessHelper->getPhoto($business, array(
                'prefix' => BUSINESS_IMAGE_SMALL_WIDTH.'_', 
                'width' => '140px',
                'class' => 'img_wrapper2 user_list thumb_mobile'));?>
        </a>
    </div>
    <div class="activity_right ">
        <div class="activity_header">
            <a href="<?php echo $business['moo_href'];?>" class="title" <?php if(!isset($new_page) || $new_page != false):?>target="_blank"<?php endif;?>>
                <?php echo $business['name'];?>                
            </a>
        </div>
        <div class="extra_info">
            <?php if($business_categories != null):?>
                <?php foreach($business_categories as $k_cat => $business_category):?>
                <a href="<?php echo $business_category['moo_href'];?>" <?php if(!isset($new_page) || $new_page != false):?>target="_blank"<?php endif;?>>
                    <?php echo $business_category['name'];?>
                </a>
                <?php if($k_cat < count($business_categories) - 1):?>,<?php endif;?>
                <?php endforeach;?>
            <?php endif;?>
            <div class="feed_bus_address">
                <?php if(isset($index)):?>
                    <a href="javascript:void(0)" data-index="<?php echo $index;?>" class="show_map_marker">
                        <?php echo $business['address'];?>  
                    </a>
                <?php else:?>
                    <?php echo $business['address'];?>  
                <?php endif;?>
            </div>
            <div class="feed_bus_review">
                <?php //if(!isset($is_branch)):?>
                    <div class="review_star">
                        <input readonly value="<?php echo $business['total_score']; ?>" type="number" class="rating form-control hide" data-stars="5" data-size="xs">
                        &nbsp;&nbsp;&nbsp;
                        <a href="<?php echo $business['moo_hrefreview'];?>" <?php if(!isset($new_page) || $new_page != false):?>target="_blank"<?php endif;?>>
                            <?php echo sprintf($business['review_count'] == 1 ?  __d('business', '%s review') : __d('business', '%s reviews'), $business['review_count']);?>
                        </a>
                        <?php /*if(!empty($cuser) && $cuser['id'] != $business['user_id'] && empty($share)):?>
                            <span>
                            <a href="<?php echo $business['moo_hrefreview'];?>" target="_blank">
                                <?php echo __d('business', 'Write a review');?>
                            </a>
                            </span>
                        <?php endif;*/ ?>
                        <?php if(!empty($map_item) && !empty($link_map) && empty($share)):?>
                        &nbsp;&nbsp;&nbsp;
                        <a href="javascript:void(0)" class="btn_toggle btn_show_map" data-item="<?php echo $map_item;?>" data-link="<?php echo $link_map;?>">
                            <i class="material-icons ">location_on</i>
                            <?php echo __d('business', 'Show map') ?>
                        </a>
                        <?php endif;?>
                    </div>
                <?php //endif;?>
            </div>
        </div>
        <div class="blog-description-truncate">
            <?php echo $this->Text->truncate(strip_tags(str_replace(array('<br>','&nbsp;'), array(' ',''), $business['description'])), 200, array('eclipse' => ''));?>
        </div>
        <div class="clear"></div>
    </div>
</div>

<?php if($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery", "mooBusiness"], function($, mooBusiness) {
            mooBusiness.initReviewStar();
        });
    </script>
<?php endif;?>