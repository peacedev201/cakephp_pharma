<?php 
class FeelingSettingsController extends FeelingAppController{
    public $components = array('QuickSettings');

    /*public function beforeFilter() {
        parent::beforeFilter();
    }*/

    public function admin_index($id=null)
    {
        $this->QuickSettings->run($this, array("Feeling"), $id);
        if (CakeSession::check('Message.flash')) {
            $menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
            $menu = $menuModel->findByUrl('/feeling');
            if ($menu)
            {
                $menuModel->id = $menu['CoreMenuItem']['id'];
                $menuModel->save(array('is_active'=>Configure::read('Feeling.feeling_enabled')));
            }
            Cache::clearGroup('menu', 'menu');
        }
        $this->set('title_for_layout', __d('feeling', 'Settings'));
    }
}