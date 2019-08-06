<?php if($businesses != null):
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business'); 
?>
<div class="box2 filter_block recent-biz-recent-added">
    <h3><?php echo __d('business', 'People also viewed');?></h3>
    <div class="box_content">
        <ul class="list_biz_block">
            <?php foreach($businesses as $business):?>
                <?php echo $this->Element('Business.misc/business_item', array(
                    'business' => $business
                ));?>
            <?php endforeach;?>
        </ul>
    </div>
</div>
<?php endif;?>