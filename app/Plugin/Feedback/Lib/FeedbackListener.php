<?php
App::uses('CakeEventListener', 'Event');

class FeedbackListener implements CakeEventListener
{
    public $helpers = array('Storage.Storage');
    
    public function implementedEvents()
    {
        return array(        	
            'Controller.Comment.afterComment' => 'afterComment',
            'MooView.beforeRender' => 'beforeRender',
            'Controller.Search.search' => 'search',
            'Controller.Search.suggestion' => 'suggestion',
            'Controller.Search.hashtags' => 'hashtags',
            'Controller.Search.hashtags_filter' => 'hashtags_filter',
            'Controller.Share.afterShare' => 'afterShare',
            'MooView.afterLoadMooCore' => 'afterLoadMooCore',
            'Controller.Home.adminIndex.Statistic' => 'renderAdminStatistic',
            'Plugin.View.Api.Search' => 'apiSearch',

            'ApiHelper.renderAFeed.feedback_create' => 'exportFeedbackCreate',
        	'ApiHelper.renderAFeed.feedback_item_detail_share' => 'exportFeedbackItemDetailShare',
            
            'StorageHelper.feedbacks.getUrl.local' => 'storage_geturl_local',
            'StorageHelper.feedbacks.getUrl.amazon' => 'storage_geturl_amazon',
            'StorageAmazon.feedbacks.getFilePath' => 'storage_amazon_get_file_path',
            'StorageTaskAwsCronTransfer.execute' => 'storage_task_transfer',
        );
    }
    
     public function storage_geturl_local($e)
    {
        $v = $e->subject();
        $request = Router::getRequest();
        $oid = $e->data['oid'];
        $thumb = $e->data['thumb'];
        $prefix = $e->data['prefix'];
        $url = '';
        if ($e->data['thumb']) {
            $url = FULL_BASE_LOCAL_URL . $request->webroot . 'uploads/feedbacks/'. $thumb;
        } else {
            $url = $v->getImage("feedback/img/noimage/feedback.png");
        }
        $e->result['url'] = $url;
    }

    public function storage_geturl_amazon($e)
    {
        $v = $e->subject();
        $e->result['url'] = $v->getAwsURL($e->data['oid'], "feedbacks", $e->data['prefix'], $e->data['thumb']);
    }

