<?php 
App::uses('StoreAppModel', 'Store.Model');
class StoreBusiness extends StoreAppModel
{
    public function isIntegrateToBusiness()
    {
        if(CakePlugin::loaded("Business") && Configure::read('Store.store_integrate_business') && Configure::read('Business.business_enabled'))
        {
            return true;
        }
        return false;
    }
    
    public function loadBusiness($id)
    {
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        $mBusiness->bindModel(array(
            'belongsTo' => array(
                'User' => array('counterCache' => true)
            )
        ));
        return $mBusiness->findById($id);
    }
}