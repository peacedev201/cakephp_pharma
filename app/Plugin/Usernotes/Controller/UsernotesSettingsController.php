<?php 
class UsernotesSettingsController extends UsernotesAppController{
    public $components = array('QuickSettings');
    public function admin_index($id = null)
    {
        $this->QuickSettings->run($this, array("Usernotes"), $id);
        
       $noteStatus = Configure::read('Usernotes.usernotes_enabled');
       
        $this->loadModel('CoreMenuItem');
        $item = $this->CoreMenuItem->find('first',array('conditions'=>array('plugin'=>'Usernotes')));
    
        if($item){

            $this->CoreMenuItem->id = $item['CoreMenuItem']['id'];
            $this->CoreMenuItem->saveField('is_active',$noteStatus);

        }
        $this->set(array(
            'title_for_layout' => __d('usernotes', 'User notes Settings')
        ));
    }
}