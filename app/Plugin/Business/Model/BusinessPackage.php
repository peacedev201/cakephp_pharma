<?php
/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
class BusinessPackage extends BusinessAppModel
{
    public $validationDomain = 'business';
    public $order = 'BusinessPackage.ordering ASC';
    /*public $hasMany = array(
        'Business' => array(
            'className' => 'Business.Business',
            'dependent' => true,
            // 'cascadeCallbacks' => true,
        )
    );*/
    public $validate = array(
        'name' => array(
            'rule' => 'notBlank',
            'message' => 'Package Title is required.'
        ),
        'price' => array(
            'not_blank' => array(
                'rule' => 'notBlank',
                'message' => 'Price is required, Please enter number.'
            ),
            'num_check' => array(
                'rule' => array('decimal'),
                'message' => 'Price is invalid, Please enter number.'
            )
            
        ),
        'duration' => array(
            'not_blank' => array(
                'rule' => 'notBlank',
                'message' => 'Duration is required, Please enter number.'
            ),
            'num_check' => array(
                'rule' => array('comparison', '>', 0),
                'message'  => 'Duration must be a valid integer greater than 0'
            )
            
        ),
        'expiration_reminder' => array(
            'not_blank' => array(
                'rule' => 'notBlank',
                'message' => 'Expiration Reminder is required, Please enter number.'
            ),
            'num_check' => array(
                'rule' => array('comparison', '>', 0),
                'message'  => 'Expiration reminder must be a valid integer greater than 0'
            )
        ),
    );
    public function getDefaultPackage($only_id = true) {
        $package =  $this->find('all', array('conditions' => array('BusinessPackage.is_default' => 1), 'limit' => 1));
        if($only_id)
        {
            if($package[0]) {
                return $package[0]['BusinessPackage']['id'];
            }
            return false;
        }
        else
        {
            return !empty($package[0]['BusinessPackage']) ? $package[0]['BusinessPackage'] : null;
        }
    }
    public function getPackages(){
        return $this->find('all', array('conditions' => array('BusinessPackage.enable' => 1, 'BusinessPackage.is_default' => 0)));
    }
    public function getUpgradePackages(){
        return $this->find('all', array('conditions' => array('BusinessPackage.enable' => 1,'BusinessPackage.trial' => 0, 'BusinessPackage.is_default' => 0)));
    }
    public function deleteBusinessPackage($id){
        $canDelete = true;
        $package = $this->findById($id);
        if($package['BusinessPackage']['is_default'] ==  1) {
            $canDelete = false;
        }
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        $business = $mBusiness->findByBusinessPackageId($id);
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

    public function getPackageSelect(){
        $result = array();
        $t_package = $this->find('list', array('fields' =>  'BusinessPackage.trial',
                                                'conditions' => array('BusinessPackage.trial >' =>  0))); 
        $cond = array('BusinessPackage.is_default' =>  0, 'BusinessPackage.trial' =>  0);
        if(!empty($t_package)) {
            $cond = array('BusinessPackage.is_default' =>  0,
                            'BusinessPackage.trial' =>  0,
                            "NOT" => array( "BusinessPackage.id" => $t_package ));
        }
        $packages = $this->find('all', array('conditions' => $cond));
        foreach($packages as $package){
            $result[$package['BusinessPackage']['id']] = $package['BusinessPackage']['name'];
        }
        return $result;
    }
}
