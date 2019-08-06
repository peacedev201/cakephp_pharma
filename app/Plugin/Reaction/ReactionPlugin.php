<?php 
App::uses('MooPlugin','Lib');
class ReactionPlugin implements MooPlugin{
    public function install(){

        $likeModel = MooCore::getInstance()->getModel("Like");
        $likeModel->getDataSource()->flushMethodCache();

        $this->_fixed_table();
    }
    public function uninstall(){
        $mNotification = MooCore::getInstance()->getModel('Notification');
        //delete notification
        //$mNotification->deleteAll(array("Notification.plugin" => "reaction"));
        $notifications = $mNotification->find('all', array('conditions' => array("Notification.plugin" => "reaction")));
        foreach ($notifications as $item){
            $mNotification->clear();
            $mNotification->delete($item['Notification']['id']);
        }

        $likeModel = MooCore::getInstance()->getModel("Like");
        $likeModel->getDataSource()->flushMethodCache();

        Cache::clearGroup('reaction');
    }
    public function settingGuide(){}
    public function menu()
    {
        return array(
            //'General' => array('plugin' => 'reaction', 'controller' => 'reactions', 'action' => 'admin_index'),
            __d('reaction', 'Reaction Settings') => array('plugin' => 'reaction', 'controller' => 'reaction_settings', 'action' => 'admin_index'),
        );
    }

    private function _fixed_table(){
        $db = ConnectionManager::getDataSource("default");
        $mSetting = MooCore::getInstance()->getModel('Setting');
        $table_prefix = $mSetting->tablePrefix;

        $db->query("UPDATE `".$table_prefix."likes` SET `reaction`= 0 WHERE `id` IN ( SELECT * FROM (SELECT `id` FROM `".$table_prefix."likes` WHERE `thumb_up` = 0) AS L)");
        $db->query("UPDATE `".$table_prefix."likes` SET `reaction`= 1 WHERE `id` IN ( SELECT * FROM (SELECT `id` FROM `".$table_prefix."likes` WHERE `thumb_up` = 1 AND `reaction` = 0) AS L);");

        Cache::clearGroup('reaction');
    }

    /*
    Example for version 1.0: This function will be executed when plugin is upgraded (Optional)
    public function callback_1_0(){}
    */
    public function callback_1_3(){
        $mSettingGroup = MooCore::getInstance()->getModel('SettingGroup');
        $mSettingGroup->clear();
        $plugin = $mSettingGroup->find('first', array(
            'conditions' => array(
                'SettingGroup.name' => 'Reaction'
            )
        ));

        if(!empty($plugin)){
            $mSetting = MooCore::getInstance()->getModel('Setting');
            $mSetting->clear();

            $settingCool = $mSetting->find('first', array(
                'conditions' => array(
                    'Setting.name' => 'reaction_cool_enabled'
                )
            ));

            if(empty($settingCool)){
                $mSetting->clear();
                $mSetting->save(array(
                    'group_id' => $plugin['SettingGroup']['id'],
                    'label' => 'Cool',
                    'name' => 'reaction_cool_enabled',
                    'field' => null,
                    'value' => '',
                    'is_hidden' => 0,
                    'version_id' => null,
                    'type_id' => 'radio',
                    'value_actual' => '[{"name":"Disable","value":"0","select":1},{"name":"Enable","value":"1","select":0}]',
                    'value_default' => '[{"name":"Disable","value":"0","select":"1"},{"name":"Enable","value":"1","select":"0"}]',
                    'description' => null,
                    'ordering' => 7,
                    'is_boot' => 0
                ));
            }

            $mSetting->clear();
            $settingConfused = $mSetting->find('first', array(
                'conditions' => array(
                    'Setting.name' => 'reaction_confused_enabled'
                )
            ));

            if(empty($settingConfused)){
                $mSetting->clear();
                $mSetting->save(array(
                    'group_id' => $plugin['SettingGroup']['id'],
                    'label' => 'Confused',
                    'name' => 'reaction_confused_enabled',
                    'field' => null,
                    'value' => '',
                    'is_hidden' => 0,
                    'version_id' => null,
                    'type_id' => 'radio',
                    'value_actual' => '[{"name":"Disable","value":"0","select":1},{"name":"Enable","value":"1","select":0}]',
                    'value_default' => '[{"name":"Disable","value":"0","select":"1"},{"name":"Enable","value":"1","select":"0"}]',
                    'description' => null,
                    'ordering' => 8,
                    'is_boot' => 0
                ));
            }
            $mSetting->clear();
        }

    }

}