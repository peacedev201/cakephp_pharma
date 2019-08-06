<?php 
    $stickerHelper = MooCore::getInstance()->getHelper('Sticker_Sticker');
    if(!empty($sticker_image)):
        $sticker_image = $sticker_image['StickerImage'];
?>
    <div class="sticker_animation" style="background-image: url('<?php echo $stickerHelper->getStickerImage($sticker_image);?>'); background-size: <?php echo $sticker_image['width'];?>px <?php echo $sticker_image['height'];?>px;<?php if($sticker_image['block'] == 1 && $sticker_image['quantity'] == 1):?>background-position: center center;<?php endif;?>" data-id="<?php echo $sticker_image['id'];?>" data-block="<?php echo $sticker_image['block'];?>" data-quantity="<?php echo $sticker_image['quantity'];?>" data-interval="<?php echo (int)$sticker_image['animation_interval'] > 0 ? $sticker_image['animation_interval'] : Configure::read('Sticker.sticker_animation_interval');?>"></div>
<?php endif;?>