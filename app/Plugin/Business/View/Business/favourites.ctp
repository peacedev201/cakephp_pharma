<?php 
    echo $this->Html->css(array(
        'Business.star-rating'), array('block' => 'css', 'minify'=>false));
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
?>
<?php if($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery", "mooBehavior"], function($, mooBehavior) {
            mooBehavior.initMoreResults();
        });
    </script>
<?php endif;?>
<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'mooBusiness', 'business_star_rating'), 
    'object' => array('$', 'mooBusiness')
));?>
    mooBusiness.initReviewStar();
<?php $this->Html->scriptEnd(); ?>


<div class="bar-content">
    <div class="content_center fav_bus_active">
        <?php echo $this->Element('Business.profile_business_tabs');?>
        <h3 class="header_green" id="my_favourite_business">
            <?php echo __d('business', 'My Favorite Businesses');?>
        </h3>
        <ul class="bus_list bussiness-list" id="my_business_content">
            <?php echo $this->Element('Business.lists/business_list', array(
                'businesses' => $businesses,
                'more_url' => $more_url,
                'is_favourite' => 1
            ));?>
        </ul>
    </div>
</div>
