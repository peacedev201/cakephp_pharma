<?php 
class ActivitylogSettingsController extends ActivitylogAppController {
	public $components = array('QuickSettings');
    
    public function admin_index($id = null)
    {
    	$this->set('title_for_layout', __d('activitylog','Activity log'));
    	$this->QuickSettings->run($this, array("Activitylog"), $id);
        if (CakeSession::check('Message.flash')) {
            $menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
            $menu = $menuModel->findByUrl('/activity_log');
            if ($menu)
            {
                $menuModel->id = $menu['CoreMenuItem']['id'];
                $menuModel->save(array('is_active'=>Configure::read('Activitylog.activitylog_enabled')));
            }
            Cache::clearGroup('menu', 'menu');
        }
    }
}