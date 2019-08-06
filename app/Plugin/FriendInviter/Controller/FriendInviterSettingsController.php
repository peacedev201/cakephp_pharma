<?php 
class FriendInviterSettingsController extends FriendInviterAppController{
    public $components = array('QuickSettings');    
    public function __construct($request = null, $response = null) 
    {
        parent::__construct($request, $response);
        $this->url = '/admin/friend_inviter/friend_inviter_settings/';
        $this->url_create = $this->url;
    }
    public function admin_index()
    {
        $this->set('title_for_layout', __d('friend_inviter','Friend Inviter Settings'));
        $this->loadModel('Setting');
        $this->loadModel('SettingGroup');
        
        //group setting
        $setting_groups = $this->SettingGroup->find('all', array(
                'conditions' => array(
                    'OR' => array(array('module_id' => 'FriendInviter'), array('module_id' => 'GoogleIntegration'))
                )
        ));
            
            
        $groupId = array();

        foreach($setting_groups as $setting_group){
            $setting_group = $setting_group['SettingGroup'];
            $groupId[] = $setting_group['id'];
        }
                
        $settings = $this->Setting->find('all', array(
            'conditions' => array('group_id' =>$groupId ),
        ));
        // var_dump($settings);die();
        
        $google_client_id = '';
        $google_client_secret = '';
      //  var_dump($settings);die();
        foreach ($settings as $setting) {
            if(isset($setting['Setting']['name']) && $setting['Setting']['name'] == 'friendinviter_enabled'){
                $friendinviter_enabled = $setting['Setting'];
            }
                                   
            if(isset($setting['Setting']['name']) && $setting['Setting']['name'] == 'maximum_emails'){
                $maximum_emails = $setting['Setting'];
            }
            
            if(isset($setting['Setting']['name']) && $setting['Setting']['name'] == 'friendinviter_automatic_addfriend'){
                $auto_friend = $setting['Setting'];
            }
            
            if(isset($setting['Setting']['name']) && $setting['Setting']['name'] == 'friendinviter_enable_referral_code_field'){
                $this->set('enable_referral_code_field', $setting['Setting']);
            }
           
           /* if(isset($setting['Setting']['name']) && $setting['Setting']['name'] == 'invites_greate_angel'){
                $invites_greate_angel = $setting['Setting'];
            }*/
            
            if(isset($setting['Setting']['name']) && $setting['Setting']['name'] == 'yahoo_app_key'){
                $yahoo_app_key = $setting['Setting'];
            }
            
            if(isset($setting['Setting']['name']) && $setting['Setting']['name'] == 'yahoo_shared_secret'){
                $yahoo_shared_secret = $setting['Setting'];
            }
            
            if(isset($setting['Setting']['name']) && $setting['Setting']['name'] == 'windows_live_appid'){
                $windows_live_appid = $setting['Setting'];
            }
            
            if(isset($setting['Setting']['name']) && $setting['Setting']['name'] == 'windows_live_secret'){
                $windows_live_secret = $setting['Setting'];
            }
            
            if(isset($setting['Setting']['name']) && $setting['Setting']['name'] == 'google_app_id'){
                $google_client_id = $setting['Setting'];
            }
            
            if(isset($setting['Setting']['name']) && $setting['Setting']['name'] == 'google_app_secret'){
                $google_client_secret = $setting['Setting'];
            }
            
            if(isset($setting['Setting']['name']) && $setting['Setting']['name'] == 'web_account_services'){
                $web_account_services = $setting['Setting'];
            }                      
        }
        $this->set('friendinviter_enabled', $friendinviter_enabled);
        $this->set('maximum_emails', $maximum_emails);
        $this->set('auto_friend', $auto_friend);
        $this->set('yahoo_app_key', $yahoo_app_key);
        $this->set('yahoo_shared_secret', $yahoo_shared_secret);
        $this->set('windows_live_appid', $windows_live_appid);
        $this->set('windows_live_secret', $windows_live_secret);
        $this->set('google_client_id', $google_client_id);
        $this->set('google_client_secret', $google_client_secret);
        $this->set('web_account_services', $web_account_services);
     //   $this->set('invites_greate_angel', $invites_greate_angel);
        
        $fiEnable = Configure::read('FriendInviter.friendinviter_enabled');
        $this->loadModel('CoreMenuItem');
        $item = $this->CoreMenuItem->find('first',array('conditions'=>array('original_name'=>'Friend Inviter')));
    
        if($item){

            $this->CoreMenuItem->id = $item['CoreMenuItem']['id'];
            $this->CoreMenuItem->saveField('is_active',$fiEnable);

        }
    }
    
