<?php
App::uses('CakeEventListener', 'Event');

class GiftListener implements CakeEventListener
{
    public function implementedEvents()
    {
        return array(        	
        	'Controller.Comment.afterComment' => 'afterComment',
            'Controller.Search.search' => 'search',
            'Controller.Search.suggestion' => 'suggestion',
            'Controller.Search.hashtags' => 'hashtags',
            'Controller.Search.hashtags_filter' => 'hashtags_filter',
            'MooView.beforeRender' => 'beforeRender',
            'UserController.deleteUserContent' => 'deleteUserContent',
            'Controller.Home.adminIndex.Statistic' => 'statistic',
            'Plugin.View.Api.Search' => 'apiSearch',

            'StorageHelper.gifts.getUrl.local' => 'storage_geturl_local',
        	'StorageHelper.gifts.getUrl.amazon' => 'storage_geturl_amazon',
        	'StorageAmazon.gifts.getFilePath' => 'storage_amazon_get_file_path',

            'StorageHelper.gift_files.getUrl.local' => 'storage_geturl_local',
            'StorageHelper.gift_files.getUrl.amazon' => 'storage_geturl_amazon',
            'StorageAmazon.gift_files.getFilePath' => 'storage_amazon_get_file_path',

        	'StorageTaskAwsCronTransfer.execute' => 'storage_task_transfer',
            'StorageAmazon.gift_files.putObject.success' => 'storage_amazon_gift_files_put_success_callback',
        );
    }

    public function storage_amazon_gift_files_put_success_callback($e)
    {
        $path = $e->data['path'];
        if (Configure::read('Storage.storage_amazon_delete_image_after_adding') == "1")
        {   //CakeLog::write('storage', $path);
            if ($path)
            {
                $file = new File($path);
                $file->delete();
                $file->close();
            }
        }
    }

    public function storage_geturl_local($e)
    {
        $v = $e->subject();
        $request = Router::getRequest();
        $oid = $e->data['oid'];
        $type = $e->data['type'];
        $thumb = $e->data['thumb'];
        $prefix = $e->data['prefix'];

        if ($type == 'gifts')
        {
            if ($e->data['thumb']) {
                if($prefix == 'thumb'){
                    $url = FULL_BASE_LOCAL_URL . $request->webroot . 'uploads/gifts/'. $prefix .'/'. $thumb;
                }else{
                    $url = FULL_BASE_LOCAL_URL . $request->webroot . 'uploads/gifts/'. $prefix . $thumb;
                }
            } else {
                //$url = FULL_BASE_LOCAL_URL . $v->assetUrl('Blog.noimage/blog.png', array('prefix' => rtrim($prefix, "_"), 'pathPrefix' => Configure::read('App.imageBaseUrl')));
                $url = $v->getImage("img/noimage/noimage-album.png");
            }
        }
        else
        {
            $url = FULL_BASE_LOCAL_URL . $request->webroot . 'uploads/gifts/'. $thumb;
        }
        $e->result['url'] = $url;
    }

    public function storage_geturl_amazon($e)
    {
        $v = $e->subject();
        $type = $e->data['type'];
        if ($type =='gifts')
        {
            $e->result['url'] = $v->getAwsURL($e->data['oid'], "gifts", $e->data['prefix'], $e->data['thumb']);
        }
        else
        {
            $e->result['url'] = $v->getAwsURL($e->data['oid'], "gift_files", $e->data['prefix'], $e->data['thumb']);
        }
    }

    public function storage_amazon_get_file_path($e)
    {
        $objectId = $e->data['oid'];
        $name = $e->data['name'];
        $thumb = $e->data['thumb'];
        $type = $e->data['type'];

        $path = false;
        if ($type == 'gifts')
        {
            if (!empty($thumb)) {
                if($name == 'thumb'){
                    $path = WWW_ROOT . "uploads" . DS . "gifts" . DS .  $name . DS . $thumb;
                }else{
                    $path = WWW_ROOT . "uploads" . DS . "gifts" . DS . $name . $thumb;
                }
            }
        }
        else
        {
            $path = WWW_ROOT . "uploads" . DS . "gifts" . DS . $thumb;
        }

        $e->result['path'] = $path;
    }

