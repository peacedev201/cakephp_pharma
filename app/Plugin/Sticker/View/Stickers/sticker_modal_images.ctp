<?php if($sticker_images != null):?>
    <?php foreach($sticker_images as $sticker_image):
        $sticker_image = $sticker_image['StickerImage'];
    ?>
    <div class="sticker_animation" style="background-image: url('<?php echo $this->Sticker->getStickerImage($sticker_image);?>'); background-size: <?php echo $sticker_image['width'];?>px <?php echo $sticker_image['height'];?>px;<?php if($sticker_image['block'] == 1 && $sticker_image['quantity'] == 1):?>background-position: center center;<?php endif;?>" data-block="<?php echo $sticker_image['block'];?>" data-quantity="<?php echo $sticker_image['quantity'];?>"></div>
    <?php endforeach;?>
<?php endif;?>