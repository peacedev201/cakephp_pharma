<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
class MooinsideSettingsController extends MooinsideAppController {

    public $components = array('QuickSettings');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('Setting');
        $this->loadModel('SettingGroup');
        $this->loadModel('Plugin');
        $this->loadModel('Menu.CoreMenuItem');
    }

    public function admin_index($id = null) {
        // clear cache menu
        Cache::clearGroup('menu', 'menu');

        $this->QuickSettings->run($this, array("Mooinside"), $id);

        $this->set('title_for_layout', __('Mooinside Setting'));
    }

}
