<?php
/**
 * Created by PhpStorm.
 * User: QUOC-PC
 * Date: 10/08/2018
 * Time: 10:23 SA
 */
/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
App::uses('CakeEventListener', 'Event');

class AvatarListener implements CakeEventListener
{

    public function implementedEvents()
    {
        return array(
            //'Helper.getItemPhoto.url_image' => 'url_image',
            'UserController.doAfterRegister' => 'doAfterRegister',
            'UserController.doAfterStep2' => 'doAfterStep2',
            'Controller.User.afterEdit' => 'afterEdit',
            'Model.User.afterSave' => 'afterSave',
            'UserController.doCreateAvatar' => 'createAvatar'
        );
    }

    public function createAvatar($event)
    {
        $e = $event->subject();
        $data = $e['data'];
        if(!empty($data['name']))
        {
            $user = $this->save_new_avatar($data['id'],$data['name'],$data['specialty']);
            $event->result = $user;
        }
    }

    public function doAfterStep2($event)
    {
        $e = $event->subject();
        $data = $event->data;
        if(!empty($data['User']['name']))
        {
            $user = $this->save_new_avatar($data['User']['id'],$data['User']['name'], $data['User']['specialty'], $data['replace']);
            $event->result = $user;
        }
    }

    public function doAfterRegister($event)
    {
        $e = $event->subject();
        if(!isset($e->data['avatar']) || (isset($e->data['avatar']) && $e->data['avatar'] == ''))
        {
            if(!empty($e->data['name']))
            {
                $this->save_avatar($e->User->id,$e->data['name']);
            }
        }
    }

    public function afterEdit($event)
    {
        $e = $event->subject();
        $cuser = $event->data['item'];
        $md5 = md5($e->data['name']) . ".png";
        if(!empty($cuser['User']['avatar_letter']) && $cuser['User']['avatar_letter'] == 1 && $cuser['User']['avatar'] != $md5)
        {
            $this->save_avatar($cuser['User']['id'],$e->data['name']);
        }
    }

    public function afterSave($event)
    {
        $e = $event->subject();
        if(isset($e->data['User']['avatar']) && $e->data['User']['avatar'] != '')
        {
            $id = $e->id;
            $created = $e->created;
            $m_User = ClassRegistry::init('User');
            $user = $m_User->findById($id);
            if(!$created)
            {
                if(!empty($user))
                {
                    $md5 = md5($user['User']['name']) . ".png";
                    if($user['User']['avatar'] != $md5)
                    {
                        $data['avatar_letter'] = 0;
                        $m_User->id = $id;
                        $m_User->set($data);
                        $m_User->save();
                    }
                }
            }
        }
    }



    /**
     * @param null $id User ID
     * @param $name Name of user
     */
    private function save_avatar($id = null, $name)
    {
        if($id != null)
        {
            App::import('Avatar.Lib', 'AvatarGenerator');
            $tmp_name = AvatarGenerator::save_avatar_tmp($name, 600);
            if($tmp_name)
            {
                $m_User = MooCore::getInstance()->getModel('User');
                $m_User->id = $id;
                $data['avatar'] = "uploads/tmp/" . $tmp_name. ".png";
                $data['avatar_letter'] = 1;
                $m_User->set($data);
                $m_User->save();
            }
        }
    }

    private function save_new_avatar($id = null, $name, $specialty = 0, $replace = 0)
    {
        $user = array();
        if($id != null)
        {
            App::import('Avatar.Lib', 'AvatarGenerator');
            $tmp_name = AvatarGenerator::save_avatar_tmp($name, 600, $specialty);
            if($tmp_name)
            {
                $img_path = "uploads/tmp/" . $tmp_name. ".png";
                if($replace){
                    $file = $img_path;
                    $epl = explode('.', $file);
                    $extension = $epl[count($epl) - 1];
                    $new_tmp_name = md5(uniqid());
                    $newTmpAvatar = WWW_ROOT . 'uploads' . DS . 'tmp' . DS . $new_tmp_name . '.' . $extension;
                    copy(WWW_ROOT . $file, $newTmpAvatar);
                    $newTmpAvatar = 'uploads' . DS . 'tmp' . DS . $new_tmp_name . '.' . $extension;
                }
                $m_User = MooCore::getInstance()->getModel('User');
                $m_User->clear();
                $data_save = array();
                $data_save['id'] = $id;

                $data_save['default_avatar'] = $img_path;
                if(!empty($newTmpAvatar)){
                    $data_save['avatar'] = $newTmpAvatar;
                }
                $m_User->save($data_save);
                $user = $m_User->read();
            }
        }
        return $user;
    }
}