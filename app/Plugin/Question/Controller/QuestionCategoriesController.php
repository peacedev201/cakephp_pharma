<?php
class QuestionCategoriesController extends QuestionAppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('Category');
    }

    public function admin_index() {
    	$type = 'Question';
        $cond = array('Category.type' => 'Question');
        $categories = $this->Category->getCats(array('conditions' => $cond, 'order' => 'Category.type asc, Category.weight asc')) ;
        $this->set('type', $type);
        $this->set('title_for_layout', __d('question','Categories Manager'));
        $this->loadModel('Question.Question');
        
        foreach($categories as &$category){
            $num_category = $this->Question->countQuestionByCategory($category['Category']['id']);
            $category['Category']['item_count'] = $num_category;
        }
        $this->set('categories', $categories);

    }

    public function admin_create($id = null) {
        $bIsEdit = false;
        if (!empty($id)) {
            $category = $this->Category->getCatById($id);
            $bIsEdit = true;
        } else {
            $category = $this->Category->initFields();
            $category['Category']['active'] = 1;
        }

        $headers = $this->Category->find('list', array('conditions' => array('Category.type' => 'Question', 'Category.header' => 1), 'fields' => 'Category.name'));
        $tmp = array('0'=>'');
        foreach ($headers as $key=>$text)
        {
        	$tmp[$key] = $text;
        }
        $headers = $tmp;
        
        // get all roles
        $this->loadModel('Role');
        $roles = $this->Role->find('all');

        $this->set('roles', $roles);
        $this->set('category', $category);
        $this->set('headers', $headers);
        $this->set('bIsEdit', $bIsEdit);
    }

    public function admin_save() {
        $this->autoRender = false;
        $bIsEdit = false;
        if (!empty($this->data['id'])) {
            $bIsEdit = true;
            $this->Category->id = $this->request->data['id'];
        }
        if ($this->request->data['header'])
            $this->request->data['parent_id'] = 0;

        $this->request->data['create_permission'] = (empty($this->request->data['everyone'])) ? implode(',', $_POST['permissions']) : '';

        $this->Category->set($this->request->data);

        $this->_validateData($this->Category);

        $this->Category->save();

        if (!$bIsEdit) {
            foreach (array_keys($this->Language->getLanguages()) as $lKey) {
                $this->Category->locale = $lKey;
                $this->Category->saveField('name', $this->request->data['name']);
            }
        }
        $this->Session->setFlash(__d('question','Category has been successfully saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));

        $response['result'] = 1;
        echo json_encode($response);
    }

    public function admin_delete($id) {
        $this->autoRender = false;

        $category = $this->Category->findById($id);
        $this->loadModel('Question.Question');

        $this->Category->delete($id);
        
        $questions = $this->Question->find('all',array('conditions'=>array('Question.category_id'=>$id)));
        if (count($questions))
        {
        	foreach ($questions as $question)
        	{
        		$this->Question->delete($question['Question']['id']);
        	}
        }
        $categories = $this->Category->find('all',array('conditions'=>array('Category.parent_id' => $category['Category']['id'])));
        if (count($categories))
        {
        	foreach ($categories as $category)
        	{
                $this->Category->delete($category['Category']['id']);
        		$questions = $this->Question->find('all',array('conditions'=>array('Question.category_id'=>$category['Category']['id'])));
        		if (count($questions))
        		{
        			foreach ($questions as $question)
        			{
        				$this->Question->delete($question['Question']['id']);
        			}
        		}
        	}
        }

        $this->Session->setFlash(__d('question','Category has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $this->redirect($this->referer());
    }

}
