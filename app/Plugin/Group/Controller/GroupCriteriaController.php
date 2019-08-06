<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
class GroupCriteriaController extends GroupAppController {
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('Group.Group');
        $this->loadModel('Category');
        $this->loadModel('Group.GroupsDefinition');
    }

    public function admin_index() {
        
        $cond = array();
        
        if (!empty($this->request->data['keyword']))
            $cond['MATCH(Group.name) AGAINST(? IN BOOLEAN MODE)'] = $this->request->data['keyword'];
        $groups = $this->paginate('GroupsDefinition', $cond);
        // $groups = $this->Group;
        // dd($groups);

        $categories = $this->Category->getCategoriesList('Group');

        $this->set('categories', $categories);
        $this->set('groups', $groups);
        $this->set('title_for_layout', __('Groups Manager'));
    }

    public function admin_delete() {
        $this->_checkPermission(array('super_admin' => 1));
        
        if (!empty($_POST['groups'])) {
            
            foreach ($_POST['groups'] as $group_id){
                $group = $this->GroupsDefinition->findById($group_id);
                
                $cakeEvent = new CakeEvent('Plugin.Controller.Group.beforeDelete', $this, array('aGroup' => $group));
                $this->getEventManager()->dispatch($cakeEvent);

                $this->GroupsDefinition->delete($group_id);
                
                $cakeEvent = new CakeEvent('Plugin.Controller.Group.afterDeleteGroup', $this, array('item' => $group));
                $this->getEventManager()->dispatch($cakeEvent);
                
            }

            $this->Session->setFlash(__( 'Groups have been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        }

        $this->redirect( array(
            'plugin' => 'group',
            'controller' => 'group_criteria',
            'action' => 'admin_index'
        ) );
    }
    
    public function admin_move() {
        if (!empty($_POST['groups']) && !empty($this->request->data['category'])) {
            foreach ($_POST['groups'] as $group_id) {
                $this->Group->id = $group_id;
                $this->Group->save(array('category_id' => $this->request->data['category']));
            }
            $this->Session->setFlash(__( 'Groups moved'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        }

        $this->redirect($this->referer());
    }

    public function admin_create() {
        $bIsEdit = false;
        if (!empty($id)) {
            $category = $this->GroupsDefinition->getCatById($id);
            $bIsEdit = true;
        } else {
            $category = $this->GroupsDefinition->initFields();
            $category['GroupsDefinition']['active'] = 1;
        }

        // dd($category);
        // get all roles

        $this->set('category', $category);
        $this->set('bIsEdit', $bIsEdit);
    }

    public function admin_save() {
        $this->autoRender = false;
        $bIsEdit = false;
        if (!empty($this->data['id'])) {
            $bIsEdit = true;
            $this->GroupsDefinition->id = $this->request->data['id'];
        }


        $this->GroupsDefinition->set($this->request->data);

        $this->_validateData($this->GroupsDefinition);

        $this->GroupsDefinition->save();
        if (!$bIsEdit) {
            foreach (array_keys($this->Language->getLanguages()) as $lKey) {
                $this->GroupsDefinition->locale = $lKey;
                $this->GroupsDefinition->saveField('name', $this->request->data['name']);
            }
        }

        // clear cache
        Cache::delete('categories', 'group');
        $this->Session->setFlash(__( 'New group created'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));

        $response['result'] = 1;
        echo json_encode($response);
    }

}
