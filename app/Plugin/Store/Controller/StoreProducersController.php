<?php
class StoreProducersController extends StoreAppController{
	public $components = array('Paginator');
	
	public function beforeFilter() {
        parent::beforeFilter();	
        $this->url = STORE_MANAGER_URL.'producers/';
        $this->set('url', $this->url);
        $this->loadModel('Store.StoreProducer');
    }
	
	public function manager_create($id = null){
		if($id != null){//editing			
			$producer = $this->_checkProducer($id);
			
			$this->set( 'title_for_layout', __d('store', 'Edit Producer') );
		}else{//create new
			$producer = $this->StoreProducer->initFields();
			$this->set( 'title_for_layout', __d('store', 'Create Producer') );
		}
		$this->set(array(
            'producer' => $producer,
            'active_menu' => 'create_producer',
            'title_for_layout' => !empty($id) ? __d('store', "Edit Producer") : __d('store', "Create Producer")
        ));
	}	
	
	public function manager_save(){
        if(!empty($this->request->data['id'])){
            $this->_checkProducer($this->request->data['id']);
        }

        $this->request->data['ordering'] = $this->generateOrdering('StoreProducer');
        $this->StoreProducer->set($this->request->data);
        $this->_validateData($this->StoreProducer);
        
        if($this->StoreProducer->save())
        {
            $redirect = $this->url.'';
            if($this->request->data['save_type'] == 1)
            {
                $redirect = $this->url.'create/'.$this->StoreProducer->id;
            }
            $this->_jsonSuccess(__d('store', 'Successfully saved'), true, array(
                'location' => $redirect
            ));
        }	
        $this->_jsonError(__d('store', 'Something went wrong, please try again'));						
	}	
	
	
	public function manager_index(){
        //search
        $search = !empty($this->request->query) ? $this->request->query : '';			

        $producers = $this->StoreProducer->loadManagerPaging($this, $search);			
        $this->set(array(
            'producers' => $producers,
            'search' => $search,
            'active_menu' => 'manage_producers',
            'title_for_layout' => __d('store', "Manage Producers")
        ));
	}	
		
	
	public function delete($id = null){
		
        $id = intval($id);
        $this->_checkProducer($id);        

        //$this->_checkPermission(array('admins' => array($group['Group']['user_id'])));

        $this->StoreProducer->delete($id);

        $this->Session->setFlash(__d('store',  'Producer has been deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));
		$this->redirect(STORE_MANAGER_URL . 'producers/index');			
	}	
	
	public function manager_ordering()
    {
		//debug($this->request->data);die;
        $data = $this->request->data;
		
        $this->StoreProducer->saveOrdering($data);
        $this->Session->setFlash(__d('store', 'Successfully updated'),'default',array('class' => 'Metronic-alerts alert alert-success fade in'));
        $this->redirect($this->referer());		
		
    }
    
    public function manager_delete()
    {
        $data = $this->request->data;
        if(!empty($data['cid']))
        {
            foreach($data['cid'] as $id)
            {
                if($this->StoreProducer->isProducerExist($id))
                {
                    $this->StoreProducer->delete($id);
                }
            }
        }
        $this->Session->setFlash(__d('store', 'Successfully deleted'),'default',array('class' => 'Metronic-alerts alert alert-success fade in'));
        $this->redirect($this->referer());
    }
	
	 public function manager_enable()
    {		
        $this->active($this->request->data, 1, 'enable');
    }
    
    public function manager_disable()
    {
        $this->active($this->request->data, 0, 'enable');
    }
	
	private function active($data, $value, $task)
    {
        if(!empty($data['cid']))
        {
            foreach($data['cid'] as $id)
            {
                if($this->StoreProducer->isProducerExist($id))
                {
                    $this->StoreProducer->create();
                    $this->StoreProducer->activeField($id, $task, $value);
                }
            }
        }
        $this->Session->setFlash(__d('store', 'Successfully updated'));
        $this->redirect($this->referer());
    }
	
	public function publish_producer($id = null,$isCurrentDisable = false){
        $this->_checkProducer($id);		

        $this->StoreProducer->id = $id;
		if(!$isCurrentDisable){
			$this->StoreProducer->save(array('publish' => 0));
        }else{
			$this->StoreProducer->save(array('publish' => 1));
		}
		
		$this->redirect($this->referer());			
	}	
	
	
	private function _checkProducer($id = null){		
		$producer = $this->StoreProducer->findByIdAndStoreId($id,Configure::read('store.store_id'));
		if($this->_checkExistence($producer));
			return $producer; 
	}
}