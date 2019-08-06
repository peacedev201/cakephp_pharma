<?php
$stickerHelper = MooCore::getInstance()->getHelper('Sticker_Sticker');
$data = array();
if($sticker_images != null)
{
    foreach($sticker_images as $sticker_image)
    {
        $sticker_image = $sticker_image['StickerImage'];
        $data[] = array(
            'id' => (int)$sticker_image['id'],
            'width' => (int)$sticker_image['width'],
            'height' => (int)$sticker_image['height'],
            'block' => (int)$sticker_image['block'],
            'quantity' => (int)$sticker_image['quantity'],
            'animation_interval' => (int)($sticker_image['animation_interval'] > 0 ? $sticker_image['animation_interval'] : Configure::read('Sticker.sticker_animation_interval')),
            'image' => (string)$stickerHelper->getStickerImageApp($sticker_image)
        );
        
    }
}
echo json_encode($data);