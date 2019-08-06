<?php 
class QuestionSettingsController extends QuestionAppController{
	public $components = array('QuickSettings');
    
    public function admin_index($id = null)
    {
    	$this->set('title_for_layout', __d('question','Question Settings'));
    	$this->QuickSettings->run($this, array("Question"), $id);
    	if (CakeSession::check('Message.flash')) {
	    	$menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
	    	$menu = $menuModel->findByUrl('/questions');
	    	if ($menu)
	    	{
	    		$menuModel->id = $menu['CoreMenuItem']['id'];
	    		$menuModel->save(array('is_active'=>Configure::read('Question.question_enabled')));
	    	}
	    	Cache::clearGroup('menu', 'menu');
    	}
    }
}