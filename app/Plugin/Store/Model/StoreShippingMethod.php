<?php 
App::uses('StoreAppModel', 'Store.Model');
class StoreShippingMethod extends StoreAppModel
{
    public $validationDomain = 'store';
	public $actsAs = array(
        'Tree',
        'Translate' => array('name' => 'nameTranslation')
    );
    public $mooFields = array('href');
	public $validate = array(           
        'name' =>   array(   
            'rule' => 'notBlank',
            'message' => 'Please provide category name'
        ),		
    );
    
    private $_default_locale = 'eng' ;
    public function setLanguage($locale) {
        $this->locale = $locale;
    }

    public function __construct($id = false, $table = null, $ds = null) 
    {
        parent::__construct($id, $table, $ds);
        $this->locale = Configure::read('Config.language');
    }
    
    public function beforeSave($options = array()) {
        parent::beforeSave($options);
        foreach($this->actsAs['Translate'] as $field => $item)
        {
            $this->data['StoreShippingMethod']['trans_'.$field] = $this->data['StoreShippingMethod'][$field];
            $this->data['StoreShippingMethod'][$field] = reset($this->data['StoreShippingMethod'][$field]);
        }
    }

    public function afterSave($created, $options = array()) {
        parent::afterSave($created, $options);
        
        //save multi language
        foreach($this->actsAs['Translate'] as $field => $item)
        {
            $data = !empty($this->data['StoreShippingMethod']['trans_'.$field]) ? $this->data['StoreShippingMethod']['trans_'.$field] : null;
            $this->saveMultiLanguage($data, $field, $this->data['StoreShippingMethod']['id']);
        }
    }
    
    public function isShippingMethodExist($id)
    {
        return $this->hasAny(array(
            'StoreShippingMethod.id' => $id
        ));
    }
    
    public function getShippingMethodList()
    {
        return $this->find('list', array(
            'fields' => array('StoreShippingMethod.id', 'StoreShippingMethod.name')
        ));
    }
    
    public function loadManagerPaging($obj)
    {  
        //pagination
        $cond = array();
        $obj->Paginator->settings = array(
            'conditions' => $cond,
            'joins' => array(
                array(
                    'table' => 'store_shipping_details',
                    'alias' => 'StoreShippingDetail',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'StoreShippingDetail.store_id' => Configure::read('store.store_id'),
                        'StoreShippingDetail.store_shipping_method_id = StoreShippingMethod.id'
                    )
                )
            ),
            'fields' => array('StoreShippingMethod.*', 'StoreShippingDetail.enable as enable'),
            'limit' => 10,
            'order' => array('StoreShippingMethod.id' => 'ASC'),
        );
        return $obj->paginate('StoreShippingMethod'); 
    }
    
    public function loadShippingMethodDetail($id)
    {
        $cond = array(
            'StoreShippingMethod.id' => $id,
        );
        $joins = array(
            array(
                'table' => 'store_shipping_details',
                'alias' => 'StoreShippingDetail',
                'type' => 'LEFT',
                'conditions' => array(
                    'StoreShippingDetail.store_id' => Configure::read('store.store_id'),
                    'StoreShippingDetail.store_shipping_method_id = StoreShippingMethod.id'
                )
            )
        );
        
        return $this->find('first', array(
            'conditions' => $cond,
            'joins' => $joins,
            'fields' => array('StoreShippingMethod.*', 'StoreShippingDetail.enable as enable')
        ));
    }
    
    public function loadStoreShippingMethod($id = null)
    {
        if($id > 0)
        {
            return $this->findById($id);
        }
        return $this->find('all');
    }
}