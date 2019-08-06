<?php
App::uses('CakeEventListener', 'Event');

class QuestionListener implements CakeEventListener
{
    public function implementedEvents()
    {
        return array(
        	'MooView.beforeRender' => 'beforeRender',
        	'Controller.Share.afterShare' => 'afterShare',
        	'Controller.Widgets.tagCoreWidget' => 'hashtagEnable',
        	'Controller.Search.search' => 'search',
        	'Controller.Search.suggestion' => 'suggestion',
        	'Controller.Search.hashtags_filter' => 'hashtags_filter',
        	'Controller.Search.hashtags' => 'hashtags',
        	'welcomeBox.afterRenderMenu' => 'welcomeBoxAfterRenderMenu',
        	'profile.afterRenderMenu'=> 'profileAfterRenderMenu',
        	'Model.beforeDelete' => 'doAfterDelete',
        	'Plugin.View.Api.Search' => 'apiSearch',
        	'Controller.Home.adminIndex.Statistic' => 'statistic',
        		
        	'StorageHelper.questions.getUrl.local' => 'storage_geturl_local',
        	'StorageHelper.questions.getUrl.amazon' => 'storage_geturl_amazon',
        	'StorageAmazon.questions.getFilePath' => 'storage_amazon_get_file_path',
        	
        	'StorageTaskAwsCronTransfer.execute' => 'storage_task_transfer',
        		
        	'StorageAmazon.photos.putObject.success.Question' => 'storage_amazon_photo_put_success_callback',
        	'StorageAmazon.photos.putObject.success.QuestionAnswer' => 'storage_amazon_photo_put_success_callback_answer',
        		
        	'ApiHelper.renderAFeed.question_create' => 'exportQuestionCreate',
        	'ApiHelper.renderAFeed.question_item_detail_share' => 'exportQuestionItemDetailShare',
        	'ApiHelper.renderAFeed.answer_question' => 'exportAnswerCreate',
        	'profile.mooApp.afterRenderMenu' => 'apiAfterRenderMenu'
        );
    }
    
    public function apiAfterRenderMenu($e)
    {
    	$subject = MooCore::getInstance()->getSubject();
    	$e->data['result']['question'] = array(
			'text' => __d('question','Questions'),
			'url' => FULL_BASE_URL . $e->subject()->request->base . '/questions/browse/profile/'. $subject['User']['id'],
			'cnt' => 0
    	);
    }
    
    public function exportAnswerCreate($e)
    {
    	$data = $e->data['data'];
    	$actorHtml = $e->data['actorHtml'];
    	
    	$questionModel = MooCore::getInstance()->getModel("Question_Question");
    	$question= $questionModel->findById($data['Activity']['item_id']);

    	list($title_tmp,$target) = $e->subject()->getActivityTarget($data,$actorHtml);
    	if(!empty($title_tmp)){
    		$title =  $title_tmp['title'];
    		$titleHtml = $title_tmp['titleHtml'];
    	}else{
    		$title = __d('question',"answered %s's question: %s",$question['User']['moo_title'],$question['Question']['moo_title']);
    		$titleHtml = $actorHtml . ' ' . __d('question',"answered %s's question: %s",$e->subject()->Moo->getName($question['User']),'<a href="'.$question['Question']['moo_href'].'">'.$question['Question']['moo_title'].'</a>');
    	}
    	
    	$e->result['result'] = array(
    			'type' => 'create',
    			'title' => $title,
    			'titleHtml' => $titleHtml,
    			'target' => $target,
    	);
    }
    
