<?php 
App::uses('AppController', 'Controller');
class GiftAppController extends AppController{
    public $check_force_login = true;

    public function beforeFilter() {
        if(isset($this->params['prefix']) && $this->params['prefix'] == 'admin')
        {
            $this->_checkPermission(array('super_admin' => 1));
        }

        if (Configure::read("Gift.gift_consider_force"))
        {
            $this->check_force_login = false;
        }

        parent::beforeFilter();
        
        $mGift = MooCore::getInstance()->getModel("Gift.Gift");
        $this->permission_can_send_gift = $this->giftPermission(GIFT_PERMISSION_CAN_SEND_GIFT);
        $this->permission_allow_photo_gift = $this->giftPermission(GIFT_PERMISSION_ALLOW_PHOTO_GIFT);
        $this->permission_allow_audio_gift = $this->giftPermission(GIFT_PERMISSION_ALLOW_AUDIO_GIFT);
        $this->permission_allow_video_gift = $this->giftPermission(GIFT_PERMISSION_ALLOW_VIDEO_GIFT);
        $this->permission_can_create_gift = false;
        $this->is_ffmpeg_installed = $mGift->checkFfmpeg(Configure::read('Gift.gift_path_to_ffmpeg'));
        if($this->permission_allow_photo_gift || $this->permission_allow_audio_gift || 
          ($this->permission_allow_video_gift && $this->is_ffmpeg_installed))
        {
            $this->permission_can_create_gift = true;
        }

        $this->set(array(
            'permission_can_send_gift' => $this->permission_can_send_gift,
            'permission_allow_photo_gift' => $this->permission_allow_photo_gift,
            'permission_allow_audio_gift' => $this->permission_allow_audio_gift,
            'permission_allow_video_gift' => $this->permission_allow_video_gift,
            'permission_can_create_gift' => $this->permission_can_create_gift,
            'is_ffmpeg_installed' => $this->is_ffmpeg_installed
        ));
    }
    
    protected function _redirectError($msg, $url)
    {
        if($msg != null)
        {
            $this->Session->setFlash($msg, 'default', array(
                'class' => 'Metronic-alerts alert alert-danger fade in'
            ));
        }
        $this->redirect($url);
    }
    
    protected function _redirectSuccess($msg, $url)
    {
        if($msg != null)
        {
            $this->Session->setFlash($msg, 'default', array(
                'class' => 'Metronic-alerts alert alert-success fade in'
            ));
        }
        $this->redirect($url);
    }

    protected function _jsonSuccess($msg, $flashMsg = false, $params = null)
    {
        $this->autoRender = false;
        if($flashMsg)
        {
            $this->Session->setFlash($msg, 'default', array(
                'class' => 'Metronic-alerts alert alert-success fade in'
            ));
        }
        $data = array(
            'result' => 1,
            'message' => $msg
        );
        if($params != null)
        {
            $data = array_merge($data, $params);
        }
        echo json_encode($data);
        exit;
    }
    
    protected function _jsonError($msg, $flashMsg = false, $params = null)
    {
        $this->autoRender = false;
        if($flashMsg)
        {
            $this->Session->setFlash($msg, 'default', array(
                'class' => 'Metronic-alerts alert alert-danger fade in'
            ));
        }
        $data = array(
            'result' => 0,
            'message' => $msg
        );
        if($params != null)
        {
            $data = array_merge($data, $params);
        }
        
        echo json_encode($data);
        exit;;
    }
    
    /*public function checkGiftPermission($permission, $user_id = null)
    {
        $userParams = $this->_getUserRoleParams();
        switch($permission)
        {
            case 'gift_create_gift':
                if(MooCore::getInstance()->getViewer(true) > 0)
                {
                    return true;
                }
                return false;
                break;
        }
        if($userParams != null && in_array($permission, $userParams))
        {
            if($user_id != null && $user_id != MooCore::getInstance()->getViewer(true))
            {
                return false;
            }
            return true;
        }
        return false;
    }*/
    
    public function giftPermission($value)
    {
        $role = $this->_getUserRoleParams();
        if(!empty($role))
        {
            if(!is_array($role) && $role == 'all')
            {
                return true;
            }
            if(in_array($value, $role))
            {
                return true;
            }
        }
        return false;
    }
}