    public function storage_task_transfer($e)
    {
        $v = $e->subject();
        $giftModel = MooCore::getInstance()->getModel('Gift.Gift');
        $gifts = $giftModel->find('all', array(
                'conditions' => array("Gift.id > " => $v->getMaxTransferredItemId("gifts")),
                'limit' => 10,
                'order' => array('Gift.id'),
            )
        );

        if($gifts){
            foreach($gifts as $gift){
                if($gift["Gift"]['type'] == 'photo') {
                    $v->transferObject($gift["Gift"]['id'], "gifts", '', $gift["Gift"]["filename"]);
                }else {
                    $v->transferObject($gift["Gift"]['id'], "gift_files", '', $gift["Gift"]["filename"]);
                }
            }
        }
    }
    
    //used to update comment count
    public function afterComment($event)
    {
        $data = $event->data['data'];
        $target_id = isset($data['target_id']) ? $data['target_id'] : null;
        $type = isset($data['type']) ? $data['type'] : '';
        if ($type == 'Gift_Gift' && !empty($target_id))
        {
            $mGift = MooCore::getInstance()->getModel('Gift.Gift');
            Cache::clearGroup('gift', 'gift');
            $mGift->updateCounter($target_id);
        }
    }

    public function search($event)
    {
        if(Configure::read('Gift.gift_enabled'))
        {
            $e = $event->subject();
            $mGift = MooCore::getInstance()->getModel('Gift.Gift');
            $results = $mGift->getListGifts('search', 1, 0, $e->keyword);

            $event->result['Gift']['header'] = __d('gift', 'Gift');
            $event->result['Gift']['icon_class'] = "card_giftcard";
            $event->result['Gift']['view'] = "search_gift_list";
            $event->result['Gift']['notEmpty'] = 1;
            $e->set('gifts', $results);
        }
    }

    public function apiSearch($event)
    {
        $view = $event->subject();
        $items = &$event->data['items'];
        $type = $event->data['type'];
        $viewer = MooCore::getInstance()->getViewer();
        $utz = $viewer['User']['timezone'];
        if ($type == 'Gift' && isset($view->viewVars['gifts']) && count($view->viewVars['gifts']))
        {
            $helper = MooCore::getInstance()->getHelper('Gift_Gift');
            foreach ($view->viewVars['gifts'] as $item){
                $items[] = array(
                    'id' => $item["Gift"]['id'],
                    'url' => FULL_BASE_URL.$item['Gift']['moo_href'],
                    'avatar' =>  $helper->getImage($item['Gift']),
                    'owner_id' => $item["Gift"]['user_id'],
                    'title_1' => $item["Gift"]['moo_title'],
                    'title_2' => $view->Moo->getTime( $item["Gift"]['created'], Configure::read('core.date_format'), $utz ),
                    'created' => $item["Gift"]['created'],
                    'type' => "Gift",
                    'type_title' => __d('gift',"Gift")
                );
            }
        }
    }
    
    public function suggestion($event)
    {
        if(Configure::read('Gift.gift_enabled'))
        {
            $event->result['gift']['header'] = __d('gift', 'Gift');
            $event->result['gift']['icon_class'] = 'card_giftcard';

            $e = $event->subject();
            $mGift = MooCore::getInstance()->getModel('Gift.Gift');
            if(isset($event->data['type']) && $event->data['type'] == 'all')
            {
                $helper = MooCore::getInstance()->getHelper('Gift_Gift');
                $gifts = $mGift->getListGifts('search', 1, 0, $e->request->data['searchVal']);
                foreach($gifts as $index => &$detail)
                {
                    $event->result['gift'][$index]['id'] = $detail['Gift']['id'];

                    $event->result['gift'][$index]['img'] = $helper->getImage($detail['Gift'],array('prefix' => 'thumb'));

                    $event->result['gift'][$index]['title'] = $detail['Gift']['title'];
                    $event->result['gift'][$index]['find_name'] = __d('gift', 'Find Gift');
                    $event->result['gift'][$index]['icon_class'] = 'icon-edit-1';
                    $event->result['gift'][$index]['view_link'] = 'gifts/view/';
                    
                    $mooHelper = MooCore::getInstance()->getHelper('Core_Moo');
                    
                    $utz = ( !is_numeric(Configure::read('core.timezone')) ) ? Configure::read('core.timezone') : 'UTC';
                    $cuser = MooCore::getInstance()->getViewer();
                    // user timezone
                    if ( !empty( $cuser['User']['timezone'] ) ){
                        $utz = $cuser['User']['timezone'];
                    }
                    
                    $event->result['gift'][$index]['more_info'] = $mooHelper->getTime( $detail['Gift']['created'], Configure::read('core.date_format'), $utz );
                }
            }
            else if(isset($event->data['type']) && $event->data['type'] == 'gift')
            {
                $results = $mGift->getListGifts('search', 1, 0, $e->request->pass[1]);
                $e->set('gifts', $results);
                $e->set('element_list_path',"Gift.search_gift_list");
                
                $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;
                $gifts = $mGift->getListGifts('search', $page, 0, $e->request->pass[1]);
                $e->set('gifts', $gifts);
                $e->set('result',1);
                $e->set('more_url','/search/suggestion/gift/'.$e->request->pass[1]. '/page:' . ( $page + 1 ));
                $e->set('element_list_path',"Gift.search_gift_list");
            }
        }
    }
    
