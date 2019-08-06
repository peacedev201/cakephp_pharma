<?php
class QuestionBadgesController extends QuestionAppController {
	public function admin_index() {
        $this->set('title_for_layout', __d('question','Badge Manager'));
        $this->loadModel('Question.QuestionBadge'); 
        $badges = $this->QuestionBadge->find('all');
        $this->set('badges', $badges);

    }

    public function admin_create($id = null) {
    	$this->loadModel('Question.QuestionBadge'); 
        $bIsEdit = false;
        if (!empty($id)) {
            $badge = $this->QuestionBadge->findById($id);
            $bIsEdit = true;
        } else {
            $badge = $this->QuestionBadge->initFields();            
        }
        
        $this->set('badge', $badge);
        $this->set('bIsEdit', $bIsEdit);
    }

    public function admin_save() {
    	$this->loadModel('Question.QuestionBadge'); 
        $this->autoRender = false;
        $bIsEdit = false;
        if (!empty($this->data['id'])) {
            $bIsEdit = true;
            $this->QuestionBadge->id = $this->request->data['id'];
        }        
        $data = $this->request->data;
        if (isset($data['permission']) && is_array($data['permission']))
        {
        	$data['permission'] = implode(',', $data['permission']);
        }
        $this->QuestionBadge->set($data);

        $this->_validateData($this->QuestionBadge);

        $this->QuestionBadge->save();       
        $this->Session->setFlash(__d('question','Badge has been successfully saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));

        $response['result'] = 1;
        echo json_encode($response);
    }

    public function admin_delete($id) {
        $this->autoRender = false;
		$this->loadModel('Question.QuestionBadge'); 
        $this->QuestionBadge->delete($id);

        $this->Session->setFlash(__d('question','Badge has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $this->redirect($this->referer());
    }
}
