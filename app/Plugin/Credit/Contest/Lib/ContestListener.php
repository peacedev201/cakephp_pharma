<?php

App::uses('CakeEventListener', 'Event');

class ContestListener implements CakeEventListener {

    public function implementedEvents() {
        return array(
            'Model.beforeDelete' => 'doAfterDelete',
            'MooView.beforeRender' => 'beforeRender',
            'View.Adm.Layout.adminGetContentInfo' => 'widgetTag',
            'welcomeBox.afterRenderMenu' => 'welcomeBoxAfterRenderMenu',
            'profile.afterRenderMenu' => 'profileAfterRenderMenu',
            'Controller.Search.search' => 'search',
            'Controller.Search.suggestion' => 'suggestion',
            'Controller.Search.hashtags_filter' => 'hashtags_filter',
            'Controller.Search.hashtags' => 'hashtags',
            'Controller.Widgets.tagCoreWidget' => 'hashtagEnable',
            'Controller.Share.afterShare' => 'afterShare',
            'Controller.Comment.afterComment' => 'afterComment',
            'Plugin.View.Api.Search' => 'apiSearch',
            'Plugin.Controller.Contest.beforeView' => 'processEventBeforeView',
            'Controller.Home.adminIndex.Statistic' => 'statistic',

            'StorageHelper.contests.getUrl.local' => 'storage_geturl_local',
            'StorageHelper.contests.getUrl.amazon' => 'storage_geturl_amazon',
            'StorageAmazon.contests.getFilePath' => 'storage_amazon_get_file_path',
                
            'StorageHelper.contest_entries.getUrl.local' => 'storage_geturl_local',
            'StorageHelper.contest_entries.getUrl.amazon' => 'storage_geturl_amazon',
            'StorageAmazon.contest_entries.getFilePath' => 'storage_amazon_get_file_path',


            'StorageHelper.contest_musics.getUrl.local' => 'storage_geturl_local',
            'StorageHelper.contest_musics.getUrl.amazon' => 'storage_geturl_amazon',
            'StorageAmazon.contest_musics.getFilePath' => 'storage_amazon_get_file_path',
                
            'StorageTaskAwsCronTransfer.execute' => 'storage_task_transfer',
            'StorageAmazon.contest_entries.putObject.success' => 'storage_amazon_contest_entries_put_success_callback',
            	
            'ApiHelper.renderAFeed.contest_create' => 'exportContestCreate',
            'ApiHelper.renderAFeed.contest_entry_create' => 'exportContestEntryCreate',
            'ApiHelper.renderAFeed.contest_item_detail_share' => 'exportContestItemDetailShare',
            'ApiHelper.renderAFeed.contest_entry_item_detail_share' => 'exportContestEntryItemDetailShare',
        	'profile.mooApp.afterRenderMenu' => 'apiAfterRenderMenu'
        );
    }

    public function apiAfterRenderMenu($e)
    {
    	$subject = MooCore::getInstance()->getSubject();
    	$e->data['result']['contest'] = array(
    			'text' => __d('contest','Contests'),
    			'url' => FULL_BASE_URL . $e->subject()->request->base . '/contests/browse/profile/'. $subject['User']['id'],
    			'cnt' => 0
    	);
    }
    