    public function storage_amazon_get_file_path($e)
    {
        $objectId = $e->data['oid'];
        $name = $e->data['name'];
        $thumb= $e->data['thumb'];
        $path = false;
        if (!empty($thumb)) {
            $path = WWW_ROOT . "uploads" . DS . "feedbacks" . DS . $name . $thumb;
        }

        $e->result['path'] =   $path ;
    }
    public function storage_task_transfer($e)
    {
        $v = $e->subject();
        $feedbackImageModel = MooCore::getInstance()->getModel('Feedback.FeedbackImage');
        $feedbacks = $feedbackImageModel->find('all', array(
                'conditions' => array("FeedbackImage.feedback_id > " => $v->getMaxTransferredItemId("feedbacks")),
                'limit' => 10,
                'fields'=>array('FeedbackImage.feedback_id','FeedbackImage.image_url'),
                'order' => array('FeedbackImage.feedback_id'),
            )
        );

        if($feedbacks){
            foreach($feedbacks as $feedback){
                if (!empty($feedback["FeedbackImage"]["image_url"])) {
                    $v->transferObject($feedback["FeedbackImage"]['feedback_id'],"feedbacks",'',$feedback["FeedbackImage"]["image_url"]);
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
        if ($type == 'Feedback_Feedback' && !empty($target_id))
        {
            $mFeedback = MooCore::getInstance()->getModel('Feedback.Feedback');
            $mUser = MooCore::getInstance()->getModel('User');
            $coMooMail = MooCore::getInstance()->getComponent('MooMail');
            Cache::clearGroup('feedback', 'feedback');
            $mFeedback->updateCounter($target_id);
            
            //send email for unregistered user
            $aFeedback = $mFeedback->findById($target_id);
            if($aFeedback != null && $aFeedback['Feedback']['user_id'] > 0)
            {
                $cUser = $mUser->findById(MooCore::getInstance()->getViewer(true)); 
                $email = !empty($aFeedback['User']['email']) ? $aFeedback['User']['email'] : $aFeedback['Feedback']['email'];
                if($email != null)
                {
                    $coMooMail->send($email, 'feedback_comment', array(  
                        'username' => $cUser['User']['name'],
                        'message' => $data['message'],
                        'link' => Router::url('/', true ).$aFeedback['Feedback']['moo_url']
                    ));
                }
            }
        }
    }

    public function apiSearch($event)
    {
    	$view = $event->subject();
    	$items = &$event->data['items'];
    	$type = $event->data['type'];
    	$viewer = MooCore::getInstance()->getViewer();
    	$utz = $viewer['User']['timezone'];
    	if ($type == 'Feedback' && isset($view->viewVars['feedbacks']) && count($view->viewVars['feedbacks']))
    	{
    		$helper = MooCore::getInstance()->getHelper('Feedback_Feedback');
    		foreach ($view->viewVars['feedbacks'] as $item){
    			$items[] = array(
    					'id' => $item["Feedback"]['id'],
    					'url' => FULL_BASE_URL.$item['Feedback']['moo_href'],
    					'avatar' =>  $helper->getImageForSuggestion($item['Feedback']['id'], array('prefix'=>'150_square')),
    					'owner_id' => $item["Feedback"]['user_id'],
    					'title_1' => $item["Feedback"]['moo_title'],
    					'title_2' => __( 'Posted by') . ' ' . $view->Moo->getNameWithoutUrl($item['User'], false) . ' ' .$view->Moo->getTime( $item["Feedback"]['created'], Configure::read('core.date_format'), $utz ),
    					'created' => $item["Feedback"]['created'],
    					'type' => "Feedback",
    					'type_title' => __d('feedback',"Feedback")
    			);
    		}
    	}
    }
    
    public function beforeRender($event)
    {
        $e = $event->subject();
        if(Configure::read('Feedback.feedback_enabled') && 
          (empty($e->request->params['prefix']) || $e->request->params['prefix'] != 'admin') &&
          $e->request->params['controller'] != 'share')
        {
            $post_max_size = ini_get('post_max_size');
            $upload_max_filesize = ini_get('upload_max_filesize');
            $file_max_upload = (int) $post_max_size > (int) $upload_max_filesize ? $upload_max_filesize : $post_max_size;
            $e->addPhraseJs(array(
                'tmaxsize' => __d('feedback', 'Can not upload file more than ' . $file_max_upload),
                'tdesc' => __d('feedback', 'Drag or click here to upload photo'),
                'tdescfile' => __d('feedback', 'Click or Drap your file here'),
                'feedback_delete_confirm' => __d('feedback', 'Are you sure you want to delete this feedback?'),
                'feedbacks_delete_confirm' => __d('feedback', 'Are you sure you want to delete these feedbacks?'),
                'upload_button_text' => __d('feedback','Drag or click here to upload files')
            ));
            $e->Helpers->Html->script( array(
                    'https://www.google.com/recaptcha/api.js?hl=en',
                    'Feedback.feedback',
                    'jquery.fileuploader',
                    'Feedback.shortcut',
                ),
                array('block' => 'script')
            );
        }
        
        
        if(1/*Configure::read('Feedback.feedback_enabled')*/){
    		$e->Helpers->Html->css( array(
    					'Feedback.feedback', 
    				),
    				array('block' => 'css')
    			);
    		
    		
    		if (Configure::read('debug') == 0){
    			$min="min.";
    		}else{
    			$min="";
    		}
                   
    		$e->Helpers->MooRequirejs->addPath(array(
                        "mooFeedback"=>$e->Helpers->MooRequirejs->assetUrlJS("Feedback.js/feedback24.js"),
    		));
                        
               $e->Helpers->MooPopup->register('themeModal');

    	}
    }

    public function afterShare($event)
    {
        $data = $event->data['data'];
        if (isset($data['item_type']) && $data['item_type'] == 'Feedback_Feedback') {
            $blog_id = isset($data['parent_id']) ? $data['parent_id'] : 0;
            $blogModel = MooCore::getInstance()->getModel('Feedback.Feedback');
            $blogModel->updateAll(array('Feedback.share_count' => 'Feedback.share_count + 1'), array('Feedback.id' => $blog_id));
        }
    }
    
    public function afterLoadMooCore($event){
        $show_shortcut = true;
        $v = $event->subject();
        $params_request = $v->request->params;
        if(empty($params_request['plugin']) && $params_request['controller'] == 'share'){
           $show_shortcut = false;
        }       
        if(Configure::read('Feedback.feedback_enabled') && $show_shortcut){        
            $v->Helpers->Html->scriptBlock( 'require(["jquery","mooFeedback"], function($, mooFeedback){$(document).ready(function(){mooFeedback.initShortcut();})});', array( 'inline' => false, 'block' => 'mooScript' ) );
        }
    }
    
    public function search($event)
    {
        if(Configure::read('Feedback.feedback_enabled'))
        {
            $e = $event->subject();
            $mFeedback = MooCore::getInstance()->getModel('Feedback.Feedback');
            $results = $mFeedback->getFeedbacks('search', $e->keyword);

            if(isset($e->plugin) && $e->plugin == 'Feedback')
    		{
    			$e->set('feedbacks', $results);
    			$e->render("Feedback.Elements/search_feedback_list");
    		}
    		else
    		{
    			$event->result['Feedback']['header'] = __d('feedback',"Feedback");
    			$event->result['Feedback']['icon_class'] = "edit";
    			$event->result['Feedback']['view'] = "search_feedback_list";
    			if(!empty($results))
    				$event->result['Feedback']['notEmpty'] = 1;
                $e->set('feedbacks', $results);
    		}
        }

        
    }
    
    public function suggestion($event)
    {
        if(Configure::read('Feedback.feedback_enabled'))
        {
            $e = $event->subject();
            $mFeedback = MooCore::getInstance()->getModel('Feedback.Feedback');
            
            $event->result['feedback']['header'] = __('Feedback');
            $event->result['feedback']['icon_class'] = 'edit';
            if(isset($event->data['type']) && $event->data['type'] == 'all')
            {
                $feedbacks = $mFeedback->getFeedbacks('search', $e->request->data['searchVal']);
                $helper = MooCore::getInstance()->getHelper("Feedback_Feedback");
                foreach($feedbacks as $index => &$detail)
                {
                    $event->result['feedback'][$index]['id'] = $detail['Feedback']['id'];
                    $event->result['feedback'][$index]['img'] = $helper->getImageForSuggestion($detail['Feedback']['id'],array('prefix'=>'75_square'));
                    $event->result['feedback'][$index]['title'] = $detail['Feedback']['title'];
                    $event->result['feedback'][$index]['find_name'] = 'Find Feedback';
                    $event->result['feedback'][$index]['icon_class'] = 'edit';
                    $event->result['feedback'][$index]['view_link'] = 'feedback/feedbacks/view/';
                    
                    $mooHelper = MooCore::getInstance()->getHelper('Core_Moo');
                    
                    $utz = ( !is_numeric(Configure::read('core.timezone')) ) ? Configure::read('core.timezone') : 'UTC';
                    $cuser = MooCore::getInstance()->getViewer();
                    // user timezone
                    if ( !empty( $cuser['User']['timezone'] ) ){
                        $utz = $cuser['User']['timezone'];
                    }
                    
                    $event->result['feedback'][$index]['more_info'] = $mooHelper->getTime( $detail['Feedback']['created'], Configure::read('core.date_format'), $utz );
                }
            }
            elseif(isset($event->data['type']) && $event->data['type'] == 'feedback')
            {
                $results = $mFeedback->getFeedbacks('search', $e->request->pass[1]);
                $e->set('feedbacks', $results);
                $e->set('element_list_path',"Feedback.search_feedback_list");
                
                $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;
                $feedbacks = $mFeedback->getFeedbacks('search', $e->request->pass[1], $page);
                $e->set('feedbacks', $feedbacks);
                $e->set('result',1);
                $e->set('more_url','/search/suggestion/feedback/'.$e->request->pass[1]. '/page:' . ( $page + 1 ));
                $e->set('element_list_path',"Feedback.search_feedback_list");
            }
        }
    }
    
    public function hashtags($event)
    {
        $enable = Configure::read('Feedback.feedback_hashtag_enabled');
        $feedbacks = array();
        $e = $event->subject();
        App::import('Model', 'Feedback.Feedback');
        App::import('Model', 'Tag');
        $this->Tag = new Tag();
        $this->Feedback = new Feedback();
        $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;

        $uid = CakeSession::read('uid');
        if($enable)
        {
            $feedbacks = $this->Feedback->getFeedbackHashtags($event->data['search_keyword'],RESULTS_LIMIT,$page);
            $feedbacks = $this->_filterFeedback($feedbacks);
            /*if(isset($event->data['type']) && $event->data['type'] == 'feedbacks')
            {
                $feedbacks = $this->Feedback->getFeedbackHashtags($event->data['item_ids'],RESULTS_LIMIT,$page);
                $feedbacks = $this->_filterFeedback($feedbacks);
            }
            $table_name = $this->Feedback->table;
            if(isset($event->data['type']) && $event->data['type'] == 'all' && !empty($event->data['item_groups'][$table_name]) )
            {
                $feedbacks = $this->Feedback->getFeedbackHashtags($event->data['item_groups'][$table_name],5);
                $feedbacks = $this->_filterFeedback($feedbacks);
            }*/
        }
        // get tagged item
        $tag = h(urldecode($event->data['search_keyword']));
        $tags = $this->Tag->find('all', array('conditions' => array(
            'Tag.type' => 'Feedback_Feedback',
            'Tag.tag' => $tag
        )));
        $feedback_ids = Hash::combine($tags,'{n}.Tag.id', '{n}.Tag.target_id');

        $friendModel = MooCore::getInstance()->getModel('Friend');

        $items = $this->Feedback->find('all', array('conditions' => array(
                'Feedback.id' => $feedback_ids
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
            $feedbacks = array_merge($feedbacks, $items);
        }

        //only display 5 items on All Search Result page
        if($feedbacks != null && isset($event->data['type']) && $event->data['type'] == 'all')
        {
            $feedbacks = array_slice($feedbacks,0,5);
        }
        if($feedbacks != null)
        {
            $feedbacks = array_map("unserialize", array_unique(array_map("serialize", $feedbacks)));
        }
        if(!empty($feedbacks))
        {
            $event->result['feedbacks']['header'] = __d('feedback',"Feedbacks");
            $event->result['feedbacks']['icon_class'] = 'edit';
            $event->result['feedbacks']['view'] = "Feedback.search_feedback_list";
            if(isset($event->data['type']) && $event->data['type'] == 'feedbacks')
            {
                $e->set('result',1);
                $e->set('more_url','/search/hashtags/'.$e->params['pass'][0]. '/feedbacks/page:' . ( $page + 1 ));
                $e->set('element_list_path',"Feedback.search_feedback_list");
            }
            $e->set('feedbacks', $feedbacks);
        }
    }
    
    public function hashtags_filter($event){
         
        $e = $event->subject();
        App::import('Model', 'Feedback.Feedback');
        $this->Feedback = new Feedback();

        if(isset($event->data['type']) && $event->data['type'] == 'feedbacks')
        {
            $page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;
            $feedbacks = $this->Feedback->getFeedbackHashtags($event->data['item_ids'],RESULTS_LIMIT,$page);
            $e->set('feedbacks', $feedbacks);
            $e->set('result',1);
            $e->set('more_url','/search/hashtags/'.$e->params['pass'][0]. '/feedbacks/page:' . ( $page + 1 ));
            $e->set('element_list_path',"Feedback.search_feedback_list");
        }
        $table_name = $this->Feedback->table;
        if(isset($event->data['type']) && $event->data['type'] == 'all' && !empty($event->data['item_groups'][$table_name]) )
        {
            $event->result['feedbacks'] = null;

            $feedbacks = $this->Feedback->getFeedbackHashtags($event->data['item_groups'][$table_name],5);

            if(!empty($feedbacks))
            {
                $event->result['feedbacks']['header'] = __d('feedback',"Feedbacks");
                $event->result['feedbacks']['icon_class'] = 'edit';
                $event->result['feedbacks']['view'] = "Feedback.search_feedback_list";
                $e->set('feedbacks', $feedbacks);
            }
        }
    }

    private function _filterFeedback($feedbacks)
    {
        if(!empty($feedbacks))
        {
            $friendModel = MooCore::getInstance()->getModel('Friend');
            $viewer = MooCore::getInstance()->getViewer();
            foreach($feedbacks as $key => &$feedback)
            {
                $owner_id = $feedback[key($feedback)]['user_id'];
                $privacy = isset($feedback[key($feedback)]['privacy']) ? $feedback[key($feedback)]['privacy'] : 1;
                if (empty($viewer)){ // guest can view only public item
                    if ($privacy != PRIVACY_EVERYONE){
                        unset($feedbacks[$key]);
                    }
                }else{ // viewer
                    $aFriendsList = array();
                    $aFriendsList = $friendModel->getFriendsList($owner_id);
                    if ($privacy == PRIVACY_ME){ // privacy = only_me => only owner and admin can view items
                        if (!$viewer['Role']['is_admin'] && $viewer['User']['id'] != $owner_id){
                            unset($feedbacks[$key]);
                        }
                    }else if ($privacy == PRIVACY_FRIENDS){ // privacy = friends => only owner and friendlist of owner can view items
                        if (!$viewer['Role']['is_admin'] && $viewer['User']['id'] != $owner_id && !in_array($viewer['User']['id'], array_keys($aFriendsList))){
                            unset($feedbacks[$key]);
                        }
                    }else {

                    }
                }
            }
        }

        return $feedbacks;
    }

    public function hashtagEnable($event)
    {
        $enable = Configure::read('Feedback.feedback_hashtag_enabled');
        $event->result['feedbacks']['enable'] = $enable;
    }
    
    public function renderAdminStatistic($event){
        if(Configure::read('Feedback.feedback_enabled')) {
            $feedbackModel = MooCore::getInstance()->getModel('Feedback.Feedback');
            $event->result['statistics'][] = array(
                'id' => 'feedbacks',
                'href' => Router::url(array('admin' => true,'plugin' => 'feedback', 'controller'=>'feedbacks')),
                'icon' => '<i class="fa fa-share-square-o"></i> ',
                'name' => __d('feedback',"Feedback"),
                'item_count' => $feedbackModel->find('count'),
                'ordering' => 1
            );
        }
    }

    public function exportFeedbackCreate($e)
    {
    	$data = $e->data['data'];
    	$actorHtml = $e->data['actorHtml'];
    	
        $feedbackModel = MooCore::getInstance()->getModel("Feedback.Feedback");
        $feedbackModel->recursive = 2;
        $feedback = $feedbackModel->findById($data['Activity']['item_id']);
        $helper = MooCore::getInstance()->getHelper('Feedback_Feedback');
    	
    	list($title_tmp,$target) = $e->subject()->getActivityTarget($data,$actorHtml);
    	if(!empty($title_tmp)){
    		$title =  $title_tmp['title'];
    		$titleHtml = $title_tmp['titleHtml'];
    	}else{
    		$title = __d('feedback','created a new feedback');
    		$titleHtml = $actorHtml . ' ' . __d('feedback','created a new feedback');
    	}
    	$e->result['result'] = array(
            'type' => 'create',
            'title' => $title,
            'titleHtml' => $titleHtml,
            'objects' => array(
                    'type' => 'Feedback_Feedback',
                    'id' => $feedback['Feedback']['id'],
                    'url' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($feedback['Feedback']['moo_href'], 'UTF-8', 'UTF-8')),
                    'title' => h($feedback['Feedback']['moo_title']),
                    'images' => array('850'=> $helper->getImageForSuggestion($feedback['Feedback']['id'],array('prefix'=>'850'))),
            ),
            'target' => $target,
        );
    }
    
    public function exportFeedbackItemDetailShare($e)
    {
    	$data = $e->data['data'];
    	$actorHtml = $e->data['actorHtml'];
    	
        $feedbackModel = MooCore::getInstance()->getModel("Feedback.Feedback");
        $feedbackModel->recursive = 2;
        $feedback = $feedbackModel->findById($data['Activity']['parent_id']);
        $helper = MooCore::getInstance()->getHelper('Feedback_Feedback');
    	
    	$target = array();
    	
    	if (isset($data['Activity']['parent_id']) && $data['Activity']['parent_id'])
    	{    	
    		$title = $data['User']['name'] . ' ' . __d('feedback',"shared %s's feedback", $feedback['User']['name']);
    		$titleHtml = $actorHtml . ' ' . __d('feedback',"shared %s's feedback", $e->subject()->Html->link($feedback['User']['name'], FULL_BASE_URL . $feedback['User']['moo_href']));
	    	$target = array(
	    			'url' => FULL_BASE_URL . $feedback['User']['moo_href'],
	    			'id' => $feedback['User']['id'],
	    			'name' => $feedback['User']['name'],
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
                    'type' => 'Feedback_Feedback',
                    'id' => $feedback['Feedback']['id'],
                    'url' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($feedback['Feedback']['moo_href'], 'UTF-8', 'UTF-8')),
                    'title' => h($feedback['Feedback']['moo_title']),
                    'images' => array('850'=> $helper->getImageForSuggestion($feedback['Feedback']['id'],array('prefix'=>'850'))),
            ),
            'target' => $target,
        );
    }
}