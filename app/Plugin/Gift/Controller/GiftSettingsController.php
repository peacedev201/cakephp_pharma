<?php 
class GiftSettingsController extends GiftAppController{
    public $components = array('QuickSettings');
    
    public function beforeFilter()
	{
		parent::beforeFilter();
        $this->_checkPermission(array('super_admin' => 1));
        $this->loadModel('Setting');
        $this->loadModel('Gift.Gift');
	}
    
    public function admin_index($id = null)
    {
        if(Configure::read('Gift.gift_enabled'))
        {
            $this->Gift->activeMenu(1);
        }
        else 
        {
            $this->Gift->activeMenu(0);
        }
        $this->QuickSettings->run($this, array("Gift"), $id);
        $this->set('title_for_layout', __d('gift', 'Settings'));
    }

    public function admin_credit_integration()
    {
        if ($this->request->is('post'))
        {
            $values = $this->request->data;
            $this->GiftSetting->updateSettings('gift_integrate_credit', $values['gift_integrate_credit']);
            // check plguin credit
            if($values['gift_integrate_credit'] == 1)
            {
                $this->GiftSetting->integrateCredit();
            }
            $this->Session->setFlash(__d('gift', 'Successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        }

        $value = 0;
        $this->loadModel('Gift.GiftSetting');
        $value = $this->GiftSetting->getValueSetting('gift_integrate_credit');
        $this->set('value', $value);

        $credit_enable = Configure::read('Credit.credit_enabled');
        $this->set('credit_enable', $credit_enable);

        Cache::clearGroup('gift', 'gift');
        $this->set('title_for_layout', __d('gift', 'Credits Integration'));
    }

}