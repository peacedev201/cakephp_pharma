<?php if($sticker_images != null):?>
    <?php foreach($sticker_images as $sticker_image):?>
        <?php echo $this->Element('Sticker.misc/sticker_animation', array(
            'sticker_image' => $sticker_image
        ));?>
    <?php endforeach;?>
<?php else:?>
    <div class="sticker_no_stickers"><?php echo __d('sticker', 'No Stickers to Show');?></div>
<?php endif;?>