<?php 
    echo $this->Html->css(array(
        'Business.slick/slick',
        'Business.slick/slick-theme'
    ), array('block' => 'css', 'minify'=>false));
?>
<?php if ($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery", "mooBusiness"], function($, mooBusiness) {
        mooBusiness.initTopBusiness();
    });
</script>
<?php endif?>
<?php 
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
?>
<!-- <h3 id="top_business_cat" class="pull-left">
    <?php echo !empty($business_category['BusinessCategory']) ? $business_category['BusinessCategory']['name'] : __d('business', 'All categories');?>
</h3> -->
<?php if(!empty($business_category['BusinessCategory'])):?>
<!-- <div class="pull-right">
    <a href="<?php echo $business_category['BusinessCategory']['moo_href'];?>">
        <?php echo __d('business', 'See more');?>
    </a>
</div> -->
<?php endif;?>
<div class="clear"></div>
<?php if($businesses != null):?>
    <div class="slider multiple-items bus-center-block">
        <?php foreach($businesses as $business):
            $review = !empty($business['Review']['BusinessReview']) ? $business['Review']['BusinessReview'] : null;
            $user_review = !empty($business['Review']['User']) ? $business['Review']['User'] : null;
            $business = $business['Business'];
        ?>
            <div class="top_business" data-id="<?php echo $business['id'];?>">
            <div>
                <a href="<?php echo $business['moo_href'];?>" style="width: 100%">
                    <?php echo $businessHelper->getPhoto($business, array('prefix' => BUSINESS_IMAGE_SMALL_WIDTH.'_', 'class' => ' user_list'));?>
                </a>
                <!-- <div class="clear"></div> -->
                <div class="bus-info">
                    <a href="<?php echo $business['moo_href'];?>" class="title">
                        <?php echo $business['name'];?>                
                    </a>
                    <div class="review_star">
                        <input readonly id="input-21e" value="<?php echo $business['total_score']; ?>" type="number" class="rating form-control hide" min="0" max="5" step="0.5" data-size="xs">
                        
                    </div>
                   <!--  <a class="total_review" href="<?php echo $business['moo_hrefreview'];?>">
                        <?php echo sprintf(__d('business', '%s reviews'), $business['review_count']);?>
                    </a> -->
                    <div class="clear"></div>
                </div>
                <?php if($review != null):?>
                <div class="mini_top_bus_review">
                    <div class="mini_left">
                        <?php
                            echo $this->Moo->getItemPhoto(array('User' => $user_review),array( 'prefix' => '100_square', 'tooltip' => true), array('class' => 'img_wrapper2 user_ava'));
                        ?>
                    </div>
                    <div class="mini_right">
                        <?php
                            switch($review['rating']){
                                case "1.00":
                                    $icon_review = 'glyphicon glyphicon-star';
                                    break;
                                case "2.00":
                                    $icon_review = 'glyphicon glyphicon-star';
                                    break;
                                case "3.00":
                                    $icon_review = 'glyphicon glyphicon-star';
                                    break;
                                case "4.00":
                                    $icon_review = 'glyphicon glyphicon-star';
                                    break;
                                case "5.00":
                                    $icon_review = 'glyphicon glyphicon-star';
                                    break;
                            }

                            

                         ?>
                         <span class="star_single"><i class="<?php echo $icon_review; ?>"></i></span>

                        <!-- <span class="review_star">
                            <input id="input-21e" readonly value="<?php echo $review['rating']; ?>" type="number" class="rating form-control hide" min="0" max="5" step="0.5" data-size="md">
                        </span> -->
                        <div class="bus_review_date">
                        <?php echo $this->Moo->getTime($review['created'], Configure::read('core.date_format'), $utz )?>
                        </div>
                    </div>
                </div>
                <?php endif;?>
                </div>
            </div>
            
        <?php endforeach;?>
    </div>
<?php else:?>
    <div class="no_item">
    <?php echo __d('business', 'No businesses found');?>
    </div>
<?php endif;?>