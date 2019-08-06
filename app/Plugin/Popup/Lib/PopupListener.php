<?php
/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
App::uses('CakeEventListener','Event');
class PopupListener implements CakeEventListener
{
    public function implementedEvents()
    {
        return array(
            'MooView.beforeRender' => 'beforeRender',
            'StorageAmazon.photos.putObject.success.Popup' => 'storage_amazon_photo_put_success_callback',
        );
    }

    public function beforeRender($event)
    {
        //for test
        $this->clearSession(false);
        $page_id = $this->getPageID();
        $popups = MooCore::getInstance()->getModel('Popup.Popup');
        $popup_saves = MooCore::getInstance()->getModel('Popup.PopupSave');
        $flag_show = false;
        $popup = null;
        if($page_id !== -1)
        {
            //get all page
            $popup = $popups->find('first', array(
                'conditions' => array('Popup.page_id' => 0,'enable' => '1')
            ));
            if(empty($popup)){
                $popup = $popups->find('first', array(
                    'conditions' => array('Popup.page_id' => $page_id,'enable' => '1')
                ));
            }
            else{
                $flag_show = true;
            }
        }
        $view = $event->subject();
        if($popup){
            //check permision
            if($this->checkPermission($popup['Popup']['permission'],strval($view->viewVars['role_id'])) == true){
                //check session
                $uid = MooCore::getInstance()->getViewer(true);
                if($uid)
                {
                    $popup_save = $popup_saves->find('first',array(
                        'conditions' => array(
                            'user_id' => $uid,
                            'popup_id'=> $popup['Popup']['id']
                        ),
                    ));
                    if(empty($popup_save))
                    {
                        if($popup['Popup']['onetime'] == 1)
                        {
                            $data['user_id'] = $uid;
                            $data['popup_id'] = $popup['Popup']['id'];
                            $popup_saves->save($data);
                        }
                        $flag_show = true;
                    }
                    else
                    {
                        $flag_show = false;
                    }
                }
                else
                {
                    //check one time
                    if($popup['Popup']['onetime'] == 1){
                        $flag_found = false;
                        isset($_COOKIE["one_time"])? $one_times = json_decode($_COOKIE["one_time"]): $one_times = null;
                        if(!empty($one_times)){
                            foreach($one_times as $one_time){
                                if($one_time->id === $popup['Popup']['id']){
                                    if($one_time->value == 1){
                                        $flag_show = true;
                                    }
                                    else{
                                        $flag_show = false;
                                    }
                                    $flag_found = true;
                                }
                            }
                        }
                        if(!$flag_found){
                            $flag_show = true;
                            isset($_COOKIE["one_time"])? $data = json_decode($_COOKIE["one_time"]): $data = null;
                            $one_time['id'] = $popup['Popup']['id'];
                            $one_time['value'] = 0;
                            $data[] = $one_time;
                            setcookie('one_time', json_encode($data),time() + (86400 * 30), "/");
                        }

                    }
                    else{
                        //check popup option
                        isset($_COOKIE["popup_option"])? $popup_options = json_decode($_COOKIE["popup_option"]): $popup_options = null;
                        $flag_found = false;
                        if(!empty($popup_options)) {
                            foreach ($popup_options as $popup_option) {
                                if ($popup_option->id === $popup['Popup']['id']) {
                                    if ($popup_option->value == 1){
                                        $flag_show = true;
                                    }
                                    else
                                        $flag_show = false;
                                    $flag_found = true;
                                }
                            }
                        }
                        if(!$flag_found)
                            $flag_show = true;
                    }
                }

                if($flag_show){
                    echo $view->element('Popup.popup',array('popup'=>$popup));
                }
            }
        }
        //for test
        $this->checkSession(false);

        if ($view instanceof MooView) {
            $view->addPhraseJs(array(
                'drag_or_click_here_to_upload_photo' => __("Drag or click here to upload photo"),
            ));
        }

    }

    private function checkPermission($role,$roleId)
    {
        //Empty = Everyone
        if(empty($role))
            return true;
        //Get Role ID of user
        $res = strpos($role,$roleId);
        if($res === false)
            return false;
        else
            return true;
    }
    private function getPageID()
    {
        $request = Router::getRequest();
        $pages = MooCore::getInstance()->getModel('Page.Page');
        $action = $request['action'];
        if($action == "display"){
            $conditions = "{$request['controller']}.{$request->pass[0]}";
        }
        else{
            $conditions = "{$request['controller']}.{$action}";
        }
        if($conditions == "home.index")
        {
            if(!MooCore::getInstance()->getViewer(true))
                $conditions = "home.landing";
        }
        $page = $pages->find('first', array(
            'conditions' => array('Page.uri' => $conditions),
            'fields' => 'Page.id',
        ));
        if($page) {
            return $page['Page']['id'];
        }
        else {
            return -1;
        }
    }
    private function checkSession($active = false)
    {
        if($active){
            var_dump(json_encode($_COOKIE["popup_option"]));
            var_dump(json_encode($_COOKIE["one_time"]));
        }
    }
    private function clearSession($active = false)
    {
        if($active){
            CakeSession::delete('popup_option');
            CakeSession::delete('one_time'); die();
        }
    }

    public function storage_amazon_photo_put_success_callback($e){
        $photo = $e->data['photo'];
        $path= $e->data['path'];
        $url= $e->data['url'];
        if (Configure::read('Storage.storage_cloudfront_enable') == "1"){
            $url = rtrim(Configure::read('Storage.storage_cloudfront_cdn_mapping'),"/")."/".$e->data['key'];
        }
        $popupModel = MooCore::getInstance()->getModel('Popup.Popup');
        $popupModel->clear();
        $popup = $popupModel->find("first",array(
            'conditions' => array("Popup.id"=>$photo['Photo']['target_id']),
        ));
        if($popup){
            $findMe = str_replace(WWW_ROOT,"",$path);
            $isReplaced = false;
            $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
            if(preg_match_all("/$regexp/siU", $popup['Popup']['body'], $matches)) {
                foreach ($matches[2] as $match){
                    if(strpos($match, $findMe) !== false){
                        $isReplaced = true;
                        $popup['Popup']['body'] = str_replace($match,$url,$popup['Popup']['body']);
                    }
                }
            }
            $regexp = "<img\s[^>]*src=(\"??)([^\" >]*?)\\1[^>]*>";
            if(preg_match_all("/$regexp/siU", $popup['Popup']['body'], $matches)) {
                foreach ($matches[2] as $match){
                    if(strpos($match, $findMe) !== false){
                        $isReplaced = true;
                        $popup['Popup']['body'] = str_replace($match,$url,$popup['Popup']['body']);
                    }
                }
            }
            if($isReplaced){
                $popupModel->clear();
                $popupModel->save($popup);
            }
        }
        if (Configure::read('Storage.storage_current_type') == 'amazon' ) {
            CakeLog::write('storage', 'storage_amazon_putObject_success');
            $objectId = $e->data['oid'];
            $photoModel = MooCore::getInstance()->getModel('Photo.Photo');
            $photo = $photoModel->find("first",array(
                'conditions' => array("Photo.id"=>$objectId),
            ));
            if($photo){
                $event = new CakeEvent("StorageAmazon.photos.putObject.success.".$photo["Photo"]["type"], $this, array("photo"=>$photo,"key"=>$e->data['key'],"name"=>$e->data['name'],"url"=>$e->data['url'],"path"=>$e->data['path']));
                $this->getEventManager()->dispatch($event);
            }
        }

    }
}