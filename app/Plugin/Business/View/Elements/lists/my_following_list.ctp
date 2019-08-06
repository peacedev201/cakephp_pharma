<?php
echo $this->Html->css(array(
    'Business.star-rating'
    ), array('block' => 'css', 'minify'=>false));
?>
<?php if($this->request->is('ajax')):?>
<script type="text/javascript">
    require(["jquery","mooBusiness"], function($,mooBusiness) {
        mooBusiness.initBusinessItem();
        mooBusiness.initFollow();
    });
</script>
<?php endif?>

<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'mooBusiness'), 
    'object' => array('$', 'mooBusiness')
));?>
    mooBusiness.initBusinessItem();
    mooBusiness.initFollow();
<?php $this->Html->scriptEnd(); ?>

<?php if($businesses != null):?>
    <?php foreach($businesses as $business):?>
        <?php echo $this->Element('Business.misc/business_item', array(
            'business' => $business,
            'show_list_option_follow' => true
        ));?>
    <?php endforeach; ?>
    <?php if(count($businesses) == Configure::read('Business.business_search_item_per_page')):?>
        <?php $this->Html->viewMore($more_url, 'list-content') ?>
    <?php endif;?>
<?php else:?>
    <li><?php echo __d('business', 'No businesses found');?></li>
<?php endif;?>
