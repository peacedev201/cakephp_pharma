<?php 
App::uses('MooPlugin','Lib');
class PopupPlugin implements MooPlugin{
    public function install(){
        $settingModel = MooCore::getInstance()->getModel('Setting');
        $setting = $settingModel->findByName('popup_enabled');
        if ($setting)
        {
            $settingModel->id = $setting['Setting']['id'];
            $settingModel->save(array('is_boot'=>1));
        }
        $i18nModel = MooCore::getInstance()->getModel('I18nModel');
        $languageModel = MooCore::getInstance()->getModel('Language');
        $languages = $languageModel->find('all');

        foreach ($languages as $language)
        {
            $i18nModel->clear();
            $i18nModel->save(array(
                'locale' => $language['Language']['key'],
                'model' => 'Popup',
                'foreign_key' => '1',
                'field' => 'title',
                'content' => 'Welcome'
            ));
            $i18nModel->clear();
            $i18nModel->save(array(
                'locale' => $language['Language']['key'],
                'model' => 'Popup',
                'foreign_key' => '1',
                'field' => 'body',
                'content' => 'Welcome to website'
            ));
        }
    }
    public function uninstall(){
        $i18nModel = MooCore::getInstance()->getModel('I18nModel');
        $i18ns = $i18nModel->find('all', array(
            'conditions' => array('model' => 'Popup')
        ));
        foreach($i18ns as $i18n)
        {
            $i18nModel->delete($i18n['I18nModel']['id']);
        }
    }
    public function settingGuide(){}
    public function menu()
    {
        return array(
            __('General')  => array('plugin' => 'popups_for_page', 'controller' => 'popups', 'action' => 'admin_index'),
            __('Settings') => array('plugin' => 'popups_for_page', 'controller' => 'popup_settings', 'action' => 'admin_index'),
        );
    }
    /*
    Example for version 1.0: This function will be executed when plugin is upgraded (Optional)
    public function callback_1_0(){}
    */
}