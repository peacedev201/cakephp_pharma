<?php 
App::uses('StoreAppModel', 'Store.Model');
class StorePackage extends StoreAppModel
{
    public $validationDomain = 'store';
    public $actsAs = array(
        'Translate' => array('name' => 'nameTranslation')
    );
    public $validate = array( 
        'name' =>   array(   
            'rule' => 'notBlank',
            'message' => 'Please provide name'
        ),
        'price' =>   array(   
            'rule' => 'notBlank',
            'message' => 'Please provide price'
        ),
        'period' =>   array(   
            'rule' => 'notBlank',
            'message' => 'Please provide period'
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
    
    public function loadStorePackage($id = null)
    {
        if($id > 0)
        {
            return $this->findById($id);
        }
        return $this->find('all');
    }
    
    public function isStorePackageExist($id, $enable = null)
    {
        $cond = array(
            'StorePackage.id' => $id
        );
        if(is_bool($enable))
        {
            $cond['StorePackage.enable'] = $enable;
        }
        return $this->hasAny($cond);
    }
    
	function activeField($id, $task, $value)
    {
        $this->create();
        $this->updateAll(array(
            'StorePackage.'.$task => $value
        ), array(
            'StorePackage.id' => $id,
        ));
    }
    
    public function loadStorePackageList()
    {
        return $this->find('list', array(
            'conditions' => array(
                'StorePackage.enable' => 1
            ),
            'fields' => array('StorePackage.id', 'StorePackage.name')
        ));
    }
}