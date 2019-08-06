<?php 
class ForumSettingsController extends ForumAppController{	
	public function admin_index($id = null)
	{
		$this->set('title_for_layout', __d('forum','Settings'));
		if ($this->request->is('post'))
		{
            /**
             * Validate
             */
            $val = true;
            if(intval($this->request->data['price_pin_per_day']) < 0)
            {
                $val = false;
                $this->Session->setFlash(__d('forum', 'Please select a value that is no less than 0.'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in', 'service'));
            }

            if($val)
            {
                $this->loadModel('Setting');
                $data = array();
                foreach ($this->request->data as $key=>$value)
                {
                    $this->Setting->updateAll(
                        array('Setting.value_actual'=>"'{$value}'"),
                        array('Setting.name' => 'forum_'.$key)
                    );
                }
                $this->Setting->save_boot_settings();
                $this->Session->setFlash(__d('forum','Successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));

                $menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
                $menu = $menuModel->findByUrl('/forums');
                if ($menu)
                {
                    $menuModel->id = $menu['CoreMenuItem']['id'];
                    $menuModel->save(array('is_active'=>$this->request->data['enabled']));
                }

                $this->redirect('/admin/forum/forum_settings');
            }
            else
            {
                $this->redirect('/admin/forum/forum_settings');
            }

		}
	}
}