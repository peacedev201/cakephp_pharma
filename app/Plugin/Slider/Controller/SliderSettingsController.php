<?php 
class SliderSettingsController extends SliderAppController{
    public $components = array('QuickSettings');
    public function admin_index($id = null)
    {
        $this->QuickSettings->run($this, array("Slider"), $id);
        if (CakeSession::check('Message.flash')) {
            $menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
            $menu = $menuModel->findByUrl('/slider');
            if ($menu)
            {
                $menuModel->id = $menu['CoreMenuItem']['id'];
                $menuModel->save(array('is_active'=>Configure::read('Slider.slider_enabled')));
            }
            Cache::clearGroup('menu', 'menu');
        }
        $this->set('title_for_layout', __d('slider','Slideshows Settings'));
    }
}