    public function hashtags($event)
    {
        $enable = Configure::read('Gift.gift_hashtag_enabled');
        $gifts = array();
        $e = $event->subject();
        App::import('Model', 'Gift.Gift');
        App::import('Model', 'Tag');
        $this->Tag = new Tag();
        $this->Gift = new Gift();
        $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;

        $uid = CakeSession::read('uid');
        if($enable)
        {
            $gifts = $this->Gift->getGiftHashtags($event->data['search_keyword'],RESULTS_LIMIT,$page);
            $gifts = $this->_filterGift($gifts);
            /*if(isset($event->data['type']) && $event->data['type'] == 'gifts')
            {
                $gifts = $this->Gift->getGiftHashtags($event->data['item_ids'],RESULTS_LIMIT,$page);
                $gifts = $this->_filterGift($gifts);
            }
            $table_name = $this->Gift->table;
            if(isset($event->data['type']) && $event->data['type'] == 'all' && !empty($event->data['item_groups'][$table_name]) )
            {
                $gifts = $this->Gift->getGiftHashtags($event->data['item_groups'][$table_name],5);
                $gifts = $this->_filterGift($gifts);
            }*/
        }
        // get tagged item
        $tag = h(urldecode($event->data['search_keyword']));
        $tags = $this->Tag->find('all', array('conditions' => array(
            'Tag.type' => 'Gift_Gift',
            'Tag.tag' => $tag
        )));
        $gift_ids = Hash::combine($tags,'{n}.Tag.id', '{n}.Tag.target_id');

        $friendModel = MooCore::getInstance()->getModel('Friend');

        $items = $this->Gift->find('all', array('conditions' => array(
                'Gift.id' => $gift_ids
            ),
            'limit' => RESULTS_LIMIT,
            'page' => $page
        ));

        $viewer = MooCore::getInstance()->getViewer();

        foreach ($items as $key => $item){
            $owner_id = $item[key($item)]['user_id'];
            $privacy = isset($item[key($item)]['privacy']) ? $item[key($item)]['privacy'] : 1;
            if (empty($viewer)){ // guest can view only public item
                if ($privacy != PRIVACY_EVERYONE){
                    unset($items[$key]);
                }
            }else{ // viewer
                $aFriendsList = array();
                $aFriendsList = $friendModel->getFriendsList($owner_id);
                if ($privacy == PRIVACY_ME){ // privacy = only_me => only owner and admin can view items
                    if (!$viewer['Role']['is_admin'] && $viewer['User']['id'] != $owner_id){
                        unset($items[$key]);
                    }
                }else if ($privacy == PRIVACY_FRIENDS){ // privacy = friends => only owner and friendlist of owner can view items
                    if (!$viewer['Role']['is_admin'] && $viewer['User']['id'] != $owner_id && !in_array($viewer['User']['id'], array_keys($aFriendsList))){
                        unset($items[$key]);
                    }
                }else {

                }
            }
        }
        if($items != null)
        {
            $gifts = array_merge($gifts, $items);
        }

        //only display 5 items on All Search Result page
        if($gifts != null && isset($event->data['type']) && $event->data['type'] == 'all')
        {
            $gifts = array_slice($gifts,0,5);
        }
        if($gifts != null)
        {
            $gifts = array_map("unserialize", array_unique(array_map("serialize", $gifts)));
        }
        if(!empty($gifts))
        {
            $event->result['gifts']['header'] = 'Gifts';
            $event->result['gifts']['icon_class'] = 'icon-edit';
            $event->result['gifts']['view'] = "Gift.search_gift_list";
            if(isset($event->data['type']) && $event->data['type'] == 'gifts')
            {
                $e->set('result',1);
                $e->set('more_url','/search/hashtags/'.$e->params['pass'][0]. '/gifts/page:' . ( $page + 1 ));
                $e->set('element_list_path',"Gift.search_gift_list");
            }
            $e->set('gifts', $gifts);
        }
    }
    