   public function admin_quick_save()
    {
        if ($this->request->is('post')) 
        {
            if (!empty($_FILES)){
                $this->saveLogo();
            }
            if(!empty( $this->request->data['setting_id']))
            {
                foreach($this->request->data['setting_id'] as $item)
                {
                    //$values['ordering'] = $this->request->data['ordering'][$item];
                    switch($this->request->data['type_id'][$item])
                    {
                        case 'text':
                            $values['value_actual'] = $this->request->data['text'][$item];
                            break;
                        case 'textarea':
                            $values['value_actual'] = $this->request->data['textarea'][$item];
                            break;
                        case 'radio':
                        case 'select':
                            $setting = $this->Setting->findById($item);
                            $multiValue = json_decode($setting['Setting']['value_actual'], true);
                            foreach($multiValue as $k => $multi)
                            {
                                if($multi['value'] == $this->request->data['multi'][$item])
                                {
                                    $multiValue[$k]['select'] = 1;
                                }
                                else
                                {
                                    $multiValue[$k]['select'] = 0;
                                }
                            }
                            $values['value_actual'] = json_encode($multiValue);
                            if($setting['Setting']['name'] == 'production_mode') {
                                $this->_saveGeneralSettings(array('production_mode' => $this->request->data['multi'][$setting['Setting']['id']]));
                            }
                            break;
                        case 'checkbox':
                            $setting = $this->Setting->findById($item);
                            $multiValue = json_decode($setting['Setting']['value_actual'], true);
                            foreach($multiValue as $k => $multi)
                            {
                                $multiValue[$k]['select'] = $this->request->data['multi'][$item][$multi['value']];
                            }
                            $values['value_actual'] = json_encode($multiValue);
                            break;
                        case 'timezone':
                            $values['value_actual'] = $this->request->data['timezone'][$item];
                            break;
                        case 'language':
                            $values['value_actual'] = $this->request->data['language'][$item];
                            break;
                    }

                    if(!is_writeable(APP.'Config'.DS.'settings.php') || !is_writeable(APP.'Config'.DS.'general.php'))
                    {
                        $this->Session->setFlash(__('Updates Failed. Unable to save due to file permissions, please check your file permissions for').'<br />"'.APP.'Config'.DS.'settings.php'.'"<br />"'.APP.'Config'.DS.'general.php'.'"', 'default', array('class' => 'Metronic-alerts alert alert-danger fade in' ));
                        $this->redirect( $this->referer() );
                        //break;
                    }

                    $this->Setting->id = $item;
                    $this->Setting->save($values);
                    
                }
                $setting = $this->Setting->findById($this->request->data['setting_id'][0]);
                if($setting){
                    $this->update_plugin_info_xml($setting['Setting']['group_id']);
                }
            }
             
            if(!Configure::read('FriendInviter.friendinviter_enabled') && Configure::read('Credit.credit_enabled')){
                $credit_action_type = MooCore::getInstance()->getModel('Credit.CreditActiontypes');
                $action_type = $credit_action_type->getActionTypeFormModule('friend_inviter');
                if ($action_type) {
                    $credit_action_type->delete($action_type['CreditActiontypes']['id']);                     
                }        
                Cache::delete('friendinviter.checkcredit');
            }
            
            if( isset($this->request->data["url_redirect"])){
                $this->redirect($this->request->data["url_redirect"]);
            }else{
                $this->Session->setFlash(__('Successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));
                $this->redirect( $this->referer() );
            }

        }
        else 
        {
            $this->redirect($this->url_create);
        }
    }
    
    
     public function update_plugin_info_xml($group_id)
    {
        $this->loadModel('SettingGroup');
        $setting_group = $this->SettingGroup->findById($group_id);
        $settings = $this->Setting->find('all', array('conditions' => array('group_id' => $group_id)));
        $xmlPath = APP . 'Plugin' . DS . $setting_group['SettingGroup']['module_id'] . DS . 'info.xml';
        if(file_exists($xmlPath))
        {
            $content = file_get_contents($xmlPath);
            $xml = new SimpleXMLElement($content);
            $xml->settings = '';
            $xmlSettings = $xml->settings;
            foreach($settings as $setting)
            {
                $setting = $setting['Setting'];
                $values = json_decode($setting['value_actual'], true);
                $xmlSetting = $xmlSettings->addChild('setting');
                $xmlSetting->label = $setting['label'];
                $xmlSetting->name = $setting['name'];
                $xmlSetting->description = $setting['description'];
                $xmlSetting->type = $setting['type_id'];
                if(!is_array($values))
                {
                    $xmlSetting->values = $setting['value_actual'];
                }
                else 
                {
                    $xmlValues = $xmlSetting->addChild('values');
                    foreach($values as $value)
                    {
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