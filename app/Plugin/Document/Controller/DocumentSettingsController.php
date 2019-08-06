<?php 
class DocumentSettingsController extends DocumentAppController{
	public $components = array('QuickSettings');
    
    public function admin_index($id = null)
    {
    	$this->set('title_for_layout', __d('document','Settings'));
    	$this->QuickSettings->run($this, array("Document"), $id);
    	if (CakeSession::check('Message.flash')) {
	    	$menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
	    	$menu = $menuModel->findByUrl('/documents');
	    	if ($menu)
	    	{
	    		$menuModel->id = $menu['CoreMenuItem']['id'];
	    		$menuModel->save(array('is_active'=>Configure::read('Document.document_enabled')));
	    	}
	    	Cache::clearGroup('menu', 'menu');
    	}
    }
}