    public function hashtags_filter($event){
         
        $e = $event->subject();
        App::import('Model', 'Gift.Gift');
        $this->Gift = new Gift();

        if(isset($event->data['type']) && $event->data['type'] == 'gifts')
        {
            $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;
            $gifts = $this->Gift->getGiftHashtags($event->data['item_ids'],RESULTS_LIMIT,$page);
            $e->set('gifts', $gifts);
            $e->set('result',1);
            $e->set('more_url','/search/hashtags/'.$e->params['pass'][0]. '/gifts/page:' . ( $page + 1 ));
            $e->set('element_list_path',"Gift.search_gift_list");
        }
        $table_name = $this->Gift->table;
        if(isset($event->data['type']) && $event->data['type'] == 'all' && !empty($event->data['item_groups'][$table_name]) )
        {
            $event->result['gifts'] = null;

            $gifts = $this->Gift->getGiftHashtags($event->data['item_groups'][$table_name],5);

            if(!empty($gifts))
            {
                $event->result['gifts']['header'] = 'Gifts';
                $event->result['gifts']['icon_class'] = 'icon-edit';
                $event->result['gifts']['view'] = "Gift.search_gift_list";
                $e->set('gifts', $gifts);
            }
        }
    }

    private function _filterGift($gifts)
    {
        if(!empty($gifts))
        {
            $friendModel = MooCore::getInstance()->getModel('Friend');
            $viewer = MooCore::getInstance()->getViewer();
            foreach($gifts as $key => &$gift)
            {
                $owner_id = $gift[key($gift)]['user_id'];
                $privacy = isset($gift[key($gift)]['privacy']) ? $gift[key($gift)]['privacy'] : 1;
                if (empty($viewer)){ // guest can view only public item
                    if ($privacy != PRIVACY_EVERYONE){
                        unset($gifts[$key]);
                    }
                }else{ // viewer
                    $aFriendsList = array();
                    $aFriendsList = $friendModel->getFriendsList($owner_id);
                    if ($privacy == PRIVACY_ME){ // privacy = only_me => only owner and admin can view items
                        if (!$viewer['Role']['is_admin'] && $viewer['User']['id'] != $owner_id){
                            unset($gifts[$key]);
                        }
                    }else if ($privacy == PRIVACY_FRIENDS){ // privacy = friends => only owner and friendlist of owner can view items
                        if (!$viewer['Role']['is_admin'] && $viewer['User']['id'] != $owner_id && !in_array($viewer['User']['id'], array_keys($aFriendsList))){
                            unset($gifts[$key]);
                        }
                    }else {

                    }
                }
            }
        }

        return $gifts;
    }

    public function hashtagEnable($event)
    {
        $enable = Configure::read('Gift.gift_hashtag_enabled');
        $event->result['gifts']['enable'] = $enable;
    }

    public function beforeRender($event)
    {
        if(Configure::read('Gift.gift_enabled')){
            $e = $event->subject();

            if (Configure::read('debug') == 0){
                $min="min.";
            }else{
                $min="";
            }
            $e->Helpers->MooRequirejs->addPath(array(
                "mooGift" => $e->Helpers->MooRequirejs->assetUrlJS( "Gift.js/main.{$min}js" ),
                "jqueryUi" => $e->Helpers->MooRequirejs->assetUrlJS( "Gift.js/jquery-ui.js" ),
            ));

            $e->addPhraseJs(array(
                'delete_gift_confirm' => __d('gift','Are you sure you want to delete this gift?'),
                'send_gift_confirm' => __d('gift','Are you sure you want to send this gift now?')
            ));
            $e->Helpers->MooPopup->register('themeModal');
        }
    }
    
    public function deleteUserContent($event) 
    {
        if(!empty($event->data['aUser']['User']['id']))
        {
            $mGift = MooCore::getInstance()->getModel('Gift.Gift');
            $mGift->deleteAllUserGift($event->data['aUser']['User']['id']);
        }
    }

    public function statistic($event)
    {
        $request = Router::getRequest();
        $model = MooCore::getInstance()->getModel("Gift.Gift");
        $event->result['statistics'][] = array(
            'item_count' => $model->find('count'),
            'ordering' => 9999,
            'name' => __d('gift','Gifts'),
            'href' => $request->base.'/admin/gift/gift_categories',
            'icon' => '<i class="material-icons">card_giftcard</i>'
        );
    }
}