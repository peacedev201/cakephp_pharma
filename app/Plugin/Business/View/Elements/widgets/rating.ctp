<?php
echo $this->Html->css(array(
    'Business.star-rating'
    ), array('block' => 'css', 'minify'=>false));
?>
<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'mooBusiness', 'business_star_rating'), 
    'object' => array('$', 'mooBusiness')
));?>
    mooBusiness.initReviewStar();
<?php $this->Html->scriptEnd(); ?> 
<div class="box2 filter_block review_wd">
    <h3><?php echo __d('business', 'Reviews & Rating');?></h3>
    <?php echo $this->element('Business.misc/rating_item', array(
        'business' => $business,
        'rulers' => $rulers,
        'is_reviewed' => $is_reviewed,
    ));?>
</div>