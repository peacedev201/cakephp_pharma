<?php if(!$is_favourite):?>
    <a href="javascript:void(0)" class="add_favourite show_when_hover" data-id="<?php echo $business_id; ?>" title="<?php echo __d('business', 'Add to favorite');?>">
        <i class="material-icons favourite">favorite</i>
    </a>
<?php else:?> 
    <a href="javascript:void(0)" class="add_favourite show_when_hover" data-id="<?php echo $business_id; ?>" title="<?php echo __d('business', 'Remove from favorite');?>">
        <i class="material-icons unfavourite">favorite</i>
    </a>
<?php endif;?>