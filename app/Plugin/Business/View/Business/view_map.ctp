<?php isset($direction) ? $direction = $direction: $direction = ''; ?>
<div class="title-modal">
    <?php if($direction):?>
        <?php echo __d('business', 'Get Directions');?>
    <?php else:?>
        <?php echo __d('business', 'Map');?>
    <?php endif;?>
    <button class="close" data-dismiss="modal" type="button">
        <span aria-hidden="true">×</span>
    </button>
</div>
<div class="modal-body">
    <?php if(!empty($empty_address)):?>
        <?php echo $empty_address;?>
    <?php else:?>
        <iframe
            width="100%"
            <?php if($direction):?>
            height="550px"
            <?php else:?>
            height="300px"
            <?php endif;?>
            frameborder="0" style="border:0"
            src="<?php echo $url.'load_map/?address='.urlencode($address).'&lat='.$lat.'&lng='.$lng.'&direction='.$direction;?>" allowfullscreen>
        </iframe>
    <?php endif;?>
    <br/><br/>
    <button class="button" href="javascript:void(0)" data-dismiss="modal">
        <?php echo __d('business', 'Close');?>
    </button>
</div>