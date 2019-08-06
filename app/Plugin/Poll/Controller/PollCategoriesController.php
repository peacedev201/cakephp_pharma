<?php

class PollCategoriesController extends PollAppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('Category');
    }

    public function admin_index() {
    	
        $cond = array('Category.type' => 'Poll');
        $categories = $this->Category->getCats(array('conditions' => $cond, 'order' => 'Category.type asc, Category.weight asc')) ;
        $this->set('type', 'Poll');
        $this->set('title_for_layout', __d('poll','Category Manager'));
        $this->loadModel('Poll.Poll');
        
        foreach($categories as &$category){
            $num_category = $this->Poll->countPollByCategory($category['Category']['id']);
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

        $headers = $this->Category->find('list', array('conditions' => array('Category.type' => 'Poll', 'Category.header' => 1), 'fields' => 'Category.name'));
        $headers[0] = '';
        
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
        $this->Session->setFlash(__d('poll','Category has been successfully saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));

        $response['result'] = 1;
        echo json_encode($response);
    }

    public function admin_delete($id) {
        $this->autoRender = false;

        $category = $this->Category->findById($id);
        $this->loadModel('Poll.Poll');
        $polls = $this->Poll->findAllByCategoryId($id);
        foreach ($polls as $poll)
            $this->Poll->delete($poll['Poll']['id']);

        $this->Category->delete($id);

        $this->Session->setFlash(__d('poll','Category has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $this->redirect($this->referer());
    }

}
