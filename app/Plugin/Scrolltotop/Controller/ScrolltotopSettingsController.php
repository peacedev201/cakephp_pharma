<?php 
class ScrolltotopSettingsController extends ScrolltotopAppController{
    public function admin_index()
    {
    	$this->set('title_for_layout',__d('scrolltotop','Scroll To Top Setting'));
    	if ($this->request->is('post')) 
        {
        	$this->loadModel('Setting');
        	$data = array();
        	foreach ($this->request->data as $key=>$value)
        	{
        		$this->Setting->updateAll(
        			array('Setting.value_actual'=>'"'.$value.'"'),
        			array('Setting.name' => 'scrolltotop_'.$key)
        		);
        	}
        	$this->Session->setFlash(__d('scrolltotop','Successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));
            $this->redirect('/admin/scrolltotop/scrolltotop_settings');
        }
    }
}