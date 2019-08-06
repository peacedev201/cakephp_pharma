<?php
App::uses('MooPlugin', 'Lib');

class ChatPlugin implements MooPlugin
{
    public function install()
    {
        $this->_initBootingSetting();
        $this->_addRole(array('chat_allow_chat', 'chat_allow_send_picture', 'chat_allow_send_files', 'chat_allow_user_emotion', 'chat_allow_chat_group', 'chat_allow_video_calling'));
        $this->_customizeDatabase();
    }

    public function uninstall()
    {
        $this->_removeRole();
    }

    public function callback_1_4()
    {
        $this->_initBootingSetting();
        $this->_customizeDatabase();
        
        //new role
        //$this->_addRole(array('chat_allow_video_calling'));
        
        //new setting
        $mSettingGroup = MooCore::getInstance()->getModel('SettingGroup');
        $mSetting = MooCore::getInstance()->getModel('Setting');
        $settingGroup = $mSettingGroup->findByModuleId('Chat');
        if ($settingGroup != null) {
            $mSetting->create();
            $mSetting->save(array(
                'group_id' => $settingGroup['SettingGroup']['id'],
                'label' => 'Waiting video call time out',
                'name' => 'chat_waiting_video_call_time_out',
                'type_id' => 'text',
                'value_actual' => 30,
                'value_default' => 30,
                'description' => 'seconds',
                'ordering' => 11
            ));
            
            $mSetting->create();
            $mSetting->save(array(
                'group_id' => $settingGroup['SettingGroup']['id'],
                'label' => 'STUN/TURN Server',
                'name' => 'chat_stun_server',
                'type_id' => 'textarea',
                'value_actual' => '',
                'value_default' => '',
                'description' => '',
                'ordering' => 12
            ));
            
            $mSetting->create();
            $mSetting->save(array(
                'group_id' => $settingGroup['SettingGroup']['id'],
                'label' => 'Google Cloud Message - Sever API Key',
                'name' => 'chat_fcm_server_api_key',
                'type_id' => 'text',
                'value_actual' => '',
                'value_default' => '',
                'description' => '',
                'ordering' => 13
            ));
            
            $mSetting->create();
            $mSetting->save(array(
                'group_id' => $settingGroup['SettingGroup']['id'],
                'label' => 'Google Cloud Message - Sender ID',
                'name' => 'chat_fcm_sender_id',
                'type_id' => 'text',
                'value_actual' => '',
                'value_default' => '',
                'description' => '',
                'ordering' => 14
            ));
        }
        
        $this->updateColumn();
    }

    public function menu()
    {
        return array(
            __d('chat', 'General') => array('plugin' => 'chat', 'controller' => 'chats', 'action' => 'admin_index'),
            __d('chat', 'Settings') => array('plugin' => 'chat', 'controller' => 'chat_settings', 'action' => 'admin_index'),
            //__d('chat', 'Video Conferencing') => array('plugin' => 'chat', 'controller' => 'chat_settings', 'action' => 'admin_stun'),
            __d('chat', 'Monitor') => array('plugin' => 'chat', 'controller' => 'chat_monitor', 'action' => 'admin_index'),
            __d('chat', 'Logs') => array('plugin' => 'chat', 'controller' => 'chat_logs', 'action' => 'admin_index'),
            __d('chat', 'Permission') => array('plugin' => 'chat', 'controller' => 'chat_permissions', 'action' => 'admin_index'),
            __d('chat', 'Report') => array('plugin' => 'chat', 'controller' => 'chat_reports', 'action' => 'admin_index'),
            __d('chat', 'Error') => array('plugin' => 'chat', 'controller' => 'chat_errors', 'action' => 'admin_index'),
            __d('chat', 'Help') => array('plugin' => 'chat', 'controller' => 'chat_help', 'action' => 'admin_index'),
        );
    }

    private function _addRole($role_params)
    {
        $roleModel = MooCore::getInstance()->getModel('Role');
        $roles = $roleModel->find('all');
        $role_ids = array();
        foreach ($roles as $role) {
            $role_ids[] = $role['Role']['id'];
            $params = explode(',', $role['Role']['params']);
            $params = array_unique(array_merge($params, $role_params));
            $roleModel->id = $role['Role']['id'];
            $roleModel->save(array('params' => implode(',', $params)));
        }
    }

    private function _removeRole()
    {
        $roleModel = MooCore::getInstance()->getModel('Role');
        $roles = $roleModel->find('all');
        foreach ($roles as $role) {
            $params = explode(',', $role['Role']['params']);
            $params = array_diff($params, array('chat_allow_chat', 'chat_allow_send_picture', 'chat_allow_send_files', 'chat_allow_user_emotion', 'chat_allow_chat_group', 'chat_allow_video_calling'));
            $roleModel->id = $role['Role']['id'];
            $roleModel->save(array('params' => implode(',', $params)));
        }
    }

    public function settingGuide()
    {
    }

