<?php
App::uses('Widget','Controller/Widgets');

class menuQuestionWidget extends Widget {
    public function beforeRender(Controller $controller) {    	
    	$uid = MooCore::getInstance()->getViewer(true);
    	$viewer = MooCore::getInstance()->getViewer();
    	$role_id = (empty($viewer)) ? ROLE_GUEST : $viewer['User']['role_id'];
    	
    	$type = (isset($controller->viewVars['type']) ? $controller->viewVars['type'] : '');
    	$key = (isset($controller->viewVars['key']) ? $controller->viewVars['key'] : '');
    	$value = (isset($controller->viewVars['value']) ? $controller->viewVars['value'] : '');
    	
    	$categoryModel =  MooCore::getInstance()->getModel('Category');
    	$categories = $categoryModel->getCategories('Question',$role_id);
    	$this->setData('categories', $categories);
    	$this->setData('key', $key);
    	$this->setData('value', $value);
    	$this->setData('type', $type);
    }
}