<?php
$stickerHelper = MooCore::getInstance()->getHelper('Sticker_Sticker');
$data = array(
    'sticker' => array(),
    'category' => array()
);
if($stickers != null)
{
    foreach($stickers as $sticker)
    {
        $sticker = $sticker['Sticker'];
        $data['sticker'][] = array(
            'id' => (int)$sticker['id'],
            'name' => (string)$sticker['name'],
            'image' => (string)$stickerHelper->getStickerIcon($sticker)
        );
    }
}
if($categories != null)
{
    foreach($categories as $category)
    {
        $category = $category['StickerCategory'];
        $data['category'][] = array(
            'id' => (int)$category['id'],
            'name' => (string)$category['name'],
            'background_color' =>  "#".(string)$category['background_color'],
            'image' => (string)$stickerHelper->getCategoryIcon($category)
        );
    }
}
echo json_encode($data);