<?php 

class ProfileCompletionSettingsController extends ProfileCompletionAppController
{
    public $components = array('QuickSettings');

    public function beforeFilter(){
        parent::beforeFilter();
        $this->loadModel('Setting');
        $this->loadModel('SettingGroup');
    }

    public function admin_index(){
        $this->set('title_for_layout', __d('profile_completion','Settings'));
        if ($this->request->is('post')) 
        {
            $this->loadModel('Setting');
            $data = array();
            foreach ($this->request->data as $key => $value)
            {
                $this->Setting->updateAll(
                    array('Setting.value_default'=>'"'.$value.'"', 'Setting.value_actual'=>'"'.$value.'"'),
                    array('Setting.name' => $key)
                );             
            }
            if(!isset($this->request->data['not_show_widget_100'])){
                $this->Setting->updateAll(
                    array('Setting.value_default'=> '"0"', 'Setting.value_actual'=> '"0"'),
                    array('Setting.name' => 'not_show_widget_100')
                );
            }
            $this->Setting->save_boot_settings();
            $this->Session->setFlash(__d('profile_completion','Successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));
            $this->redirect('/admin/profile_completion/profile_completion_settings');
        }
    }

}

 ?>