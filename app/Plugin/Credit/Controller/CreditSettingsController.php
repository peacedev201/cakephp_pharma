<?php 
class CreditSettingsController extends CreditAppController{
    public $components = array('QuickSettings');
    public function admin_index($id = null)
    {
        $this->QuickSettings->run($this, array("Credit"), $id);

        if (CakeSession::check('Message.flash')) {
            $menuModel = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
            $menu = $menuModel->findByUrl('/credits');
            if ($menu)
            {
                $menuModel->id = $menu['CoreMenuItem']['id'];
                $menuModel->save(array('is_active'=>Configure::read('Credit.credit_enabled')));
            }
            Cache::clearGroup('menu', 'menu');

            // update enable gateway credit
            $this->loadModel('PaymentGateway.Gateway');
            $gateway = $this->Gateway->find('first', array('conditions' => array(
                array('Plugin' => 'Credit')
            )));
            if( !empty($gateway) ) {
                $data = array(
                    'enabled' => Configure::read('Credit.credit_enabled'),
                    'test_mode' => Configure::read('Credit.credit_test_mode')
                );
                $this->Gateway->id = $gateway['Gateway']['id'];
                $this->Gateway->save($data);
            }
        }
        $this->set('title_for_layout',__d('credit','Credit Setting'));
    }

    public function admin_quick_save()
    {
        $this->autoRender = false;
        if ($this->request->is('post')) {
            if(!empty( $this->request->data['setting_id']))
            {
                $this->loadModel('Setting');
                $this->loadModel('SettingGroup');

                $data = $this->request->data;
                foreach( $data['text'] as $key=>$value) {
                    $item = $this->Setting->findById($key);
                    if( isset($item['Setting']['name']) && $item['Setting']['name'] == 'credit_currency_exchange' ) {
                        if( !$value ) {
                            $this->Session->setFlash(__d('credit','Currency exchange is required.'), 'default', array('class' => 'Metronic-alerts alert error-message fade in' ));
                            $this->redirect( $this->referer() );
                        }
                        if( !is_numeric($value) ) {
                            $this->Session->setFlash(__d('credit','Currency exchange is numeric.'), 'default', array('class' => 'Metronic-alerts alert error-message fade in' ));
                            $this->redirect( $this->referer() );
                        }
                    }
                }

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
                $this->update_plugin_info_xml($setting['Setting']['group_id']);
            }

            $this->Session->setFlash(__('Successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));
            $this->redirect( $this->referer() );
        }
        else {
            $this->redirect($this->url_create);
        }
    }

    public function update_plugin_info_xml($group_id)
    {
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

    public function admin_settings()
    {
        $this->set('title_for_layout',__d('credit','Credit Setting'));
        $this->loadModel('Credit.CreditActiontypes');
        $actions = $this->CreditActiontypes->getActions();
        $group_actions = array();
        $header = array();
        if(count($actions)){
            foreach($actions as $key => $item){
                $group_actions[$item['CreditActiontypes']['action_module']][] = $item;
                if (!isset($header[$item['CreditActiontypes']['action_module']]))
                {
                    $header[$item['CreditActiontypes']['action_module']] = $item['CreditActiontypes']['plugin'];
                }
            }
        }
        $this->set('group_actions', $group_actions);
        $this->set('header',$header);
    }

    public function admin_save()
    {
        $this->loadModel('Credit.CreditActiontypes');
        $this->autoRender = false;
        $values = $this->request->data;

        foreach ($values as $key => $param) {
            $row_rollover_period = explode("-", $key);
            $row_max_credit = explode("_",$key);

            if($key == 'credit'){

                foreach($param as $key_2 => $item){
                    $this->CreditActiontypes->id = $key_2;
                    $this->CreditActiontypes->set(array('credit' => $item));
                    $this->CreditActiontypes->save();
                }
            }

            $param = (int)$param;
            if ($param < 0) {
                $param *= -1;
            }


            if (isset($row_rollover_period[1])) {
                $this->CreditActiontypes->id = $row_rollover_period[1];
                $this->CreditActiontypes->set(array($row_rollover_period[0] => $param));
                $this->CreditActiontypes->save();
            }
            if (isset($row_max_credit[2])) {
                $this->CreditActiontypes->id = $row_max_credit[2];
                $this->CreditActiontypes->set(array($row_max_credit[0] ."_" . $row_max_credit[1] => $param));
                $this->CreditActiontypes->save();
            }
        }
        $this->Session->setFlash(__d('credit','Saved successfully'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $this->redirect($this->referer());
    }
}
