<?php 
App::uses('StoreAppModel', 'Store.Model');
class StoreProducer extends StoreAppModel
{
    public $validationDomain = 'store';
	public $belongsTo = array(
        'User' => array( 
            'foreignKey' => 'store_id',
    ));	
	public $order = 'StoreProducer.ordering asc';
	public $validate = array(           
        'name' =>   array(   
            'rule' => 'notBlank',
            'message' => 'Please provide producer name'
        )		
    );
    
    public function beforeSave($options = array()) 
    {
        parent::beforeSave($options);
        $this->data['StoreProducer']['store_id'] = Configure::read('store.store_id');
    }
	
	
	public function getProducer($cond = array(), $findMethod = 'all', $limit = RESULTS_LIMIT, $page = 1){		
		$producers = $this->find($findMethod,array('conditions' => $cond, 'limit' => $limit, 'page' => $page));
		
		return $producers;
	}
	
    public function saveOrdering($data){
		if(!empty($data['cid']))
        {
            foreach($data['cid'] as $k => $category_id)
            {
				$this->updateAll( array( 'StoreProducer.ordering' => $data['ordering'][$k] ), array( 'StoreProducer.id' => $category_id ,'StoreProducer.store_id' => Configure::read('store.store_id')) );     
			}
        }
	}
	
	public function loadManagerPaging($obj, $search = array())
    {       
        
        //pagination
        $this->unbindModel(array('belongsTo' => array('User')));
        $cond = array(
            'StoreProducer.store_id' => Configure::read('store.store_id'),
        );
        
		if(!empty($search['keyword']))
        {
            $cond[] = "StoreProducer.name LIKE '%".$search['keyword']."%'";
        }
		
        $obj->Paginator->settings = array(
            'conditions' => $cond,
            'limit' => 10,
            'order' => array('StoreProducer.ordering' => 'DESC'),
        );
        $data = $obj->paginate('StoreProducer');      
        
        return $data;
    }
	
    public function loadListProducer()
    {
        return $this->find('list', array(
            'conditions' => array('StoreProducer.store_id' => Configure::read('store.store_id')),
            'order' => array('StoreProducer.ordering ASC')
        ));
    }
	public function isProducerExist($id)
    {
        return $this->hasAny(array(
            'store_id' => Configure::read('store.store_id'),
            'id' => $id
        ));
    }
	
	public function activeField($id, $task, $value)
    {
        $this->create();
        $this->updateAll(array(
            'StoreProducer.'.$task => $value
        ), array(
            'StoreProducer.store_id' => Configure::read('store.store_id'),
            'StoreProducer.id' => $id,
        ));
    }
}