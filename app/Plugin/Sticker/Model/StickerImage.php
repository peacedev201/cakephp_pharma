<?php
class StickerImage extends StickerAppModel {
    public $useTable = 'sticker_sticker_images';
    
    public function loadImages($params = array())
    {
        $cond = array();
        $order = array('StickerImage.ordering ASC', 'StickerImage.id ASC');
        if(!empty($params['sticker_id']))
        {
            $cond['StickerImage.sticker_sticker_id'] = $params['sticker_id'];
        }
        if(isset($params['enabled']))
        {
            $cond['StickerImage.enabled'] = $params['enabled'];
        }
        if(isset($params['enabled_sticker']))
        {
            $cond[] = 'StickerImage.sticker_sticker_id IN(SELECT id FROM '.$this->tablePrefix.'sticker_stickers WHERE enabled = 1)';
        }
        if(!empty($params['ids']))
        {
            $cond['StickerImage.id'] = is_array($params['ids']) ? $params['ids'] : explode(',', $params['ids']);
            $order = array('FIND_IN_SET(StickerImage.id, "'.(!is_array($params['ids']) ? $params['ids'] : implode(',', $params['ids'])).'")');
        }
        if(!empty($params['sticker_category_id']))
        {
            $cond['StickerImage.sticker_category_id'] = is_array($params['sticker_category_id']) ? $params['sticker_category_id'] : explode(',', $params['sticker_category_id']);
        }
        return $this->find('all', array(
            'conditions' => $cond,
            'order' => $order
        ));
    }
    
    public function loadImageList($params = array(), $field = "id", $value = "filename")
    {
        $cond = array();
        if(!empty($params['sticker_id']))
        {
            $cond['StickerImage.sticker_sticker_id'] = $params['sticker_id'];
        }
        return $this->find("list", array(
            "conditions" => $cond,
            "fields" => array("StickerImage.$field", "StickerImage.$value"),
            "order" => array("StickerImage.id DESC")
        ));
    }
    
    public function getDetail($id)
    {
        return $this->findById($id);
    }
}
