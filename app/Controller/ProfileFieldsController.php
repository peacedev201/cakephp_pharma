<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */

class ProfileFieldsController extends AppController
{
	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->_checkPermission(array('super_admin' => 1));
		
		$this->loadModel("ProfileType");
	}
	
	/*
	 * Render listing fields
	 */
	public function admin_index()
	{
		$fields = $this->ProfileType->find( 'all' );
		$this->set('fields', $fields);
		$this->set('title_for_layout', __('Profile Types'));
	}
	
	public function admin_profile_fields($id = null)
	{
		$fields = $this->ProfileField->find( 'all' ,array('conditions'=>array(
			'ProfileField.profile_type_id' => $id
		)));
		$profile_type = $this->ProfileType->findById($id);
		$this->set('id',$id);
		$this->set('profile_type',$profile_type);
		$this->set('fields', $fields);
		$this->set('title_for_layout', __('Custom Profile Fields'));
	}
	
	public function admin_ajax_type_create($id = null)
	{
		$bIsEdit = false;
		if (!empty($id))
		{
			$field = $this->ProfileType->findById($id);
			$bIsEdit = true;
		}
		else
			$field = $this->ProfileType->initFields();
		
			$this->set('bIsEdit',$bIsEdit);
			$this->set('field', $field);
	}
	
	public function admin_ajax_type_save( )
	{
		$this->autoRender = false;
		$bIsEdit = false;
		if ( !empty( $this->data['id'] ) )
		{
			$bIsEdit = true;
			$this->ProfileType->id = $this->request->data['id'];
		}
	
		$this->ProfileType->set( $this->request->data );
		$this->_validateData( $this->ProfileType );
	
		$this->ProfileType->save( $this->request->data );
	
		$this->Session->setFlash(__('Profile type has been successfully saved'),'default',
				array('class' => 'Metronic-alerts alert alert-success fade in' ));
	
		$response['result'] = 1;
		echo json_encode($response);
	}
	
	public function admin_save_type_order()
	{
		$this->_checkPermission(array('super_admin' => 1));
		$this->autoRender = false;
		foreach ($this->request->data['order'] as $id => $order) {
			$this->ProfileType->id = $id;
			$this->ProfileType->save(array('order' => $order));
		}
		$this->Session->setFlash(__('Order saved'),'default',array('class' => 'Metronic-alerts alert alert-success fade in'));
		echo $this->referer();
	}
	
	/*
	 * Render add/edit field
	 * @param mixed $id Id of field to edit
	 */
	public function admin_ajax_create($profile_type_id, $id = null )
	{
		$bIsEdit = false;
		if (!empty($id))
		{
			$field = $this->ProfileField->findById($id);
			$bIsEdit = true;
		}
		else
			$field = $this->ProfileField->initFields();
		
		$this->set('profile_type_id',$profile_type_id);
		$this->set('bIsEdit',$bIsEdit);
		$this->set('field', $field);
	}
	
	/*
	 * Handle add/edit field submission
	 */
	public function admin_ajax_save( )
	{
		$this->autoRender = false;
		$bIsEdit = false;
		if ( !empty( $this->data['id'] ) )
		{
			$bIsEdit = true;
			$this->ProfileField->id = $this->request->data['id'];
		}

		$this->ProfileField->set( $this->request->data );
		$this->_validateData( $this->ProfileField );

		$type = $this->request->data['type'];
		$event = new CakeEvent('Profile.Field.getType',$this);
		$result = $this->getEventManager()->dispatch($event);
		$this->request->data['plugin'] = '';
		if ($result->result)
		{
			if (isset($result->result[$type]) && isset($result->result[$type]['plugin']))
			{
				$this->request->data['plugin'] = $result->result[$type]['plugin'];
			}
		}
		
		$this->ProfileField->save( $this->request->data );
        
        if ( $this->request->data['type'] == 'heading' && empty( $this->request->data['id'] ) ) // insert dummy value
        {
            $this->loadModel('ProfileFieldValue');
            $this->ProfileFieldValue->save( array( 'profile_field_id' => $this->ProfileField->id ) );
        }
        
        if (!$bIsEdit) {
        	foreach (array_keys($this->Language->getLanguages()) as $lKey) {
        		$this->ProfileField->locale = $lKey;
        		$this->ProfileField->saveField('name', $this->request->data['name']);
        	}
        }

        $this->Session->setFlash(__('Profile field has been successfully saved'),'default',
            array('class' => 'Metronic-alerts alert alert-success fade in' ));

        $response['result'] = 1;
        echo json_encode($response);
	}
	
	public function admin_ajax_reorder()
	{
		$this->_checkPermission(array('super_admin' => 1));
		$this->autoRender = false;
		foreach ($this->request->data['order'] as $id => $order) {
			$this->ProfileField->id = $id;
			$this->ProfileField->save(array('weight' => $order));
		}
		$this->Session->setFlash(__('Order saved'),'default',array('class' => 'Metronic-alerts alert alert-success fade in'));
		echo $this->referer();
	}
	
	public function admin_delete( $id )
	{
		$this->autoRender = false;
		$this->loadModel("ProfileFieldValue");
		$this->ProfileField->delete( $id );
		$this->ProfileFieldValue->deleteAll( array( 'ProfileFieldValue.profile_field_id' => $id ), false, false );
		
		$this->Session->setFlash(__('Field deleted'),'default',
            array('class' => 'Metronic-alerts alert alert-success fade in' ));
		$this->redirect( $this->referer() );
	}
	
	public function admin_ajax_translate($id) {
	
		if (!empty($id)) {
			$profile = $this->ProfileField->findById($id);
			$this->set('profile', $profile);
			$this->set('languages', $this->Language->getLanguages());
		} else {
			// error
		}
	}
	
	public function admin_ajax_translate_save() {
	
		$this->autoRender = false;
		if ($this->request->is('post') || $this->request->is('put')) {
			if (!empty($this->request->data)) {
				// we are going to save the german version
				$this->ProfileField->id = $this->request->data['id'];
				foreach ($this->request->data['name'] as $lKey => $sContent) {
					$this->ProfileField->locale = $lKey;
					if ($this->ProfileField->saveField('name', $sContent)) {
						$response['result'] = 1;
					} else {
						$response['result'] = 0;
					}
				}
			} else {
				$response['result'] = 0;
			}
		} else {
			$response['result'] = 0;
		}
		echo json_encode($response);
	}
	
	public function admin_type()
	{
		$this->loadModel('ProfileType');
		$fields_type = $this->ProfileType->find( 'all' );
		$this->set('fields', $fields_type);
		$this->set('title_for_layout', __('Profile Type'));
		$this->set('count_profilt_type', count($fields_type));
	}
	
	public function admin_ajax_create_type( $id = Null)
	{
		$this->loadModel('ProfileType');
		if (!empty($id))
			$field = $this->ProfileType->findById($id);
			else
				$field = $this->ProfileType->initFields();
	
				$this->set('field', $field);
	}
	
	public function admin_ajax_save_type()
	{
		$this->autoRender = false;
		$this->loadModel('ProfileType');
		if ( !empty( $this->data['id'] ) )
			$this->ProfileType->id = $this->request->data['id'];
	
			$this->ProfileType->set( $this->request->data );
			$this->_validateData( $this->ProfileType );
	
			$this->ProfileType->save( $this->request->data );
			$this->Session->setFlash(__('Profile type has been successfully saved'),'default',
					array('class' => 'Metronic-alerts alert alert-success fade in' ));
	
			$response['result'] = 1;
			echo json_encode($response);
	}
	
	public function admin_delete_type( $id )
	{
		$this->autoRender = false;
	
		$fields = $this->ProfileField->findAllByProfileTypeId($id);
		$ids = array();
		foreach( $fields as $field ){
			$ids[] = $field['ProfileField']['id'];
			$this->ProfileField->delete( $field['ProfileField']['id'] );			
		}
		$this->loadModel("ProfileFieldValue");
		if (count($ids))
		{
			$this->ProfileFieldValue->deleteAll( array( 'ProfileFieldValue.profile_field_id' => $ids ), false, false );
		}
		$this->loadModel('ProfileType');
		$this->ProfileType->delete( $id );
	
		$this->Session->setFlash(__('Profile type deleted'),'default',
				array('class' => 'Metronic-alerts alert alert-success fade in' ));
		$this->redirect( $this->referer() );
	}
	
}