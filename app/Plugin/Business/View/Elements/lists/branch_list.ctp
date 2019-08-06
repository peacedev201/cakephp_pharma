<?php
echo $this->Html->css(array(
    'Business.star-rating'
    ), array('block' => 'css', 'minify'=>false));
?>
<?php if($this->request->is('ajax')):?>
<script type="text/javascript">
    require(["jquery","mooBusiness"], function($,mooBusiness) {
        mooBusiness.initReviewStar();
    });
</script>
<?php endif?>

<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'mooBusiness'), 
    'object' => array('$', 'mooBusiness')
));?>
    mooBusiness.initReviewStar();
<?php $this->Html->scriptEnd(); ?>
    
<?php if ($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery", "mooBehavior", "mooBusiness"], function($, mooBehavior, mooBusiness) {
        mooBehavior.initMoreResults();
    });
</script>
<?php endif?>
    
<?php if($branches != null):?>
    <?php foreach($branches as $branch):?>
        <?php echo $this->Element('Business.misc/business_item', array(
            'business' => $branch,
        ));?>
    <?php endforeach; ?>
    <?php if(count($branches) == Configure::read('Business.business_branch_per_page')):?>
        <?php $this->Html->viewMore($more_url, 'branches-content') ?>
    <?php endif;?>
<?php else:?>
<li>
    <?php echo __d('business', 'No sub pages found');?>
</li>
<?php endif;?>