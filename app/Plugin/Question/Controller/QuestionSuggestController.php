<?php 
class QuestionSuggestController extends QuestionAppController{
    public function tag()
    {
    	$this->autoRender = false;
    	$this->response->type('json');
    	$result = array();
    	if (isset($this->request->query['search']))
    	{
    		$this->loadModel('Question.QuestionTag');
    		$key = $this->request->query['search'];
    		$key = strtolower($key);
    		$key = str_replace(' ', '', $key);
    		while (preg_match('/[#$%^&*()+=\-\[\]\';,.\/{}|":<>?~\\\\]/', $key,$results))
    		{
    			foreach ($results as $text)
    			{
    				$key = str_replace($text, '', $key);
    			}
    		}
    		$tags = $this->QuestionTag->find('all',array(
    			'conditions' => array(
    				'QuestionTag.title LIKE' => '%'.$key.'%',
    				'QuestionTag.status' => 1
    			),
    			'limit' => 10
    		));    		
    		foreach ($tags as $tag)
    		{
    			$result[] = array(
    				'value'=>$tag['QuestionTag']['id'],
    				'text'=>$tag['QuestionTag']['title'],
    			);
    		}
    		
    		$helper = MooCore::getInstance()->getHelper('Question_Question');
    		if ($helper->can('create_new_tag',MooCore::getInstance()->getViewer()) == QUESTION_CAN_ERROR_NONE)
    		{    		
	    		if (strlen($key) > 2)
	    		{
		    		$result[] = array(
		    			'value'=>'new_'.$key,
		    			'text'=>$key
		    		);
	    		}
    		}
    	}
    	$json = json_encode($result);
    	$this->response->body($json);
    }
}