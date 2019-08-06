<?php
echo $this->Html->css(array(
    'Business.star-rating'
    ), array('block' => 'css', 'minify'=>false));
?>
<?php if($this->request->is('ajax')):?>
<script type="text/javascript">
    require(["jquery","mooBusiness"], function($,mooBusiness) {
        mooBusiness.initBusinessItem();
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
<?php $this->Html->scriptEnd(); ?>
    
<?php if($businesses != null):?>
    <?php foreach($businesses as $k => $business):?>
        <?php echo $this->Element('Business.misc/business_item', array(
            'business' => $business,
            'show_list_option' => isset($show_list_option) ? $show_list_option : false,
            'show_status' => isset($show_status) ? $show_status : false,
            'show_list_option_favourite' => isset($show_list_option_favourite) ? $show_list_option_favourite : false,
            'show_list_option_follow' => isset($show_list_option_follow) ? $show_list_option_follow : false,
            'index' => isset($index) && $index == true ? $k : false,
        ));?>
    <?php endforeach; ?>
    <?php if((empty($paging) || $paging == false) && count($businesses) == Configure::read('Business.business_search_item_per_page')):?>
        <?php $this->Html->viewMore($more_url, 'my_business_content') ?>
    <?php endif;?>
<?php else:?>
    <li>
        <?php echo __d('business', 'No businesses found');?>
    </li>
<?php endif;?>