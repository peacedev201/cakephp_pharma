<?php echo $this->Html->css(array('Business.business-widget.css' )); ?>
<?php if(Configure::read('Business.business_enabled')): ?>
<?php if(!empty($featured_businesses)): 
echo $this->Html->css(array('Business.flexslider'), null, array('inline' => false));
$businessHelper = MooCore::getInstance()->getHelper('Business_Business'); 
if(empty($title)) $title = __d('business', 'Featured Businesses') ;
    if(empty($num_item_show)) $num_item_show = 10;
    if(isset($title_enable)&&($title_enable)=== "") $title_enable = false; else $title_enable = true;
?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => false, 'requires' => array('jquery', 'mooBusiness'), 'object' => array('$', 'mooBusiness'))); ?>	
$(window).load(function() {
var flex_width = $('.featured_job').width();
//flex_width = Math.floor(flex_width / (Math.floor(flex_width / 175)));
flex_width = 167;
$('.featured_busieness').flexslider({
animation: "slide",
animationLoop: false,
itemWidth: flex_width,
itemMargin: 10,
controlNav: false
});
});
<?php $this->Html->scriptEnd(); ?>

<div class="box2" id="featured_busieness">
        <?php if($title_enable): ?>
    <h3><?php echo h($title) ?></h3>
        <?php endif; ?>
    <div class="flexslider featured_busieness">
        <ul class="slides">
                <?php foreach($featured_businesses as $business): ?>                
            <li class="featured_business_item">
                <div class="business_photo">
                    <a href="<?php echo $business['Business']['moo_href']; ?>" title="<?php echo h($business['Business']['name']) ?>">
                        <span class="featured_business_item_photo" style="background-image:url(<?php echo $businessHelper->getPhoto($business['Business'], array(
                                                                                                                                                                    'tag' => false,
                                                                                                                                                                    'prefix' => BUSINESS_IMAGE_SMALL_WIDTH.'_', 
                                                                                                                                                                    'width' => '140px',
                                                                                                                                                                    'class' => 'img_wrapper2 user_list thumb_mobile')); ?>);"></span>
                    </a> 
                </div>
                <div class="business-info">
                    <div class="business_name">
                        <a href="<?php echo $business['Business']['moo_href']; ?>" title="<?php echo $business['Business']['moo_title'] ?>">
                                    <?php echo $business['Business']['moo_title'] ?>
                        </a>
                    </div>
                    <div class="business_category">
                            <?php $business_categories = !empty($business['BusinessCategory']) ? $business['BusinessCategory'] : null; ?>
                            <?php if($business_categories != null):?>
                                <?php foreach($business_categories as $k_cat => $business_category):?>
                        <a href="<?php echo $business_category['moo_href'];?>">
                                    <?php echo $business_category['name'];?>
                        </a>
                                <?php if($k_cat < count($business_categories) - 1):?>,<?php endif;?>
                                <?php endforeach;?>
                            <?php endif;?>
                    </div>
                </div>
            </li>
                <?php endforeach; ?>
        </ul>
    </div>
</div>
    <?php endif; ?>
<?php endif;