    private function _customizeDatabase()
    {
        // Added since 1.2 version

        // Core conversations integration
        $db = ConnectionManager::getDataSource('default');
        if (isset($db->config['prefix'])) {
            $userTable = $db->config['prefix'] . "users";
            $queryGetFirstUser = "SELECT * FROM $userTable  LIMIT 1";
            $queryAddChatCountField = "ALTER TABLE $userTable ADD `chat_count` INT NOT NULL DEFAULT '0' ";

            try {

                $result = $db->fetchAll($queryGetFirstUser);
                if ($result) {
                    $user = $result[0][$userTable];
                    if (!isset($user["chat_count"])) {
                        try {
                            $db->rawQuery($queryAddChatCountField);
                        } catch (Exception $ex) {
                            echo $ex->getMessage();
                            die();
                        }

                    }

                }
            } catch (Exception $ex) {
                echo $ex->getMessage();
                die();
            }
            //$db = ConnectionManager::getDataSource('default');
            $roomTable = $db->config['prefix'] . "chat_rooms";
            $messagesTable = $db->config['prefix'] . "chat_messages";
            if(!$this->isColumnExist("latest_mesasge_id",$roomTable)){
                $queryAddLatestMessageField = "ALTER TABLE $roomTable ADD `latest_mesasge_id` INT NOT NULL DEFAULT '0' ";
                try{
                    $db->fetchAll($queryAddLatestMessageField);
                } catch(Exception $ex) {
                    echo $ex->getMessage();
                    die();
                }
            }            
            $queryRoomIds = "SELECT id FROM $roomTable WHERE latest_mesasge_id=0";
            $queryLastestMessage = "SELECT MAX(id) AS id FROM $messagesTable WHERE room_id=%s";
            $queryUpdateRoom = "UPDATE $roomTable SET latest_mesasge_id=%s WHERE id=%s";
            try {

                $result = $db->fetchAll($queryRoomIds);
                if ($result) {

                    foreach ($result as $i) {

                        try {
                            $messageId = $db->fetchAll(sprintf($queryLastestMessage, $i[$roomTable]["id"]));
                            if ($messageId) {
                                try {
                                    if (!empty($messageId[0][0]["id"])) {
                                        $db->rawQuery(sprintf($queryUpdateRoom, $messageId[0][0]["id"], $i[$roomTable]["id"]));
                                    }

                                } catch (Exception $ex) {
                                    echo $ex->getMessage();
                                    die();
                                }
                            }
                        } catch (Exception $ex) {
                            echo $ex->getMessage();
                            die();
                        }
                    }
                }
            } catch (Exception $ex) {
                echo $ex->getMessage();
                die();
            }
        }
        
        $this->updateColumn();
    }

    private function _initBootingSetting()
    {
        // Added since 1.2 version
        $settingModel = MooCore::getInstance()->getModel('Setting');

        $setting = $settingModel->findByName('chat_disable');
        if ($setting) {
            $settingModel->id = $setting['Setting']['id'];
            $settingModel->save(array('is_boot' => 1));
        }
        $settingModel->clear();
        $setting = $settingModel->findByName('chat_turn_on_notification');
        if ($setting) {
            $settingModel->id = $setting['Setting']['id'];
            $settingModel->save(array('is_boot' => 1));
        } else {
            $settingGroupModel = MooCore::getInstance()->getModel('SettingGroup');
            $settingGroup = $settingGroupModel->findByModuleId("Chat");
            if ($settingGroup) {
                $settingModel->save(
                    array(
                        "group_id" => $settingGroup['SettingGroup']['id'],
                        "label" => "Turn on messages notifications",
                        "name" => "chat_turn_on_notification",
                        "field" => "",
                        "value" => "",
                        "is_hidden" => 0,
                        "version_id" => "",
                        "type_id" => "radio",
                        "value_actual" => "[{\"name\":\"Yes\",\"value\":\"1\",\"select\":1},{\"name\":\"No\",\"value\":\"0\",\"select\":0}]",
                        "value_default" => "[{\"name\":\"Yes\",\"value\":\"1\",\"select\":\"0\"},{\"name\":\"No\",\"value\":\"0\",\"select\":\"1\"}]",
                        "description" => "Conversation notifications is replaced by chat notifications",
                        "ordering" => 10,
                        "is_boot" => 1
                    )
                );

            }

        }
    }

    private function isColumnExist($name, $table)
    {
        $db = ConnectionManager::getDataSource('default');
        $result = $db->fetchAll("SHOW COLUMNS FROM $table LIKE '$name'");
        if ($result) {
            return true;
        }

        return false;
    }
    
    private function updateColumn()
    {
    	$db = ConnectionManager::getDataSource('default');
    	if (isset($db->config['prefix'])) {
    		$userTable = $db->config['prefix'] . "users";
    		if(!$this->isColumnExist("who_can_see_gender",$userTable)){
    			$query= "ALTER TABLE `$userTable` ADD `who_can_see_gender` INT NOT NULL DEFAULT '0' ";
    			try{
    				$db->fetchAll($query);
    			} catch(Exception $ex) {
    				echo $ex->getMessage();
    				die();
    			}
    		}
    		if(!$this->isColumnExist("chat_online_status",$userTable)){
    			$query= "ALTER TABLE `$userTable` ADD `chat_online_status` enum('active','busy','invisible','') NOT NULL DEFAULT 'active';";
    			try{
    				$db->fetchAll($query);
    			} catch(Exception $ex) {
    				echo $ex->getMessage();
    				die();
    			}
    		}
    	}
    }
    /*
    Example for version 1.0: This function will be executed when plugin is upgraded (Optional)
    public function callback_1_0(){}
    */
}