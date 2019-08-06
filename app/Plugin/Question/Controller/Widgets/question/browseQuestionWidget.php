<?php
App::uses('Widget','Controller/Widgets');

class browseQuestionWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$uid = MooCore::getInstance()->getViewer(true);
    	$viewer = MooCore::getInstance()->getViewer();
    	$role_id = (empty($viewer)) ? ROLE_GUEST : $viewer['User']['role_id'];
    	$categoryModel =  MooCore::getInstance()->getModel('Category');
    	$categories = $categoryModel->getCategoriesList('Question',$role_id);
    	
    	$type = (isset($controller->viewVars['type']) ? $controller->viewVars['type'] : 'all');
    	$category = isset($controller->request->query['category']) ? $controller->request->query['category'] : '';
    	$keyword = isset($controller->request->query['keyword']) ? $controller->request->query['keyword'] : '';
    	$url = $type.'?category='.$category.'&keyword='.htmlspecialchars($keyword);
    	$is_tag = false;
    	$tag = null;
    	if ($keyword)
    	{
    		$tmp = htmlspecialchars_decode($keyword);
    		if ($tmp[0] == '[' && $tmp[strlen($tmp) - 1] == ']')
    		{
    			$tmp = substr($tmp,1,strlen($tmp) - 2);
    			$controller->loadModel("Question.QuestionTag");
    			$tag = $controller->QuestionTag->findByTitle($tmp);
    			if ($tag)
    			{
    				$is_tag = true;
    				$keyword = htmlspecialchars_decode($keyword);
    			}
    		}
    	}
    	
    	$tab = isset($controller->request->query['tab']) ? $controller->request->query['tab'] : 'last';
    	$questionModel = MooCore::getInstance()->getModel('Question.Question');
    	   	
    	$params = array();
    	$params['type'] = $type;
    	$params['user_id'] =  $uid;
    	
    	if ($category)
    		$params['category'] = $category;
    	if ($keyword && !$is_tag)
    		$params['search'] = $keyword;
    	
    	$cond = $questionModel->getConditionsQuestions($params);
    	$scope = array();
    	if ($is_tag)
    	{
    		$scope['joins'] = array(
                 array(
					'table' => Configure::read('core.prefix').'question_tag_maps',
					'alias' => 'QuestionTagMap',
					'conditions' => 'QuestionTagMap.question_id = Question.id'
				)
    		);
    		$cond['QuestionTagMap.tag_id'] = $tag['QuestionTag']['id'];
    	}
    	if ($type == 'favorites')
    	{
    		$scope['joins'] = array(
    				array(
    						'table' => Configure::read('core.prefix').'question_favorites',
    						'alias' => 'QuestionFavorite',
    						'conditions' => 'QuestionFavorite.question_id = Question.id'
    				)
    		);
    		$cond['QuestionFavorite.user_id'] = $uid;
    	}
    	switch ($tab) {
    		case 'last':
    			$scope['order']['Question.id'] = 'DESC';
    			break;
    		case 'active':
    			$scope['order']['Question.answer_count'] = 'DESC';
    			break;
    		case 'votes':
    			$scope['order']['Question.vote_count'] = 'DESC';
    			break;
    		case 'unanswered':
    			$scope['order']['Question.answer_count'] = 'ASC';
    			break;
    		case 'feature':
    			$scope['order']['Question.feature'] = 'DESC';
    			break;
    		default:
    			$tab = 'last';
    			$scope['order']['Question.id'] = 'DESC';
    			break;
    	}
    	$scope['conditions'] = $cond;
    	$scope['limit'] = Configure::read('Question.question_item_per_pages');
    	$scope['paramType'] = 'querystring';
    	
    	$controller->Paginator->settings = $scope;
    	
    	$questions = $controller->Paginator->paginate('Question');
    	if ($controller->request->is('androidApp') || $controller->request->is('iosApp'))
    	{
    		$controller->set('categories', $categories);
    		$controller->set('questions', $questions);
    		$controller->set('tab', $tab);
    		$controller->set('type',$type);
    		$controller->set('category',$category);
    		$controller->set('keyword',$keyword);
    		$controller->set('url',$url);
    		$controller->set('is_tag',$is_tag);
    		$controller->set('tag',$tag);
    	}
    	else
    	{
    		$this->setData('categories', $categories);
    		$this->setData('questions', $questions);
    		$this->setData('tab', $tab);
    		$this->setData('type',$type);
    		$this->setData('category',$category);
    		$this->setData('keyword',$keyword);
    		$this->setData('url',$url);
    		$this->setData('is_tag',$is_tag);
    		$this->setData('tag',$tag);
    	}
    }
}