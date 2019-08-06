<?php
echo $this->Html->css(array(
    'Business.star-rating'
    ), array('block' => 'css', 'minify'=>false));
?>
<?php if($this->request->is('ajax')):?>
    <script type="text/javascript">
        require(["jquery","mooBusiness"], function($,mooBusiness) {
            mooBusiness.initMyReviews();
        });
    </script>
<?php else:?>
    <?php $this->Html->scriptStart(array(
        'inline' => false, 
        'domReady' => true, 
        'requires' => array('jquery', 'mooBusiness'), 
        'object' => array('$', 'mooBusiness')
    ));?>
        mooBusiness.initMyReviews();
    <?php $this->Html->scriptEnd(); ?>
<?php endif?>

<?php if($reviews != null):?>
    <?php foreach($reviews as $review):?>
        <?php echo $this->Element('misc/my_review_item', array(
            'review' => $review
        ));?>
    <?php endforeach; ?>
    <?php if(count($reviews) == Configure::read('Business.business_review_per_page')):?>
        <?php $this->Html->viewMore($more_url, 'my_review_content') ?>
    <?php endif;?>
<?php else:?>
    <li><?php echo __d('business', 'No reviews found');?></li>
<?php endif;?>