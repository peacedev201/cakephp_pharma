<?php

App::uses('AppHelper', 'View/Helper');

class StickerHelper extends AppHelper {
    public $helpers = array('Storage.Storage');
    
    public function getEnable() {
        return Configure::check('Sticker.sticker_enabled') ? Configure::read('Sticker.sticker_enabled') : 0;
    }
    
    public function getCategoryIcon($item, $options = array())
    {
        if (isset($item['Category']))
        {
            $item = $item['Category'];
        }
        $request = Router::getRequest();
        $view = MooCore::getInstance()->getMooView();
        $prefix = '';
        if (isset($options['prefix']))
        {
            $prefix = $options['prefix'] . '_';
        }
        
        return $this->Storage->getUrl($item['id'], $prefix, $item['icon'], "sticker_category");
    }
    
    public function getStickerIcon($item, $options = array())
    {
        if (isset($item['Sticker']))
        {
            $item = $item['Sticker'];
        }
        $request = Router::getRequest();
        $view = MooCore::getInstance()->getMooView();
        $prefix = '';
        if (isset($options['prefix']))
        {
            $prefix = $options['prefix'] . '_';
        }
        
        return $this->Storage->getUrl($item['id'], $prefix, $item['id']."/".$item['icon'], "sticker_icon");
    }
    
    public function getStickerImage($item, $options = array())
    {
        if (isset($item['StickerImage']))
        {
            $item = $item['StickerImage'];
        }
        $request = Router::getRequest();
        $view = MooCore::getInstance()->getMooView();
        $prefix = '';
        if (isset($options['prefix']))
        {
            $prefix = $options['prefix'] . '_';
        }
        
        return $this->Storage->getUrl($item['id'], $prefix, $item['sticker_sticker_id']."/".$item['filename'], "sticker_image");
    }
    
    public function getStickerImageApp($item, $options = array())
    {
        if (isset($item['StickerImage']))
        {
            $item = $item['StickerImage'];
        }
        $request = Router::getRequest();
        $view = MooCore::getInstance()->getMooView();
        $prefix = '';
        if (isset($options['prefix']))
        {
            $prefix = $options['prefix'] . '_';
        }
        
        return $this->Storage->getUrl($item['id'], $prefix, $item['sticker_sticker_id']."/app/".$item['filename'], "sticker_image_app");
    }
    
    public function loadLanguage()
    {
        $mLanguage = MooCore::getInstance()->getModel('Language');
        return $mLanguage->find('all');
    }
    
    public function getStickermageTypeList($select = "")
    {
        $data = array(
            STICKER_IMAGE_TYPE_IMAGE => __d('sticker', 'Image'),
            STICKER_IMAGE_TYPE_ANIMATION => __d('sticker', 'Animation'),
            STICKER_IMAGE_TYPE_GIF => __d('sticker', 'Gif'),
        );
        if(!empty($select))
        {
            return isset($data[$select]) ? $data[$select] : "";
        }
        return $data;
    }
}
