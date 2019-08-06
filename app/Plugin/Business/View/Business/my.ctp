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

<?php $this->setNotEmpty('east');?>
<?php $this->start('east'); ?>
<?php echo $this->widget('Business.business/create_button') ?> 
<?php $this->end(); ?>


<div class="bar-content">
    <div class="content_center my_bus_active" id="browse">
        <?php echo $this->Element('Business.profile_business_tabs');?>
        <h3 class="header_green">
            <?php echo __d('business', 'My businesses');?>
        </h3>
       <!--  <a href="<?php echo $this->request->base;?>/businesses/create" class="button">
            <?php echo __d('business', 'Create your business');?>            
        </a> -->
        <ul class="bus_list bussiness-list" id="my_business_content">
            <?php echo $this->Element('Business.lists/my_business_list', array(
                'businesses' => $businesses,
                'more_url' => $more_url
            ));?>
        </ul>
    </div>
</div>
