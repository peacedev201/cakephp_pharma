<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
class QuizCategoriesController extends QuizAppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('Category');
    }

    public function admin_index() {
        $aCategories = $this->Category->getCats(array('conditions' => array('Category.type' => 'Quiz_Quiz'), 'order' => 'Category.weight, Category.id'));
        $this->set('type', 'Quiz_Quiz');
        $this->set('title_for_layout', __d('quiz', 'Categories Manager'));
        $this->loadModel('Quiz.Quiz');

        foreach ($aCategories as &$aCategory) {
            $iNumCategory = $this->Quiz->countQuizByCategory($aCategory['Category']['id']);
            $aCategory['Category']['item_count'] = $iNumCategory;
        }
        $this->set('aCategories', $aCategories);
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

        $headers = $this->Category->find('list', array('conditions' => array('Category.type' => 'Quiz_Quiz', 'Category.header' => 1), 'fields' => 'Category.name'));

        // Get all roles
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
        if ($this->request->data['header']) {
            $this->request->data['parent_id'] = 0;
        }

        $this->request->data['create_permission'] = (empty($this->request->data['everyone'])) ? implode(',', $this->request->data['permissions']) : '';

        $this->Category->set($this->request->data);
        $this->_validateData($this->Category);
        $this->Category->save();

        if (!$bIsEdit) {
            foreach (array_keys($this->Language->getLanguages()) as $lKey) {
                $this->Category->locale = $lKey;
                $this->Category->saveField('name', $this->request->data['name']);
            }
        }
        $this->Session->setFlash(__d('quiz', 'Category has been successfully saved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));

        $response['result'] = 1;
        echo json_encode($response);
    }

}
