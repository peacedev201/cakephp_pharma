<?php 
class PollSettingsController extends PollAppController{
	public $components = array('QuickSettings');
    
    public function admin_index($id = null)
    {
    	$this->set('title_for_layout', __d('poll','Settings'));
    	$this->QuickSettings->run($this, array("Poll"), $id);
    	if (CakeSession::check('Message.flash')) {
	    	$menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
	    	$menu = $menuModel->findByUrl('/polls');
	    	if ($menu)
	    	{
	    		$menuModel->id = $menu['CoreMenuItem']['id'];
	    		$menuModel->save(array('is_active'=>Configure::read('Poll.poll_enabled')));
	    	}
	    	Cache::clearGroup('menu', 'menu');
    	}
    }
}