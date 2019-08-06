<?php
class BusinessType extends BusinessAppModel 
{
    public $validationDomain = 'business';
    public $order = 'BusinessType.ordering ASC';
    
    public $validate = array(
        'name' => array(
            'rule' => 'notBlank',
            'message' => 'Name is required'
        )
    );
    
    public $actsAs = array(
        'Translate' => array('name' => 'nameTranslation')
    );
    public $recursive = 1;
    private $_default_locale = 'eng' ;
    
    public function setLanguage($locale) {
        $this->locale = $locale;
    }

    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->locale = Configure::read('Config.language');
    }
    public $belongsTo = array(
            );
    /*public $hasMany = array(
        'Business' => array(
            'className'=> 'Business.Business',
            'dependent' => true,
            'cascadeCallbacks' => true,
        )
    );*/
     public function deleteBusinessType($id){
        $canDelete = true;
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        $business = $mBusiness->findByBusinessTypeId($id);
        if(!empty($business)) {
            $canDelete = false;
        }
        if(!$canDelete) {
             return false;
        }
        if($this->delete($id)) {
            return true;
        }
    }
    public function getBusinessTypes() {
        //$this->locale = $this->_default_locale;
        $jobtype = $this->find('all', array('conditions' => array()));
        return $jobtype ;
    }
    public function getItemById($id) {
        $jobtype = $this->findById($id);
        if (empty($jobtype)) {
            $this->locale = $this->_default_locale;
            $jobtype = $this->findById($id);
        }
        return $jobtype ;
    }
    public function getBusinessTypeSelect() {
        //$this->locale = $this->_default_locale;
        $data = array();
        $jobtypes = $this->find('all', array('conditions' => array()));
        
        if(!empty($jobtypes)) {
            foreach($jobtypes as  $jobtype) {
                foreach($jobtype['nameTranslation'] as $jobtype_transalte) {
                    if($this->locale == $jobtype_transalte['locale']) {
                        $data[$jobtype_transalte['foreign_key']] = $jobtype_transalte['content'];
                    }
                }
            }
        }
        return $data ;
    }
    public function getBusinessTypeList($enable = null)
    {
        //$this->locale = $this->_default_locale;
        $data = array();
        $cond = array();
        if(is_bool($enable))
        {
            $cond['enable'] = $enable;
        }
        $jobtypes = $this->find('all', array('conditions' => $cond));
        
        if(!empty($jobtypes)) {
            foreach($jobtypes as  $jobtype) {
                foreach($jobtype['nameTranslation'] as $jobtype_transalte) {
                    if($this->locale == $jobtype_transalte['locale']) {
                        $data[$jobtype_transalte['foreign_key']] = $jobtype_transalte['content'];
                    }
                }
            }
        }
        return $data ;
    }
    
    public function isBusinessTypeExist($id)
    {
        return $this->hasAny(array(
            'BusinessType.id' => $id,
            'BusinessType.enable' => 1
        ));
    }
}