<?php 
class GifCommentSettingsController extends GifCommentAppController{
    public $components = array('QuickSettings');
    public function beforeFilter() {
        parent::beforeFilter();
        $this->loadModel('Setting');
        $this->loadModel('SettingGroup'); 
    }
    public function admin_index($id = null) {
        $this->QuickSettings->run($this, array("GifComment"), $id);

        $this->set('title_for_layout', __d('gif_comment', 'Gif Image Comment Setting'));
        if ($this->request->is('post')) {
            $db = $this->Setting->getDataSource();
            $data = array();

            foreach ($this->request->data as $key => $value) {
                $this->Setting->updateAll(
                        array('Setting.value_actual' => $db->value($value)), array('Setting.name' => 'gif_comment_' . $key)
                );
            }
            
            $this->update_plugin_info_xml('GifComment');
            $this->Session->setFlash(__d('gif_comment', 'Successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
            $this->redirect($this->referer());
        }
    }
    public function update_plugin_info_xml($name) {
        $this->loadModel('SettingGroup');
        $setting_group = $this->SettingGroup->findByGroupType($name);
        $group_id = $setting_group['SettingGroup']['id'];
        $settings = $this->Setting->find('all', array('conditions' => array('group_id' => $group_id)));
        $xmlPath = APP . 'Plugin' . DS . $setting_group['SettingGroup']['module_id'] . DS . 'info.xml';
        if (file_exists($xmlPath)) {
            $content = file_get_contents($xmlPath);
            $xml = new SimpleXMLElement($content);
            $xml->settings = '';
            $xmlSettings = $xml->settings;
            foreach ($settings as $setting) {
                $setting = $setting['Setting'];
                $values = json_decode($setting['value_actual'], true);
                $xmlSetting = $xmlSettings->addChild('setting');
                $xmlSetting->label = $setting['label'];
                $xmlSetting->name = $setting['name'];
                $xmlSetting->description = $setting['description'];
                $xmlSetting->type = $setting['type_id'];
                if (!is_array($values)) {
                    $xmlSetting->values = $setting['value_actual'];
                } else {
                    $xmlValues = $xmlSetting->addChild('values');
                    foreach ($values as $value) {
                        $xmlValue = $xmlValues->addChild('value');
                        $xmlValue->name = $value['name'];
                        $xmlValue->value = $value['value'];
                        $xmlValue->select = $value['select'];
                    }
                }
            }
            $xml->saveXML($xmlPath);
        }
    }
}