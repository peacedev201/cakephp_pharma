<?php
App::uses('Widget','Controller/Widgets');

class relatedQuestionWidget extends Widget {
    public function beforeRender(Controller $controller) {
    	$question = MooCore::getInstance()->getSubject();
    	$questions = array();
    	if ($question)
    	{
    		$mapModel = MooCore::getInstance()->getModel("Question.QuestionTagMap");
    		$questionModel = MooCore::getInstance()->getModel("Question.Question");
    		$tags = $mapModel->getTag($question['Question']['id']);
    		$uid = MooCore::getInstance()->getViewer(true);    		
    		if (count($tags))
    		{
    			$tag_ids = array();
    			foreach ($tags as $tag)
    			{
    				$tag_ids[] = $tag['QuestionTagMap']['tag_id'];    				
    			}
    			$params = array('limit'=>$this->params['num_item_show'],'user_id'=>$uid);
    			$conditions = $questionModel->getConditionsQuestions($params);
    			$conditions = array_merge($conditions,array('Question.id <>' => $question['Question']['id'],'QuestionTagMap.tag_id'=>$tag_ids));    			
    			$questions = $questionModel->find('all',array(
    				'fields' => array(
				        'Question.*,User.*',
				        'COUNT(Question.id) AS `count`'
				    ),
    				'joins' => array(
    					array(
    						'table' => 'question_tag_maps',
    						'alias' => 'QuestionTagMap',
    						'type'  => 'INNER',
    						'conditions' => array(
    							'QuestionTagMap.question_id = Question.id'
    						)
    					)
    				),
    				'group'=>array('Question.id'),
    				'conditions' => $conditions,
    				'order' => array('count DESC'),
    				'limit' => $this->params['num_item_show']
    			));
    		}
    	}
    	$this->setData("questions", $questions);
    }
}