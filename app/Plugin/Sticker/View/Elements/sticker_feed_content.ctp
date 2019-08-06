<?php if($sticker_image != null):?>
    <div class="sticker_activity_item">
        <?php 
            echo $this->Element('Sticker.misc/sticker_animation', array(
                'sticker_image' => $sticker_image
            ));
        ?>
    </div>
<?php endif;?>