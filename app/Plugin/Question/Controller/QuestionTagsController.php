<?php 
class QuestionTagsController extends QuestionAppController{
	public $components = array('Paginator');
	
	public function admin_index() {
        $this->set('title_for_layout', __d('question','Tag Manager'));
        $this->loadModel('Question.QuestionTag'); 
 		$cond = array();
        $passedArgs = array();
        $named = $this->request->params['named'];
        if ($named)
        {
        	foreach ($named as $key => $value)
        	{
        		$this->request->data[$key] = $value;
        	}
        }
        
    	if ( isset( $this->request->data['status'] ) && $this->request->data['status']!='' )
        {
        	$cond['QuestionTag.status'] = $this->request->data['status'];
        	$this->set('status',$this->request->data['status']);
        	$passedArgs['status'] = $this->request->data['status'];
        }
        
        $tags = $this->Paginator->paginate('QuestionTag',$cond);
        $this->set('tags', $tags);
        $this->set('passedArgs',$passedArgs);
    }

    public function admin_create($id = null) {
    	$this->loadModel('Question.QuestionTag'); 
        $bIsEdit = false;
        if (!empty($id)) {
            $tag = $this->QuestionTag->findById($id);
            $bIsEdit = true;
        } else {
            $tag = $this->QuestionTag->initFields();
            $tag['QuestionTag']['status'] = 1;
        }
        
        $this->set('tag', $tag);
        $this->set('bIsEdit', $bIsEdit);
    }

    public function admin_save() {
    	$this->loadModel('Question.QuestionTag'); 
        $this->autoRender = false;
        $bIsEdit = false;
        if (!empty($this->data['id'])) {
            $bIsEdit = true;
            $tag_old = $this->QuestionTag->findById($this->data['id']);
            $this->QuestionTag->id = $this->request->data['id'];
        }      
        
        $this->QuestionTag->set($this->request->data);

        $this->_validateData($this->QuestionTag);

        $this->QuestionTag->save();       
        if ($bIsEdit)
        {
        	$tag_id = $this->data['id'];
        	$this->loadModel('Question.QuestionTagMap');
        	$questions = $this->QuestionTagMap->find('all',array('conditions'=>array('QuestionTagMap.tag_id'=>$tag_id)));
        	$ids = array();
        	foreach ($questions as $question)
        	{
        		$ids[] = $question['QuestionTagMap']['question_id'];
        
        	}
        	if (count($ids))
        	{
        		$this->loadModel('Tag');
        		$this->Tag->updateAll(array('tag'=>"'".$this->request->data['title']."'"),array('target_id'=>$ids,'type'=>'Question_Question','tag'=>$tag_old['QuestionTag']['title']));
        	}
        }
        
        $this->Session->setFlash(__d('question','Tag has been successfully saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));

        $response['result'] = 1;
        echo json_encode($response);
    }

    public function admin_delete($id) {
        $this->autoRender = false;
		$this->loadModel('Question.QuestionTag'); 
        $this->QuestionTag->delete($id);

        $this->Session->setFlash(__d('question','Tag has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $this->redirect($this->referer());
    }
    
    public function admin_change_status($id,$status)
    {
    	$this->loadModel('Question.QuestionTag');   	
    	
    	$this->QuestionTag->id = $id;
    	$this->QuestionTag->save(array(
    		'status' => $status
    	));
    	
    	$this->Session->setFlash(__d('question',"Tag's status has been changed"), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $this->redirect($this->referer());
    }
    
    public function index()
    {
    	$this->set('title_for_layout', '');
    	if ($this->isApp())
    	{
    		App::uses('browse_tagsQuestionWidget', 'Question.Controller'.DS.'Widgets'.DS.'question');
    		$widget = new browse_tagsQuestionWidget(new ComponentCollection(),null);
    		$widget->beforeRender($this);
    	}
    }
}