    public function exportContestCreate($e)
    {
    	$data = $e->data['data'];
    	$actorHtml = $e->data['actorHtml'];
    	
    	$contestModel = MooCore::getInstance()->getModel("Contest_Contest");
    	$contest = $contestModel->findById($data['Activity']['item_id']);
    	$helper = MooCore::getInstance()->getHelper('Contest_Contest');
    	
    	list($title_tmp,$target) = $e->subject()->getActivityTarget($data,$actorHtml);
    	if(!empty($title_tmp)){
    		$title =  $title_tmp['title'];
    		$titleHtml = $title_tmp['titleHtml'];
    	}else{
    		$title = __d('contest','posted a new contest');
    		$titleHtml = $actorHtml . ' ' . __d('contest','posted a new contest');
    	}
    	$e->result['result'] = array(
    			'type' => 'create',
    			'title' => $title,
    			'titleHtml' => $titleHtml,
    			'objects' => array(
    					'type' => 'Contest_Contest',
    					'id' => $contest['Contest']['id'],
    					'url' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($contest['Contest']['moo_href'], 'UTF-8', 'UTF-8')),
    					'description' => $e->subject()->Text->convert_clickable_links_for_hashtags($e->subject()->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $contest['Contest']['description'])), 200, array('eclipse' => '')), Configure::read('Contest.contest_hashtag_enabled')),
    					'title' => $contest['Contest']['moo_title'],
    					'images' => array('850'=>$helper->getImage($contest,array('prefix'=>'850'))),
    			),
    			'target' => $target,
    	);
    }
    public function exportContestEntryCreate($e)
    {
    	$data = $e->data['data'];
    	$actorHtml = $e->data['actorHtml'];
    	
    	$contestEntryModel = MooCore::getInstance()->getModel("Contest_Contest_Entry");
    	$entry = $contestEntryModel->findById($data['Activity']['item_id']);
    	$helper = MooCore::getInstance()->getHelper('Contest_Contest');
    	
    	list($title_tmp,$target) = $e->subject()->getActivityTarget($data,$actorHtml);
    	if(!empty($title_tmp)){
    		$title =  $title_tmp['title'];
    		$titleHtml = $title_tmp['titleHtml'];
    	}else{
    		$title = __d('contest','submitted a entry to') . " <a href='".$entry['Contest']['moo_href']."'>".$entry['Contest']['moo_title']. "</a>";
    		$titleHtml = $actorHtml . ' ' . __d('contest','submitted a entry to') . " <a href='".$entry['Contest']['moo_href']."'>".$entry['Contest']['moo_title']. "</a>";
    	}
    	$e->result['result'] = array(
    			'type' => 'create',
    			'title' => $title,
    			'titleHtml' => $titleHtml,
    			'objects' => array(
    					'type' => 'Contest_Contest_Entry',
    					'id' => $entry['ContestEntry']['id'],
    					'url' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($entry['ContestEntry']['moo_href'], 'UTF-8', 'UTF-8')),
    					'description' => $e->subject()->Text->convert_clickable_links_for_hashtags($e->subject()->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $entry['ContestEntry']['caption'])), 200, array('eclipse' => '')), Configure::read('Contest.contest_hashtag_enabled')),
    					'title' => $entry['ContestEntry']['moo_title'],
    					'images' => array('850'=>$helper->getEntryImage($entry,array('prefix'=>'850')))
    			),
    			'target' => $target,
    	);
    }
    public function exportContestItemDetailShare($e)
    {
    	$data = $e->data['data'];
    	$actorHtml = $e->data['actorHtml'];
    	
    	$contestModel = MooCore::getInstance()->getModel("Contest_Contest");
    	$contest = $contestModel->findById($data['Activity']['parent_id']);
    	$helper = MooCore::getInstance()->getHelper('Contest_Contest');
    	
    	$target = array();
    	
    	if (isset($data['Activity']['parent_id']) && $data['Activity']['parent_id'])
    	{    	
    		$title = $data['User']['name'] . ' ' . __d('contest',"shared %s's contest", $contest['User']['name']);
    		$titleHtml = $actorHtml . ' ' . __d('contest',"shared %s's contest", $e->subject()->Html->link($contest['User']['name'], FULL_BASE_URL . $contest['User']['moo_href']));
	    	$target = array(
	    			'url' => FULL_BASE_URL . $contest['User']['moo_href'],
	    			'id' => $contest['User']['id'],
	    			'name' => $contest['User']['name'],
	    			'type' => 'User',
	    	);
    	}
    	
    	list($title_tmp,$target) = $e->subject()->getActivityTarget($data,$actorHtml,true);
    	if(!empty($title_tmp)){
    		$title .=  $title_tmp['title'];
    		$titleHtml .= $title_tmp['titleHtml'];
    	}
    	
    	$e->result['result'] = array(
    			'type' => 'share',
    			'title' => $title,
    			'titleHtml' => $titleHtml,
    			'objects' => array(
    					'type' => 'Contest_Contest',
    					'id' => $contest['Contest']['id'],
    					'url' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($contest['Contest']['moo_href'], 'UTF-8', 'UTF-8')),
    					'description' => $e->subject()->Text->convert_clickable_links_for_hashtags($e->subject()->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $contest['Contest']['description'])), 200, array('eclipse' => '')), Configure::read('Contest.contest_hashtag_enabled')),
    					'title' => $contest['Contest']['moo_title'],
    					'images' => array('850'=>$helper->getImage($contest,array('prefix'=>'850')))
    			),
    			'target' => $target,
    	);
    }
    
    public function exportContestEntryItemDetailShare($e)
    {
    	$data = $e->data['data'];
    	$actorHtml = $e->data['actorHtml'];
    	
    	$contestEntryModel = MooCore::getInstance()->getModel("Contest_Contest_Entry");
    	$entry = $contestEntryModel->findById($data['Activity']['parent_id']);
    	$helper = MooCore::getInstance()->getHelper('Contest_Contest');
    	
    	$target = array();
    	
    	if (isset($data['Activity']['parent_id']) && $data['Activity']['parent_id'])
    	{    	
    		$title = $data['User']['name'] . ' ' . __d('contest',"shared %s's entry", $entry['User']['name']);
    		$titleHtml = $actorHtml . ' ' . __d('contest',"shared %s's entry", $e->subject()->Html->link($entry['User']['name'], FULL_BASE_URL . $entry['User']['moo_href']));
	    	$target = array(
	    			'url' => FULL_BASE_URL . $entry['User']['moo_href'],
	    			'id' => $entry['User']['id'],
	    			'name' => $entry['User']['name'],
	    			'type' => 'User',
	    	);
    	}
    	
    	list($title_tmp,$target) = $e->subject()->getActivityTarget($data,$actorHtml,true);
    	if(!empty($title_tmp)){
    		$title .=  $title_tmp['title'];
    		$titleHtml .= $title_tmp['titleHtml'];
    	}
    	
    	$e->result['result'] = array(
    			'type' => 'share',
    			'title' => $title,
    			'titleHtml' => $titleHtml,
    			'objects' => array(
    					'type' => 'Contest_Contest_Entry',
    					'id' => $entry['ContestEntry']['id'],
    					'url' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($entry['ContestEntry']['moo_href'], 'UTF-8', 'UTF-8')),
    					'description' => $e->subject()->Text->convert_clickable_links_for_hashtags($e->subject()->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $entry['ContestEntry']['caption'])), 200, array('eclipse' => '')), Configure::read('Contest.contest_hashtag_enabled')),
    					'title' => $entry['ContestEntry']['moo_title'],
    					'images' => array('850'=>$helper->getEntryImage($entry,array('prefix'=>'850')))
    			),
    			'target' => $target,
    	);
    }

    public function storage_amazon_contest_entries_put_success_callback($e)
    {
        $path = $e->data['path'];
        if (Configure::read('Storage.storage_amazon_delete_image_after_adding') == "1")
        {
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
        
        if ($type == 'contests')
        {
            if ($e->data['thumb']) {
                $url = FULL_BASE_LOCAL_URL . $request->webroot . 'uploads/contests/thumbnail/' . $oid . '/' . $prefix . $thumb;
            } else {
                //$url = FULL_BASE_LOCAL_URL . $v->assetUrl('Blog.noimage/blog.png', array('prefix' => rtrim($prefix, "_"), 'pathPrefix' => Configure::read('App.imageBaseUrl')));
                $url = $v->getImage("contest/img/noimage/contest.png");
            }
        }
        elseif ($type == 'contest_entries') {
            if ($e->data['thumb']) {
                $url = FULL_BASE_LOCAL_URL . $request->webroot . 'uploads/contest_entries/thumbnail/' . $oid . '/' . $prefix . $thumb;
            } else {
                $url = $v->getImage("contest/img/noimage/contest.png");
            }
        }
        else
        {
            $url = FULL_BASE_LOCAL_URL . $request->webroot . 'uploads/contest_entries/music/'. $thumb;
        }
        $e->result['url'] = $url;
    }
    
    public function storage_geturl_amazon($e)
    {
        $v = $e->subject();
        $type = $e->data['type'];
        if ($type =='contests')
        {
            $e->result['url'] = $v->getAwsURL($e->data['oid'], "contests", $e->data['prefix'], $e->data['thumb']);
        }
        elseif ($type =='contest_entries')
        {
            $e->result['url'] = $v->getAwsURL($e->data['oid'], "contest_entries", $e->data['prefix'], $e->data['thumb']);
        }
        else 
        {
            $e->result['url'] = $v->getAwsURL($e->data['oid'], "contest_musics", '', $e->data['thumb']);
        }
    }
    
    public function storage_amazon_get_file_path($e)
    {
        $objectId = $e->data['oid'];
        $name = $e->data['name'];
        $thumb = $e->data['thumb'];
        $type = $e->data['type'];;
        $path = false;
        if ($type == 'contests')
        {
            if (!empty($thumb)) {
                $path = WWW_ROOT . "uploads" . DS . "contests" . DS . "thumbnail" . DS . $objectId . DS . $name . $thumb;
            }
        }
        elseif ($type =='contest_entries')
        {
            $path = WWW_ROOT . "uploads" . DS . "contest_entries" . DS . "thumbnail" . DS . $objectId . DS . $name . $thumb;
        }else 
        {
            $path = WWW_ROOT . "uploads" . DS . "contest_entries" . DS . "music" . DS . $thumb;
        }
        
        $e->result['path'] = $path;
    }
    public function storage_task_transfer($e)
    {
        $v = $e->subject();
        $mModel = MooCore::getInstance()->getModel('Contest.Contest');
        $items = $mModel->find('all', array(
                'conditions' => array("Contest.id > " => $v->getMaxTransferredItemId("contests")),
                'limit' => 10,
                'fields'=>array('Contest.id','Contest.thumbnail'),
                'order' => array('Contest.id'),
                )
        );
        
        if($items){
            $photoSizes = $v->photoSizes();
            foreach($items as $item){
                if (!empty($item["Contest"]["thumbnail"])) {
                    foreach ($photoSizes as $size){
                        $v->transferObject($item["Contest"]['id'],"contests",$size.'_',$item["Contest"]["thumbnail"]);
                    }
                    $v->transferObject($item["Contest"]['id'],"contests",'',$item["Contest"]["thumbnail"]);
                }
            }
        }

        $mPhotoModel = MooCore::getInstance()->getModel('Contest.ContestEntry');
        $item_photos = $mPhotoModel->find('all', array(
                'conditions' => array("ContestEntry.id > " => $v->getMaxTransferredItemId("contest_entries")),
                'limit' => 10,
                'fields'=>array('ContestEntry.id','ContestEntry.thumbnail'),
                'order' => array('ContestEntry.id'),
                )
        );
        if($item_photos){
            $photoSizes = $v->photoSizes();
            foreach($item_photos as $item_photo){
                if (!empty($item_photo["ContestEntry"]["thumbnail"])) {
                    foreach ($photoSizes as $size){
                        $v->transferObject($item_photo["ContestEntry"]['id'],"contest_entries",$size.'_',$item_photo["ContestEntry"]["thumbnail"]);
                    }
                    $v->transferObject($item_photo["ContestEntry"]['id'],"contest_entries",'',$item_photo["ContestEntry"]["thumbnail"]);
                }
            }
        }
    }

    public function statistic($event)
    {
        $request = Router::getRequest();
        $model = MooCore::getInstance()->getModel("Contest.Contest");
        $event->result['statistics'][] = array(
            'item_count' => $model->find('count'),
            'ordering' => 9999,
            'name' => __d('contest','Contests'),
            'href' => $request->base.'/admin/contest/contests',
            'icon' => '<i class="material-icons">highlight</i>'
        );
    }

    public function processEventAfterSaveEntry($event) {
        $v = $event->subject();
        $entry_id = $event->data['id'];
        App::import('Model', 'Contest.ContestEntry');
        $this->ContestEntry = new ContestEntry();
        $entry = $this->ContestEntry->findById($entry_id);
        if ($entry['Contest']['auto_approve']) {
            $this->ContestEntry->updateStatus($this->ContestEntry->id, 'published');
        }
        if ($entry['User']['id'] != $entry['Contest']['user_id']) {
            $notificationModel = MooCore::getInstance()->getModel('Notification');
            $notification = $notificationModel->find('first', array('conditions' => array(
                    'user_id' => $entry['Contest']['user_id'],
                    'sender_id' => $entry['User']['id'],
                    'action' => 'submit_entry',
                    'url' => '/contests/entry/' . $entry['ContestEntry']['id'],
                    'plugin' => 'Contest'
            )));
            App::import('Model', 'UserBlock');
            $helper = MooCore::getInstance()->getHelper('Contest_Contest'); 
            $is_block = $helper->areUserBlocks($entry['Contest']['user_id'], $entry['User']['id']);
            if (empty($notification) && !$is_block) {
                $notificationModel->record(array(
                    'recipients' => $entry['Contest']['user_id'],
                    'sender_id' => $entry['User']['id'],
                    'action' => 'submit_entry',
                    'url' => '/contests/entry/' . $entry['ContestEntry']['id'],
                    'params' => htmlspecialchars($entry['ContestEntry']['id']),
                    'plugin' => 'Contest'
                ));
            }
        }
    }

    public function apiSearch($event) {
        $view = $event->subject();
        $items = &$event->data['items'];
        $type = $event->data['type'];
        $viewer = MooCore::getInstance()->getViewer();
        $utz = $viewer['User']['timezone'];
        if ($type == 'Contest' && isset($view->viewVars['contests']) && count($view->viewVars['contests'])) {
            $helper = MooCore::getInstance()->getHelper('Contest_Contest');
            foreach ($view->viewVars['contests'] as $item) {
                $items[] = array(
                    'id' => $item["Contest"]['id'],
                    'url' => FULL_BASE_URL . $item['Contest']['moo_href'],
                    'avatar' => $helper->getImage($item),
                    'owner_id' => $item["Contest"]['user_id'],
                    'title_1' => $item["Contest"]['moo_title'],
                    'title_2' => __('Posted by') . ' ' . html_entity_decode($view->Moo->getNameWithoutUrl($item['User'], false),ENT_QUOTES)  . ' ' . $view->Moo->getTime($item["Contest"]['created'], Configure::read('core.date_format'), $utz),
                    'created' => $item["Contest"]['created'],
                    'type' => "Contest",
                    'type_title' => __d('contest',"Contest")
                );
            }
        }
    }

    public function afterShare($event) {
        $data = $event->data['data'];
        if (isset($data['item_type']) && $data['item_type'] == 'Contest_Contest') {
            $contest_id = isset($data['parent_id']) ? $data['parent_id'] : 0;
            $contestModel = MooCore::getInstance()->getModel('Contest.Contest');
            $contestModel->updateAll(array('Contest.share_count' => 'Contest.share_count + 1'), array('Contest.id' => $contest_id));
        }
    }

    public function afterComment($event) {
        $data = $event->data['data'];
        $target_id = isset($data['target_id']) ? $data['target_id'] : null;
        $type = isset($data['type']) ? $data['type'] : '';
        if ($type == 'Contest_Contest' && !empty($target_id)) {
            $contestModel = MooCore::getInstance()->getModel('Contest.Contest');
            $contestModel->updateCounter($target_id);
        }
        if ($type == 'Contest_Contest_Entry' && !empty($target_id)) {
            $contestEntryModel = MooCore::getInstance()->getModel('Contest.ContestEntry');
            $contestEntryModel->updateCounter($target_id);
        }
    }

    public function hashtags($event) {
        if (Configure::read('Contest.contest_enabled')) {
            $contests = array();
            $e = $event->subject();
            $tagModel = MooCore::getInstance()->getModel('Tag');
            $contestModel = MooCore::getInstance()->getModel('Contest.Contest');
            $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;

            $uid = MooCore::getInstance()->getViewer(true);

            if (Configure::read('Contest.contest_hashtag_enabled')) {
                if(isset($event->data['type']) && $event->data['type'] == 'contests')
                {
                    $contests = $contestModel->getContestHashtags($event->data['item_ids'],RESULTS_LIMIT,$page);
                    $contests = $this->_filterContest($contests);
                }
                $table_name = $contestModel->table;
                if(isset($event->data['type']) && $event->data['type'] == 'all' && !empty($event->data['item_groups'][$table_name]) )
                {
                    $contests = $contestModel->getContestHashtags($event->data['item_groups'][$table_name],5);
                    $contests = $this->_filterContest($contests);
                }
            }
            // get tagged item
            $tag = htmlspecialchars(urldecode($event->data['search_keyword']));
            $tags = $tagModel->find('all', array('conditions' => array(
                    'Tag.type' => 'Contest_Contest',
                    'Tag.tag' => $tag
            )));
            $contest_ids = Hash::combine($tags, '{n}.Tag.id', '{n}.Tag.target_id');
            $items = $contestModel->getContests(array('user_id' => $uid, 'page' => $page, 'ids' => $contest_ids));

            $contests = array_merge($contests, $items);

            //only display 5 items on All Search Result page
            if (isset($event->data['type']) && $event->data['type'] == 'all') {
                $contests = array_slice($contests, 0, 5);
            }
            $contests = array_map("unserialize", array_unique(array_map("serialize", $contests)));
            if (!empty($contests)) {
                $event->result['contests']['header'] = __d('contest', 'Contests');
                $event->result['contests']['icon_class'] = 'highlight';
                $event->result['contests']['view'] = "Contest.lists/contests";
                if (isset($event->data['type']) && $event->data['type'] == 'contests') {
                    $e->set('result', 1);
                    $e->set('more_url', '/search/hashtags/' . $e->params['pass'][0] . '/contests/page:' . ( $page + 1 ));
                    $e->set('element_list_path', "Contest.lists/contests");
                }
                $e->set('contests', $contests);
            }
        }
    }


    public function hashtags_filter($event) {
        if (Configure::read('Contest.contest_enabled')) {
            $e = $event->subject();
            $contestModel = MooCore::getInstance()->getModel('Contest_Contest');
            $uid = MooCore::getInstance()->getViewer(true);

            if (isset($event->data['type']) && $event->data['type'] == 'contests') {
                $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;
                $contests = $contestModel->getContests(array('user_id' => $uid, 'page' => $page, 'ids' => $event->data['item_ids']));
                $e->set('contests', $contests);
                $e->set('result', 1);
                $e->set('more_url', '/search/hashtags/' . $e->params['pass'][0] . '/contests/page:' . ( $page + 1 ));
                $e->set('element_list_path', "Contest.lists/contests");
            }
            $table_name = $contestModel->table;
            if (isset($event->data['type']) && $event->data['type'] == 'all' && !empty($event->data['item_groups'][$table_name])) {
                $event->result['contests'] = null;

                $contests = $contestModel->getContests(array('user_id' => $uid, 'limit' => 5, 'ids' => $event->data['item_groups'][$table_name]));

                if (!empty($contests)) {
                    $event->result['contests']['header'] = __d('contest', 'Contests');
                    $event->result['contests']['icon_class'] = 'highlight';
                    $event->result['contests']['view'] = "Contest.lists/contests";
                    $e->set('contests', $contests);
                }
            }
        }
    }
    private function _filterContest($items)
    {
        if(!empty($items))
        {
            $friendModel = MooCore::getInstance()->getModel('Friend');
            $viewer = MooCore::getInstance()->getViewer();
            foreach($items as $key => &$item)
            {
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
        }

        return $items;
    }
     public function hashtagEnable($event)
    {
        $event->result['contests']['enable'] = Configure::read('Contest.contest_hashtag_enabled');
    }
    public function widgetTag($event) {
        $event->result['tag']['type']['Contest_Contest'] = 'Contest';
    }

    public function welcomeBoxAfterRenderMenu($event) {
        $view = $event->subject();
        $uid = MooCore::getInstance()->getViewer(true);
        if (Configure::read('Contest.contest_enabled') && $uid) {
            $contestModel = MooCore::getInstance()->getModel('Contest_Contest');
            $total = $contestModel->getTotalContests(array('type' => 'home', 'user_id' => $uid));
            echo $view->element('menu_welcome', array('count' => $total), array('plugin' => 'Contest'));
        }
    }

    public function profileAfterRenderMenu($event) {
        $view = $event->subject();
        $uid = MooCore::getInstance()->getViewer(true);
        if (Configure::read('Contest.contest_enabled')) {
            $contestModel = MooCore::getInstance()->getModel('Contest_Contest');
            $subject = MooCore::getInstance()->getSubject();
            $total = $contestModel->getTotalContests(array('type' => 'profile' ,'owner_id' => $subject['User']['id'], 'user_id' => $uid));
            echo $view->element('menu_profile', array('count' => $total), array('plugin' => 'Contest'));
        }
    }

    public function search($event) {
        if (Configure::read('Contest.contest_enabled')) {
            $e = $event->subject();
            $uid = MooCore::getInstance()->getViewer(true);
            $contestModel = MooCore::getInstance()->getModel('Contest_Contest');
            $results = $contestModel->getContests(array('search' => $e->keyword, 'user_id' => $uid, 'type' => 'all', 'limit' => 4));

            if (isset($e->plugin) && $e->plugin == 'Contest') {
                $e->set('contests', $results);
                $e->render("Contest.Elements/lists/contests");
                $e->set('no_list_id', true);
            } else {
                $event->result['Contest']['header'] = __d('contest', "Contest");
                $event->result['Contest']['icon_class'] = 'highlight';
                $event->result['Contest']['view'] = "lists/contests";
                $e->set('no_list_id', true);
                if (!empty($results))
                    $event->result['Contest']['notEmpty'] = 1;
                $e->set('contests', $results);
            }
        }
    }

    public function suggestion($event) {
        if (Configure::read('Contest.contest_enabled')) {
            $e = $event->subject();
            $contestModel = MooCore::getInstance()->getModel('Contest_Contest');
            $uid = MooCore::getInstance()->getViewer(true);

            $event->result['contest']['header'] = __d('contest', "Contest");
            $event->result['contest']['icon_class'] = '<i class="material-icons">highlight</i>';

            if (isset($event->data['type']) && $event->data['type'] == 'contest') {
                $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;
                $contests = $contestModel->getContests(array('page' => $page, 'user_id' => $uid, 'search' => $event->data['searchVal']));

                $e->set('contests', $contests);
                $e->set('result', 1);
                $e->set('no_list_id', true);
                $e->set('more_url', '/search/suggestion/contest/' . $e->params['pass'][1] . '/page:' . ( $page + 1 ));
                $e->set('element_list_path', "Contest.lists/contests");
            }
            if (isset($event->data['type']) && $event->data['type'] == 'all') {
                $event->result['contest'] = null;
                $contests = $contestModel->getContests(array('page' => 1, 'limit' => 2, 'user_id' => $uid, 'search' => $event->data['searchVal']));
                $helper = MooCore::getInstance()->getHelper('Contest_Contest');
                $mooHelper = MooCore::getInstance()->getHelper('Core_Moo');

                if (!empty($contests)) {
                    foreach ($contests as $index => $detail) {
                        $event->result['contest'][$index]['id'] = $detail['Contest']['id'];
                        $event->result['contest'][$index]['img'] = $helper->getImage($detail, array('prefix' => '150_square'));
                        $event->result['contest'][$index]['title'] = $detail['Contest']['moo_title'];
                        $event->result['contest'][$index]['find_name'] = __d('contest', 'Find Contest');
                        $event->result['contest'][$index]['icon_class'] = 'icon-edit-1';
                        $event->result['contest'][$index]['view_link'] = 'contests/view/';

                        $event->result['contest'][$index]['more_info'] = __d('contest', 'Posted by') . ' ' . $mooHelper->getNameWithoutUrl($detail['User'], false) . ' ' . $mooHelper->getTime($detail['Contest']['created'], Configure::read('core.date_format'), $e->viewVars['utz']);
                    }
                }
            }
        }
    }

    public function beforeRender($event) {
        if (Configure::read('Contest.contest_enabled')) {
            $e = $event->subject();
            $e->Helpers->Html->css(array(
                'Contest.main'
                    ), array('block' => 'css')
            );

            if (Configure::read('debug') == 0) {
                $min = "min.";
            } else {
                $min = "";
            }
            $e->Helpers->MooRequirejs->addPath(array(
                "mooMasonry" => $e->Helpers->MooRequirejs->assetUrlJS("Contest.js/masonry.pkgd.min.js"),
                "mooImagesloaded" => $e->Helpers->MooRequirejs->assetUrlJS("Contest.js/imagesloaded.pkgd.min.js"),
                "mooBridget" => $e->Helpers->MooRequirejs->assetUrlJS("Contest.js/jquery-bridget.js"),
                "mooContestCountdown" => $e->Helpers->MooRequirejs->assetUrlJS("Contest.js/jquery.countdown.min.js"),
                "mooContestMusicPlayer"=>$e->Helpers->MooRequirejs->assetUrlJS("Contest.js/jquery.jplayer.min.js"),             
                "mooContest" => $e->Helpers->MooRequirejs->assetUrlJS("Contest.js/main.{$min}js")
            ));
            $e->Helpers->MooRequirejs->addShim(array(
                'mooMasonry' => array("deps" => array('jquery')),
                'mooContestCountdown' => array("deps" => array('jquery')),
                "mooImagesloaded" => array("deps" => array('jquery')),
                'mooContestMusicPlayer'=>array("deps" =>array('jquery'))
            ));
            $e->addPhraseJs(array(
                'sec' => __d('contest', 'sec'),
                'min' => __d('contest', 'min'),
                'hrs' => __d('contest', 'hrs'),
                'days' => __d('contest', 'days'),
                'weeks' => __d('contest', 'weeks'),
                'drag_or_click_here_to_upload_music' => __d('contest', 'Drag or click here to upload music'),
                'only_approve_pending' => __d('contest', 'You can only approve Pending approval entries'),
                'select_entry_to_submit' => __d('contest', 'Please select entry to continue submission'),
                'can_not_vote' => __d('contest', 'Can not vote entry'),
                'can_not_unvote' => __d('contest', 'Can not un-vote entry'),
                'select_entry' => __d('contest', 'Please select at least one list'),
                'win_entry_confirm' => __d('contest', 'Are you sure you want to set entry to win? This contest will be closed.'),
                'deny_entry_confirm' => __d('contest', 'Are you sure you want to deny this entry?'),
                'deny_entries_confirm' => __d('contest', 'Are you sure you want to deny entries?'),
                'delete_entry_confirm' => __d('contest', 'Are you sure you want to delete this entry?'),
                'request_delete_contest_confirm' => __d('contest', 'Are you sure you want to request delete this contest? Admin will review and delete this contest.'),
                'delete_entries_confirm' => __d('contest', 'Are you sure you want to delete entries?'),
                'approve_entry_confirm' => __d('contest', 'Are you sure you want to approve this entry?'),
                'approve_entries_confirm' => __d('contest', 'Are you sure you want to approve entries?'),
                'win_entries_confirm' => __d('contest', 'Are you sure you want to set win for entries? This contest will be closed.'),
                'leave_contest_confirm' => __d('contest', 'Are you sure you want to leave this contest? All submitted entry will be removed.'),
                'delete_contest_confirm' => __d('contest', 'Are you sure you want to delete this contest?'),
                'contest_duration_start' => __d('contest', 'Contest Duration: Start date must be greater than current date'),
                'contest_duration_end' => __d('contest', 'Contest Duration: End date must be greater than current date'),
                'contest_duration_start_end' => __d('contest', 'Contest Duration: End date must be greater than Start date'),
                'submit_duration_start' => __d('contest', 'Submit Entries Duration: Start date must be greater than current date'),
                'submit_duration_end' => __d('contest', 'Submit Entries Duration: End date must be greater than current date'),
                'submit_duration_start_end' => __d('contest', 'Submit Entries Duration: End date must be greater than Start date'),
                'vote_duration_start' => __d('contest', 'Voting Duration: Start date must be greater than current date'),
                'vote_duration_end' => __d('contest', 'Voting Duration: End date must be greater than current date'),
                'vote_duration_start_end' => __d('contest', 'Voting Duration: End date must be greater than Start date'),
                'start_submit_greater_duration' => __d('contest', 'Start of Submit Entries Duration must be greater than or equal to the Start of Contest Duration'),
                'end_submit_greater_duration' => __d('contest', 'End of Contest Duration must be greater than or equal to the End of Submit Entries Duration'),
                'start_vote_greater_submit' => __d('contest', 'Start of Voting Duration must be greater than or equal to the Start of Submit Entries Duration'),
                'end_vote_greater_submit' => __d('contest', 'End of Voting Duration must be greater than or equal to the End of Submit Entries Duration'),
                'note_published' => __d('contest', 'When the contest is published, you can not edit any information. You should choose Save as Draft to make sure that all information are correct before publishing.')
            ));
            $e->Helpers->MooPopup->register('themeModal');
        }
    }

    public function doAfterDelete($event) {
        $model = $event->subject();
        $type = ($model->plugin) ? $model->plugin . '_' : '' . get_class($model);
        if ($type == 'User') {
            $contestModel = MooCore::getInstance()->getModel('Contest_Contest');
            $contestModel->deleteAll(array('Contest.user_id' => $model->id));
            $contestEntryModel = MooCore::getInstance()->getModel('Contest.ContestEntry');
            $contestEntryModel->deleteEntries($model->id);
            $contestCandidateModel = MooCore::getInstance()->getModel('Contest.ContestCandidate');
            $contestCandidateModel->deleteCandidates($model->id);
            $contestVoteModel = MooCore::getInstance()->getModel('Contest.ContestVote');
            $contestVoteModel->deleteVotes($model->id);
        }
        if ($type == 'Video_Video') {
            if($model->pc_upload){
                $contestEntryModel = MooCore::getInstance()->getModel('Contest.ContestEntry');
                $contestVoteModel = MooCore::getInstance()->getModel('Contest.ContestVote');
                $entry_ids = $contestEntryModel->find('list', array('conditions' => array('ContestEntry.source_id' => $model->id), 'fields' => array('ContestEntry.id')));
                if(!empty($entry_ids)) {
                    foreach($entry_ids as $entry_id){
                        $contestEntryModel->deleteEntry($entry_id);
                        $vote_ids = $contestVoteModel->find('list', array('conditions' => array('ContestVote.contest_entry_id' => $entry_id), 'fields' => array('ContestVote.id')));
                        if(!empty($vote_ids)) {
                            foreach($vote_ids as $vote_id){
                                $contestVoteModel->deleteVote($vote_id);
                            }
                        }
                    }
                }
            }
            
        }
    }

    public function processEventBeforeView($event) {
        $v = $event->subject();
        $this->Friend = ClassRegistry::init('Friend');
        $this->FriendRequest = ClassRegistry::init('FriendRequest');
        $this->Tag = ClassRegistry::init('Tag');
        $this->Comment = ClassRegistry::init('Comment');
        $this->Like = ClassRegistry::init('Like');
        $areFriends = false;
        if (!empty($event->data['uid'])) { //  check if user is a friend
            $areFriends = $this->Friend->areFriends($event->data['uid'], $event->data['contest']['User']['id']);
        }
        $v->_checkContestPrivacy($event->data['contest']['Contest']['privacy'], $event->data['contest']['User']['id'], $areFriends);

        $tags = $this->Tag->getContentTags($event->data['id'], 'Contest_Contest');

        $comments = $this->Comment->getComments($event->data['id'], 'Contest_Contest');

        $comment_likes = array();
        // get comment likes
        if (!empty($event->data['uid'])) {
            $comment_likes = $this->Like->getCommentLikes($comments, $event->data['uid']);
            $v->set('comment_likes', $comment_likes);
        }

        $requests = $this->FriendRequest->getRequestsList($event->data['uid']);
        $respond = $this->FriendRequest->getRequests($event->data['uid']);
        $request_id = Hash::combine($respond, '{n}.FriendRequest.sender_id', '{n}.FriendRequest.id');
        $respond = Hash::extract($respond, '{n}.FriendRequest.sender_id');

        $v->set('respond', $respond);
        $v->set('request_id', $request_id);
        $v->set('friends_request', $requests);

        $v->set('tags', $tags);
        $v->set('areFriends', $areFriends);

        $comment_count = $this->Comment->getCommentsCount($event->data['id'], 'Contest_Contest');
        $page = 1;
        $data['bIsCommentloadMore'] = $comment_count - $page * RESULTS_LIMIT;
        $data['more_comments'] = '/comments/browse/contest_contest/' . $event->data['id'] . '/page:' . ($page + 1);
        $data['admins'] = array($event->data['contest']['Contest']['user_id']);
        $data['comments'] = $comments;
        $data['comment_likes'] = $comment_likes;
        $v->set('data', $data);
    }

}