    public function exportQuestionCreate($e)
    {
    	$data = $e->data['data'];
    	$actorHtml = $e->data['actorHtml'];
    	
    	$questionModel = MooCore::getInstance()->getModel("Question_Question");
    	$question= $questionModel->findById($data['Activity']['params']);
    	$helper = MooCore::getInstance()->getHelper('Question_Question');
    	
    	list($title_tmp,$target) = $e->subject()->getActivityTarget($data,$actorHtml);
    	if(!empty($title_tmp)){
    		$title =  $title_tmp['title'];
    		$titleHtml = $title_tmp['titleHtml'];
    	}else{
    		$title = __d('question','created a new question');
    		$titleHtml = $actorHtml . ' ' . __d('question','created a new question');
    	}
    	$e->result['result'] = array(
    			'type' => 'create',
    			'title' => $title,
    			'titleHtml' => $titleHtml,
    			'objects' => array(
    					'type' => 'Question_Question',
    					'id' => $question['Question']['id'],
    					'url' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($question['Question']['moo_href'], 'UTF-8', 'UTF-8')),
    					'description' => $e->subject()->Text->convert_clickable_links_for_hashtags($e->subject()->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $question['Question']['description'])), 200, array('eclipse' => '')), Configure::read('Question.question_hashtag_enabled')),
    					'title' => h($question['Question']['moo_title']),
    					'images' => array('850'=>$helper->getImage($question,array('prefix'=>''))),
    			),
    			'target' => $target,
    			'isActivityView' => true
    	);
    }
    
    public function exportQuestionItemDetailShare($e)
    {
    	$data = $e->data['data'];
    	$actorHtml = $e->data['actorHtml'];
    	
    	$questionModel= MooCore::getInstance()->getModel("Question_Question");
    	$question= $questionModel->findById($data['Activity']['parent_id']);
    	$helper = MooCore::getInstance()->getHelper('Question_Question');
    	
    	$target = array();
    	
    	if (isset($data['Activity']['parent_id']) && $data['Activity']['parent_id'])
    	{
    		$title = $data['User']['name'] . ' ' . __d('question',"shared %s's question", $question['User']['name']);
    		$titleHtml = $actorHtml . ' ' . __d('question',"shared %s's question", $e->subject()->Html->link($question['User']['name'], FULL_BASE_URL . $question['User']['moo_href']));
    		$target = array(
    				'url' => FULL_BASE_URL . $question['User']['moo_href'],
    				'id' => $question['User']['id'],
    				'name' => $question['User']['name'],
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
    					'type' => 'Question_Question',
    					'id' => $question['Question']['id'],
    					'url' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($question['Question']['moo_href'], 'UTF-8', 'UTF-8')),
    					'description' => $e->subject()->Text->convert_clickable_links_for_hashtags($e->subject()->Text->truncate(strip_tags(str_replace(array('<br>', '&nbsp;'), array(' ', ''), $question['Question']['description'])), 200, array('eclipse' => '')), Configure::read('Question.question_hashtag_enabled')),
    					'title' => h($question['Question']['moo_title']),
    					'images' => array('850'=>$helper->getImage($question,array('prefix'=>''))),
    			),
    			'target' => $target,
    	);
    }
    
    public function storage_amazon_photo_put_success_callback_answer($e)
    {
    	$photo = $e->data['photo'];
    	$path= $e->data['path'];
    	$url= $e->data['url'];
    	if (Configure::read('Storage.storage_cloudfront_enable') == "1"){
    		$url = rtrim(Configure::read('Storage.storage_cloudfront_cdn_mapping'),"/")."/".$e->data['key'];
    	}
    	$questionModel = MooCore::getInstance()->getModel('Question.QuestionAnswer');
    	$questionModel->clear();
    	$question = $questionModel->find("first",array(
    			'conditions' => array("QuestionAnswer.id"=>$photo['Photo']['target_id']),
    	));
    	if($question){
    		$findMe = str_replace(WWW_ROOT,"",$path);
    		$isReplaced = false;
    		$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
    		if(preg_match_all("/$regexp/siU", $question['QuestionAnswer']['description'], $matches)) {
    			foreach ($matches[2] as $match){
    				if(strpos($match, $findMe) !== false){
    					$isReplaced = true;
    					$question['QuestionAnswer']['description'] = str_replace($match,$url,$question['QuestionAnswer']['description']);
    				}
    			}
    		}
    		$regexp = "<img\s[^>]*src=(\"??)([^\" >]*?)\\1[^>]*>";
    		if(preg_match_all("/$regexp/siU", $question['QuestionAnswer']['description'], $matches)) {
    			foreach ($matches[2] as $match){
    				if(strpos($match, $findMe) !== false){
    					$isReplaced = true;
    					$question['QuestionAnswer']['description'] = str_replace($match,$url,$question['QuestionAnswer']['description']);
    				}
    			}
    		}
    		if($isReplaced){
    			$questionModel->clear();
    			$questionModel->save($question);
    		}
    	}
    }
    
    public function storage_amazon_photo_put_success_callback($e){
    	$photo = $e->data['photo'];
    	$path= $e->data['path'];
    	$url= $e->data['url'];
    	if (Configure::read('Storage.storage_cloudfront_enable') == "1"){
    		$url = rtrim(Configure::read('Storage.storage_cloudfront_cdn_mapping'),"/")."/".$e->data['key'];
    	}
    	$questionModel = MooCore::getInstance()->getModel('Question.Question');
    	$questionModel->clear();
    	$question = $questionModel->find("first",array(
    		'conditions' => array("Question.id"=>$photo['Photo']['target_id']),
    	));
    	if($question){
    		$findMe = str_replace(WWW_ROOT,"",$path);
    		$isReplaced = false;
    		$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
    		if(preg_match_all("/$regexp/siU", $question['Question']['description'], $matches)) {
    			foreach ($matches[2] as $match){
    				if(strpos($match, $findMe) !== false){
    					$isReplaced = true;
    					$question['Question']['description'] = str_replace($match,$url,$question['Question']['description']);
    				}
    			}
    		}
    		$regexp = "<img\s[^>]*src=(\"??)([^\" >]*?)\\1[^>]*>";
    		if(preg_match_all("/$regexp/siU", $question['Question']['description'], $matches)) {
    			foreach ($matches[2] as $match){
    				if(strpos($match, $findMe) !== false){
    					$isReplaced = true;
    					$question['Question']['description'] = str_replace($match,$url,$question['Question']['description']);
    				}
    			}
    		}
    		if($isReplaced){
    			$questionModel->clear();
    			$questionModel->save($question);
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
    	
    	if ($e->data['thumb']) {
    		$url = FULL_BASE_LOCAL_URL . $request->webroot . 'uploads/questions/thumbnail/' . $oid . '/' . $prefix . $thumb;
    	} else {
    		//$url = FULL_BASE_LOCAL_URL . $v->assetUrl('Blog.noimage/blog.png', array('prefix' => rtrim($prefix, "_"), 'pathPrefix' => Configure::read('App.imageBaseUrl')));
    		$url = $v->getImage("question/img/noimage/question.png");
    	}
    	
    	$e->result['url'] = $url;
    }
    
    public function storage_geturl_amazon($e)
    {
    	$v = $e->subject();
    	$type = $e->data['type'];
    	
    	$e->result['url'] = $v->getAwsURL($e->data['oid'], "questions", $e->data['prefix'], $e->data['thumb']);
    	
    }
    
    public function storage_amazon_get_file_path($e)
    {
    	$objectId = $e->data['oid'];
    	$name = $e->data['name'];
    	$thumb = $e->data['thumb'];
    	$type = $e->data['type'];;
    	$path = false;
    	
    	if (!empty($thumb)) {
    		$path = WWW_ROOT . "uploads" . DS . "questions" . DS . "thumbnail" . DS . $objectId . DS . $name . $thumb;
    	}
    	
    	$e->result['path'] = $path;
    }
    
    public function storage_task_transfer($e)
    {
    	$v = $e->subject();
    	$questionModel = MooCore::getInstance()->getModel('Question.Question');
    	$questions = $questionModel->find('all', array(
    			'conditions' => array("Question.id > " => $v->getMaxTransferredItemId("questions")),
    			'limit' => 10,
    			'fields'=>array('Question.id','Question.thumbnail'),
    			'order' => array('Question.id'),
    	)
    			);
    	
    	if($questions){
    		$photoSizes = $v->photoSizes();
    		foreach($questions as $question){
    			if (!empty($question["Question"]["thumbnail"])) {
    				if (!empty($question["Question"]["thumbnail"])) {
    					foreach ($photoSizes as $size){
    						$v->transferObject($question["Question"]['id'],"questions",$size.'_',$question["Question"]["thumbnail"]);
    					}
    					$v->transferObject($question["Question"]['id'],"questions",'',$question["Question"]["thumbnail"]);
    				}
    			}
    		}
    	}
    }
    
    public function statistic($event)
    {
    	$request = Router::getRequest();
    	$questionModel = MooCore::getInstance()->getModel("Question.Question");
    	$event->result['statistics'][] = array(
    			'item_count' => $questionModel->find('count'),
    			'ordering' => 9999,
    			'name' => __d('question','Questions'),
    			'href' => $request->base.'/admin/question/questions',
    			'icon' => '<i class="fa fa-question-circle"></i>'
    	);
    }
    
    public function apiSearch($event)
    {
    	$view = $event->subject();
    	$items = &$event->data['items'];
    	$type = $event->data['type'];
    	$viewer = MooCore::getInstance()->getViewer();
    	$utz = $viewer['User']['timezone'];
    	if ($type == 'Question' && isset($view->viewVars['questions']) && count($view->viewVars['questions']))
    	{
    		$helper = MooCore::getInstance()->getHelper('Question_Question');
    		foreach ($view->viewVars['questions'] as $item){
    			$items[] = array(
    					'id' => $item["Question"]['id'],
    					'url' => FULL_BASE_URL.$item['Question']['moo_href'],
    					'avatar' =>  $helper->getImage($item),
    					'owner_id' => $item["Question"]['user_id'],
    					'title_1' => $item["Question"]['moo_title'],
    					'title_2' => __( 'Posted by') . ' ' . $view->Moo->getNameWithoutUrl($item['User'], false) . ' ' .$view->Moo->getTime( $item["Question"]['created'], Configure::read('core.date_format'), $utz ),
    					'created' => $item["Question"]['created'],
    					'type' => "Question",
    					'type_title' => __d('question',"Question")
    			);
    		}
    	}
    }
    
    public function profileAfterRenderMenu($event)
    {
    	$view = $event->subject();
    	$uid = MooCore::getInstance()->getViewer(true);
    	if(Configure::read('Question.question_enabled')){
    		$questionModel = MooCore::getInstance()->getModel('Question_Question');
    		$subject = MooCore::getInstance()->getSubject();
    		$total = $questionModel->getTotalQuestions(array('owner_id'=>$subject['User']['id'],'user_id'=>$uid));
    		echo $view->element('menu_profile',array('count'=>$total),array('plugin'=>'Question'));
    	}
    }
    
    public function doAfterDelete($event)
    {
    	$model = $event->subject();
    	$type = ($model->plugin) ? $model->plugin.'_' : ''.get_class($model);
    	if ($type == 'User')
    	{
    		//delete question
    		$questionModel = MooCore::getInstance()->getModel('Question_Question');
    		$questions = $questionModel->find('all',array(
    			'conditions'=>array(
    				'Question.user_id' => $model->id
    			)
    		));
    		foreach ($questions as $question)
    		{
    			$questionModel->delete($question['Question']['id']);
    		}
    		
    		//delete answer
    		$answerModel = MooCore::getInstance()->getModel('Question.QuestionAnswer');
    		$answers = $answerModel->find('all',array(
    			'conditions'=>array(
    				'QuestionAnswer.user_id' => $model->id
    			)
    		));    		
    		
    		foreach ($answers as $answer)
    		{    			
    			$answerModel->delete($answer['QuestionAnswer']['id']);
    		}
    		
    		//delete comment
    		$commentModel = MooCore::getInstance()->getModel('Question.QuestionComment');
    		$comments = $commentModel->find('all',array(
    			'conditions'=>array(
    				'QuestionComment.user_id' => $model->id
    			)
    		));
    		foreach ($comments as $comment)
    		{
    			$commentModel->delete($comment['QuestionComment']['id']);
    		}
    		
    		//delete favorite
    		$favoriteModel = MooCore::getInstance()->getModel('Question.QuestionFavorite');
    		$favorites = $favoriteModel->find('all',array(
    			'conditions'=>array(
    				'QuestionFavorite.user_id' => $model->id
    			)
    		));
    		foreach ($favorites as $favorite)
    		{
    			$favoriteModel->delete($favorite['QuestionFavorite']['id']);
    		}
    		
    		//delete vote
    		$voteModel = MooCore::getInstance()->getModel('Question.QuestionVote');
    		$votes = $voteModel->find('all',array(
    			'conditions'=>array(
    				'QuestionVote.user_id' => $model->id
    			)
    		));
    		foreach ($votes as $vote)
    		{
    			$voteModel->delete($vote['QuestionVote']['id']);
    		}
    		
    		//delete user
    		$userModel = MooCore::getInstance()->getModel('Question.QuestionUser');
    		$user = $userModel->getUser($model->id);
    		if ($user)
    		{
    			$userModel->delete($user['QuestionUser']['id']);
    		}
    		
    	}
    }
    
    public function welcomeBoxAfterRenderMenu($event)
    {
    	$view = $event->subject();
    	$uid = MooCore::getInstance()->getViewer(true);
    	if(Configure::read('Question.question_enabled') && $uid){
    		$questionModel = MooCore::getInstance()->getModel('Question_Question');
    		$total = $questionModel->getTotalQuestions(array('type'=>'my','user_id'=>$uid));
    		echo $view->element('menu_welcome',array('count'=>$total),array('plugin'=>'Question'));
    	}
    }
    
    public function hashtags($event)
    {
    	if(Configure::read('Question.question_enabled')){
    		$enable = Configure::read('Question.question_hashtag_enabled');
    		$questions = array();
    		$e = $event->subject();
    		$tagModel = MooCore::getInstance()->getModel('Tag');
    		$questionModel = MooCore::getInstance()->getModel('Question.Question');
    		$page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;
    
    		$uid = MooCore::getInstance()->getViewer(true);
    		if($enable)
    		{
    			if(isset($event->data['type']) && $event->data['type'] == 'questions')
    			{
    				$questions = $questionModel->getQuestions(array('user_id'=>$uid,'page'=>$page,'ids'=>$event->data['item_ids']));
    			}
    			$table_name = $questionModel->table;
    			if(isset($event->data['type']) && $event->data['type'] == 'all' && !empty($event->data['item_groups'][$table_name]) )
    			{
    				$questions = $questionModel->getQuestions(array('user_id'=>$uid,'limit'=>5,'ids'=>$event->data['item_groups'][$table_name]));
    			}
    		}
    
    		// get tagged item
    		$tag = h(urldecode($event->data['search_keyword']));
    		$tags = $tagModel->find('all', array('conditions' => array(
    				'Tag.type' => 'Question_Question',
    				'Tag.tag' => $tag
    		)));
    		$question_ids = Hash::combine($tags,'{n}.Tag.id', '{n}.Tag.target_id');
    		$items = $questionModel->getQuestions(array('user_id'=>$uid,'page'=>$page,'ids'=>$question_ids));
    		 
    		$questions = array_merge($questions, $items);
    
    		//only display 5 items on All Search Result page
    		if(isset($event->data['type']) && $event->data['type'] == 'all')
    		{
    			$questions = array_slice($questions,0,5);
    		}
    		$questions = array_map("unserialize", array_unique(array_map("serialize", $questions)));
    		if(!empty($questions))
    		{
    			$event->result['questions']['header'] = __d('question','Questions');
    			$event->result['questions']['icon_class'] = 'question_answer';
    			$event->result['questions']['view'] = "Question.lists/questions";
    			if(isset($event->data['type']) && $event->data['type'] == 'questions')
    			{
    				$e->set('result',1);
    				$e->set('more_url','/search/hashtags/'.$e->params['pass'][0]. '/questions/page:' . ( $page + 1 ));
    				$e->set('element_list_path',"Question.lists/questions");
    			}
    			$e->set('questions', $questions);
    		}
    	}
    }
    
    public function hashtags_filter($event)
    {
    	if(Configure::read('Question.question_enabled')){
    		$e = $event->subject();
    		$questionModel = MooCore::getInstance()->getModel('Question_Question');
    		$uid = MooCore::getInstance()->getViewer(true);
    
    		if(isset($event->data['type']) && $event->data['type'] == 'questions')
    		{
    			$page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;
    			$questions = $questionModel->getQuestions(array('user_id'=>$uid,'page'=>$page,'ids'=>$event->data['item_ids']));
    			$e->set('questions', $questions);
    			$e->set('result',1);
    			$e->set('more_url','/search/hashtags/'.$e->params['pass'][0]. '/questions/page:' . ( $page + 1 ));
    			$e->set('element_list_path',"Question.lists/questions");
    		}
    		$table_name = $questionModel->table;
    		if(isset($event->data['type']) && $event->data['type'] == 'all' && !empty($event->data['item_groups'][$table_name]) )
    		{
    			$event->result['questions'] = null;
    
    			$questions = $questionModel->getQuestions(array('user_id'=>$uid,'limit'=>5,'ids'=>$event->data['item_groups'][$table_name]));
    
    			if(!empty($questions))
    			{
    				$event->result['questions']['header'] = __d('question','Questions');
    				$event->result['questions']['icon_class'] = 'question_answer';
    				$event->result['questions']['view'] = "Question.lists/questions";
    				$e->set('questions', $questions);
    			}
    		}
    	}
    }
    
    public function afterShare($event){
    	$data = $event->data['data'];
    	if (isset($data['item_type']) && $data['item_type'] == 'Question_Question'){
    		$question_id = isset($data['parent_id']) ? $data['parent_id'] : 0;
    		$questionModel = MooCore::getInstance()->getModel('Question.Question');
    		$questionModel->updateAll(array('Question.share_count' => 'Question.share_count + 1'), array('Question.id' => $question_id));
    	}
    }
    
    public function hashtagEnable($event)
    {
    	if(Configure::read('Question.question_enabled')){
    		$enable = Configure::read('Question.question_hashtag_enabled');
    		$event->result['questions']['enable'] = $enable;
    	}
    }
    
	public function beforeRender($event)
    {    	
    	if(Configure::read('Question.question_enabled')){
    		$e = $event->subject();
    		$e->Helpers->Html->css( array(
					'Question.main'
				),
				array('block' => 'css')
			);
    		
    		if (Configure::read('debug') == 0){
    			$min="min.";
    		}else{
    			$min="";
    		}
    		$e->Helpers->MooRequirejs->addPath(array(
    			"mooQuestion"=>$e->Helpers->MooRequirejs->assetUrlJS("Question.js/main.{$min}js"),
    			"mooJqueryTokenize" => $e->Helpers->MooRequirejs->assetUrlJS("Question.js/jquery.tokenize.js")
    		));
    		
    		$e->Helpers->MooRequirejs->addShim(array(
    			'mooJqueryTokenize'=>array("deps" =>array('jquery')),
    		));
    		
    		$e->addPhraseJs(array(
    			'delete_question_confirm' => __d('question','Are you sure you want to delete this question?')
    		));
    		
    		$e->Helpers->MooPopup->register('questionModal');
    	}
    }
    
    public function search($event)
    {
    	if(Configure::read('Question.question_enabled')){
    		$e = $event->subject();
    		$uid = MooCore::getInstance()->getViewer(true);
    		$questionModel = MooCore::getInstance()->getModel('Question_Question');
    		$results = $questionModel->getQuestions(array('search'=>$e->keyword,'user_id'=>$uid,'type' => 'all','limit'=>4));
    		 
    		if(isset($e->plugin) && $e->plugin == 'Question')
    		{
    			$e->set('questions', $results);
    			$e->render("Question.Elements/lists/questions");
    			$e->set('no_list_id',true);
    		}
    		else
    		{
    			$event->result['Question']['header'] = __d('question',"Question");
    			$event->result['Question']['icon_class'] = "question_answer";
    			$event->result['Question']['view'] = "lists/questions";
    			$e->set('no_list_id',true);
    			if(!empty($results))
    				$event->result['Question']['notEmpty'] = 1;
    				$e->set('questions', $results);
    		}
    	}
    }
    
    public function suggestion($event)
    {
    	if(Configure::read('Question.question_enabled')){
    		$e = $event->subject();
    		$questionModel = MooCore::getInstance()->getModel('Question_Question');
    		$uid = MooCore::getInstance()->getViewer(true);
    
    		$event->result['question']['header'] = __d('question',"Question");
    		$event->result['question']['icon_class'] = 'question_answer';
    
    		if(isset($event->data['type']) && $event->data['type'] == 'question')
    		{
    			$page = (!empty($e->request->named['page'])) ? $e->request->named['page'] : 1;
    			$questions = $questionModel->getQuestions(array('page'=>$page,'user_id'=>$uid,'search'=>$event->data['searchVal']));
    			$questions_next = $questionModel->getQuestions(array('page'=>$page + 1,'user_id'=>$uid,'search'=>$event->data['searchVal']));
    
    			$e->set('questions', $questions);
    			$e->set('result',1);
    			$e->set('no_list_id',true);
    			if ($questions_next && count($questions_next))
    				$e->set('is_view_more',true);
    			
    			$e->set('url_more','/search/suggestion/question/'.$e->params['pass'][1]. '/page:' . ( $page + 1 ));
    			$e->set('element_list_path',"Question.lists/questions");
    		}
    		if(isset($event->data['type']) && $event->data['type'] == 'all')
    		{
    			$event->result['question'] = null;
    			$questions = $questionModel->getQuestions(array('page'=>1,'limit'=>2,'user_id'=>$uid,'search'=>$event->data['searchVal']));
    			$helper = MooCore::getInstance()->getHelper('Question_Question');
    			$mooHelper = MooCore::getInstance()->getHelper('Core_Moo');
    			 
    			if(!empty($questions)){
    				$event->result['question'] = array(__d('question','Question'));
    				foreach($questions as $index=>$detail){
    					$index++;
    					$event->result['question'][$index]['id'] = $detail['Question']['id'];
    					$event->result['question'][$index]['img'] = $helper->getImage($detail);
    					 
    					$event->result['question'][$index]['title'] = $detail['Question']['title'];
    					$event->result['question'][$index]['find_name'] = __d('question','Find Question');
    					$event->result['question'][$index]['icon_class'] = 'question_answer';
    					$event->result['question'][$index]['view_link'] = 'questions/view/';
    					 
    					$event->result['question'][$index]['more_info'] = __d('question','Posted by') . ' ' . $mooHelper->getNameWithoutUrl($detail['User'], false) . ' ' . $mooHelper->getTime( $detail['Question']['created'], Configure::read('core.date_format'), $e->viewVars['utz'] );
    				}
    			}
    		}
    	}
    }
}