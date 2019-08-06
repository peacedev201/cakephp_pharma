<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
App::uses('CakeEventListener', 'Event');

class StickerApiListener implements CakeEventListener {

    public function implementedEvents()
    {
        return array(
            'ApiHelper.AfterRenderApiFeedObject' => 'AfterRenderApiFeedObject',
            'Api.View.ApiComment.afterRenderApiComment' => 'afterRenderApiComment',
            'ApiHelper.afterRenderFirstComment' => 'afterRenderFirstComment'
        );
    }
    
    public function AfterRenderApiFeedObject($e)
    {
        $data = $e->data['data'];
        $feed = $e->data['feed'];
        if($data['Activity']['action'] != 'photos_add' && $data['Activity']['item_type'] != 'Photo_Album' &&
           !empty($data['Activity']['sticker_image_id']) && !empty($feed['items']['objects']))
        {
            $apiHelper = MooCore::getInstance()->getHelper('Api_Api');
            list($tagUser, $content, $contentHtml) = $apiHelper->getContentAndTagUser($data);
            $sticker_html = $contentHtml.$this->parseStickerAnimationHtml($data['Activity']['sticker_image_id']);
            if($sticker_html != null)
            {
                $e->data['feed']['items']['objects']['contentHtml'] = $sticker_html;
                $e->result['feed'] = $e->data['feed'];
            }
        }
    }
    
    public function afterRenderFirstComment($e)
    {
        $data = $e->data['data'];
        $data_json = $e->data['data_json'];
        if(isset($data['Comment']['sticker_image_id']) || isset($data['sticker_image_id']))
        {
            $sticker_image_id = 0;
            if(isset($data['sticker_image_id']))
            {
                $sticker_image_id = $data['sticker_image_id'];
            }
            else if(isset($data['Comment']['sticker_image_id']))
            {
                $sticker_image_id = $data['Comment']['sticker_image_id'];
            }
            $sticker_html = $this->parseStickerAnimationHtml($sticker_image_id, true);
            if($sticker_html != null)
            {
                $e->result['result'] = array(
                    'message' => $data_json['message'].$sticker_html
                );
            }
        }
    }
    
    public function afterRenderApiComment($e)
    {
        $data = $e->data['data'];
        $data_json = $e->data['data_json'];
        if(isset($data['Comment']['sticker_image_id']) || isset($data['sticker_image_id']))
        {
            $sticker_image_id = 0;
            if(isset($data['sticker_image_id']))
            {
                $sticker_image_id = $data['sticker_image_id'];
            }
            else if(isset($data['Comment']['sticker_image_id']))
            {
                $sticker_image_id = $data['Comment']['sticker_image_id'];
            }
            $sticker_html = $this->parseStickerAnimationHtml($sticker_image_id, true);
            if($sticker_html != null)
            {
                $e->data['data_json']['message'] = $data_json['message'].$sticker_html;
                /*$e->result['result'] = array(
                    'message' => $data_json['message'].$sticker_html
                );*/
            }
        }
    }
    
    private function parseStickerAnimationHtml($sticker_image_id, $is_comment = false)
    {
        $mStickerImage = MooCore::getInstance()->getModel('Sticker.StickerImage');
        $stickerHelper = MooCore::getInstance()->getHelper('Sticker_Sticker');
        $sticker_image = $mStickerImage->getDetail($sticker_image_id);
        $html = '';
        if($sticker_image != null)
        {
            $sticker_image = $sticker_image['StickerImage'];
            $style_center = "";
            if(!$is_comment)
            {
                $style_center = "margin: auto;";
            }
            $html = '<div class="sticker_animation" style="width: 80px;height: 80px;image-rendering: -webkit-optimize-contrast;background-repeat: no-repeat;'.$style_center.'background-image: url(\''.$stickerHelper->getStickerImage($sticker_image).'\'); background-size: '.$sticker_image['width'].'px '.$sticker_image['height'].'px;'.(($sticker_image['block'] == 1 && $sticker_image['quantity'] == 1) ? "background-position: center center;" : "").'" data-id="'.$sticker_image['id'].'" data-block="'.$sticker_image['block'].'" data-quantity="'.$sticker_image['quantity'].'" data-interval="'.((int)$sticker_image['animation_interval'] > 0 ? $sticker_image['animation_interval'] : Configure::read('Sticker.sticker_animation_interval')).'"></div>';
        }
        return $html;
    }
}
