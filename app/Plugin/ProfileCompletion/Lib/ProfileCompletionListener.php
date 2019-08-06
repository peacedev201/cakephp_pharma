<?php 

App::uses('CakeEventListener', 'Event');

class ProfileCompletionListener implements CakeEventListener
{
    public function implementedEvents(){
        return array(
            'Model.beforeDelete'        => 'doAfterDelete',
            'MooView.beforeRender'      => 'beforeRender',
            'Controller.beforeRender'   => array('callable'=>'beforeRenderController','priority' => 1000),
            'View.Mooapp.users.view.renderProfileCompletionForApp' => 'afterRenderSectionProfileCompletenessAppVer1_2',      
            'View.Mooapp.activities.ajax_browse.renderAppOptimizedContent' => 'renderAppOptimizedContent',
            'Plugin.Controller.UsersApi.me' => 'UsersApiMe'
        );
    }
    
    public function renderAppOptimizedContent($event)
    {
        $viewer = MooCore::getInstance()->getViewer();
        
        if (!$viewer)
            return;
            
        if (Configure::read('core.site_offline'))
            return;
                
        $result = false;
        if ($this->checkProfile($viewer))
        {
            $result = true;
        }
        if ($result)
        {
            echo 'window.check_reload = true;';
        }
        else
        {
            echo 'window.check_reload = false;';
        }
    }
    
    public function UsersApiMe($event)
    {
        $viewer = MooCore::getInstance()->getViewer();
        
        if (!$viewer)
            return;
            
        if (Configure::read('core.site_offline'))
            return;
                
        $result = false;
        if ($this->checkProfile($viewer))
        {
            $result = true;
        }       
        $event->result['check_reload'] = $result;
    }
    
    public function checkProfile($viewer)
    {
        if ($viewer)
        {
            $viewer = array_merge($viewer,$viewer['User']);
        }       
        $force_update_profile_info = Configure::read("ProfileCompletion.force_update_profile_info");
        
        if(Configure::read('ProfileCompletion.profile_completion_enabled') && !empty($viewer) && !$viewer['Role']['is_admin'] && !$viewer['Role']['is_super'] && $force_update_profile_info > 0){
            
            $profile_type_id = $viewer['profile_type_id'];
            
            if( $profile_type_id <= 0 ){
                return false;
            }
            
            $profileCompletionModel = MooCore::getInstance()->getModel('ProfileCompletion.ProfileCompletion');
            $profileFieldModel      = MooCore::getInstance()->getModel('ProfileField');
            $profileFieldValueModel = MooCore::getInstance()->getModel('ProfileFieldValue');
            $userCountryModel       = MooCore::getInstance()->getModel('UserCountry');
            
            $profile_completion = $profileCompletionModel->find('list', array('fields' => array('field_name', 'field_value'), 'conditions' => array('ProfileCompletion.profile_type_id' => $profile_type_id)));
            
            $cond = array(
                    'ProfileField.active' => 0,
                    'ProfileField.profile_type_id' => $profile_type_id,
                    'ProfileField.type <> ?' => 'heading'
            );
            
            $profile_fields_not_active = $profileFieldModel->find('list', array('conditions' => $cond, 'fields' => array('ProfileField.id')));
            
            if(count($profile_fields_not_active) > 0){
                foreach ($profile_fields_not_active as $value) {
                    if(isset($profile_completion['fields_'.$value])) unset($profile_completion['fields_'.$value]);
                }
            }
            
            $total_per = array_sum($profile_completion);
            
            foreach ($profile_completion as $k => $val) {
                if($k == 'birthday' && empty($viewer[$k])){
                    unset($profile_completion[$k]);
                }
                if(isset($viewer[$k]) && empty($viewer[$k])){
                    unset($profile_completion[$k]);
                }
            }
            
            $profile_fields = $profileFieldModel->getFields($profile_type_id, '', true);
            
            if(!empty($profile_fields)){
                foreach ($profile_fields as $k => $val) {
                    if($val['ProfileField']['type'] == 'location'){
                        $user_location = $userCountryModel->find('first', array('conditions' => array('UserCountry.user_id' => $viewer['id'])));
                        if( empty($user_location['UserCountry']['country_id']) && empty($user_location['UserCountry']['address']) && empty($user_location['UserCountry']['zip']) ){
                            unset($profile_completion['fields_'.$val['ProfileField']['id']]);
                        }
                    }else{
                        $tmp_profile_fields_val = $profileFieldValueModel->find('first', array('conditions' => array('ProfileFieldValue.user_id' => $viewer['id'], 'ProfileFieldValue.profile_field_id' => $val['ProfileField']['id'])));
                        
                        if(empty($tmp_profile_fields_val) || $tmp_profile_fields_val['ProfileFieldValue']['value'] == ''){
                            unset($profile_completion['fields_'.$val['ProfileField']['id']]);
                        }
                    }
                }
            }
            
            $cond = array(
                    'ProfileField.active' => 0,
                    'ProfileField.profile_type_id' => $profile_type_id,
                    'ProfileField.type <> ?' => 'heading'
            );
            
            $profile_fields_not_active = $profileFieldModel->find('list', array('conditions' => $cond, 'fields' => array('ProfileField.id')));
            
            if(count($profile_fields_not_active) > 0){
                foreach ($profile_fields_not_active as $value) {
                    if(isset($profile_completion['fields_'.$value])) unset($profile_completion['fields_'.$value]);
                }
            }
            
            $count_profile_completion = 0;
            
            if(count($profile_completion)) $count_profile_completion = array_sum($profile_completion);
            
            if( $count_profile_completion < $force_update_profile_info && $total_per == 100)
                return false;
                            
        }
        
        return true;
    }

    public function afterRenderSectionProfileCompletenessAppVer1_2($event){
        $profileCompletionModel = MooCore::getInstance()->getModel('ProfileCompletion.ProfileCompletion');
        $profileFieldModel      = MooCore::getInstance()->getModel('ProfileField');
        $profileFieldValueModel = MooCore::getInstance()->getModel('ProfileFieldValue');
        $userCountryModel       = MooCore::getInstance()->getModel('UserCountry');
        $coreBlockModel         = MooCore::getInstance()->getModel('CoreBlock');
        $coreContentModel       = MooCore::getInstance()->getModel('CoreContent');
        $userModel              = MooCore::getInstance()->getModel('User');
        
        $viewer = MooCore::getInstance()->getViewer();
        $viewer_id = MooCore::getInstance()->getViewer('id');        
        $subject = MooCore::getInstance()->getSubject();

        $core_block = $coreBlockModel->find('first', array('conditions' => array('CoreBlock.path_view' => 'profile_completions.profile')));
        $core_block_id = 0;
        if(!empty($core_block)) $core_block_id = $core_block['CoreBlock']['id'];

        $core_content = $coreContentModel->find('first', array('conditions' => array('CoreContent.core_block_id' => $core_block_id)));

        $params = array();
        if(!empty($core_content)){
            $params = json_decode($core_content['CoreContent']['params'], 'array');
            extract($params);
        }

        if(Configure::read('ProfileCompletion.profile_completion_enabled') && !empty($viewer) && $subject['User']['id'] == $viewer_id){

            $profile_type_id = $viewer['User']['profile_type_id'];

            $profile_completion = $profileCompletionModel->find('list', array('fields' => array('field_name', 'field_value'), 'conditions' => array('ProfileCompletion.profile_type_id' => $profile_type_id)));

            $cond = array(
                'ProfileField.active' => 0,
                'ProfileField.profile_type_id' => $profile_type_id,
                'ProfileField.type <> ?' => 'heading'
            );

            $profile_fields_not_active = $profileFieldModel->find('list', array('conditions' => $cond, 'fields' => array('ProfileField.id')));

            if(count($profile_fields_not_active) > 0){
                foreach ($profile_fields_not_active as $value) {
                    if(isset($profile_completion['fields_'.$value])) unset($profile_completion['fields_'.$value]);
                }
            }

            $total_per = 0;
            if(count($profile_completion) > 0){
                $total_per = array_sum($profile_completion);
            }

            $tmp_profile_completion = array();

            foreach ($profile_completion as $k => $val) {
                if($k == 'birthday' && empty($viewer['User'][$k])){
                    $tmp_profile_completion[$k] = $profile_completion[$k];
                    unset($profile_completion[$k]);
                }
                
                if(isset($viewer['User'][$k]) && empty($viewer['User'][$k])){
                    $tmp_profile_completion[$k] = $profile_completion[$k];
                    unset($profile_completion[$k]);
                }                    
            }

            $profile_fields = $profileFieldModel->getFields($profile_type_id, '', true);

            if(!empty($profile_fields)){
                foreach ($profile_fields as $k => $val) {
                    if($val['ProfileField']['type'] == 'location'){
                        $user_location = $userCountryModel->find('first', array('conditions' => array('UserCountry.user_id' => $viewer['User']['id'])));
                        if( empty($user_location['UserCountry']['country_id']) && empty($user_location['UserCountry']['address']) && empty($user_location['UserCountry']['zip']) ){
                            if(isset($profile_completion['fields_'.$val['ProfileField']['id']]))
                                $tmp_profile_completion['fields_'.$val['ProfileField']['id']] = $profile_completion['fields_'.$val['ProfileField']['id']];
                            unset($profile_completion['fields_'.$val['ProfileField']['id']]);
                        }
                    }else{
                        $tmp_profile_fields_val = $profileFieldValueModel->find('first', array('conditions' => array('ProfileFieldValue.user_id' => $viewer['User']['id'], 'ProfileFieldValue.profile_field_id' => $val['ProfileField']['id'])));

                        if(empty($tmp_profile_fields_val) || $tmp_profile_fields_val['ProfileFieldValue']['value'] == ''){
                            $tmp_profile_completion['fields_'.$val['ProfileField']['id']] = $profile_completion['fields_'.$val['ProfileField']['id']];
                            unset($profile_completion['fields_'.$val['ProfileField']['id']]);
                        }                 
                    }
                }
            }

            $array_pc = array(
                'name'      => __d('profile_completion', 'Full Name'),
                'email'     => __d('profile_completion', 'Email Address'),
                'birthday'  => __d('profile_completion', 'Birthday'),
                'gender'    => __d('profile_completion', 'Gender'),
                'timezone'  => __d('profile_completion', 'Timezone'),
                'username'  => __d('profile_completion', 'Username'),
                'about'     => __d('profile_completion', 'About'),
                'avatar'    => __d('profile_completion', 'Avatar')
            );

            $next = null;
            $next_percent = 0;
            $tmp_key = '';

            if(count($tmp_profile_completion) > 0){
                $tmp_key = array_keys($tmp_profile_completion, max($tmp_profile_completion));
                $tmp_key = current($tmp_key);

                $next_percent = $tmp_profile_completion[$tmp_key];

                $next = $tmp_key;
            }

            if(isset($array_pc[$tmp_key])){
                $next = $array_pc[$tmp_key];
            }else{
                $tmp_profile_fields = $profileFieldModel->findById(filter_var($tmp_key, FILTER_SANITIZE_NUMBER_INT));

                if(!empty($tmp_profile_fields))
                    $next = $tmp_profile_fields['ProfileField']['name'];
            }

            $total_percent = ((count($profile_completion) > 0) ? array_sum($profile_completion) : 0);
            $next_key = $tmp_key; // if avatar => '/users/avatar' else '/users/profile'
            $title_update_profile = __d('profile_completion', 'Update Profile');

            $next_url = $this->base.'/users/profile';
            if($next_key == 'avatar') $next_url = $this->base.'/users/avatar';

            $is_show_widget = false;

            if( Configure::read('ProfileCompletion.profile_completion_enabled') && !empty($core_content) && $total_per == 100 && $total_percent != 100 && Configure::read('ProfileCompletion.not_show_widget_100') ){
                $is_show_widget = true;
            }

            if( Configure::read('ProfileCompletion.profile_completion_enabled') && !empty($core_content) && $total_per == 100 && !Configure::read('ProfileCompletion.not_show_widget_100') ){
                $is_show_widget = true;
            }

            $data = array(
                'title_update_profile'  => $title_update_profile,
                'total_per'             => $total_per, // if total per = 100% show widget
                'total_percent'         => $total_percent,
                'total_percent_title'   => sprintf(__d('profile_completion', '%s Profile Completeness'), $total_percent.'%'),
                'title_widget'          => $params['title'],
                'next'                  => $next,
                'next_percent'          => $next_percent,
                'next_key'              => $next_key,
                'next_url'              => $next_url,
                'remaining_bar_color'   => Configure::read('ProfileCompletion.remaining_bar_color'),
                'progress_bar_color'    => Configure::read('ProfileCompletion.progress_bar_color'),
                'is_show_widget'        => $is_show_widget
            );

            $event->result['result']['profile_completion'] = $data;
        }
    }

    public function doAfterDelete($event)
    {
        $model = $event->subject();
        $type = ($model->plugin) ? $model->plugin.'_' : ''.get_class($model);

        if($type == 'ProfileField'){
            $profile_fields_id = $model->id;
            $profileCompletionModel = MooCore::getInstance()->getModel('ProfileCompletion.ProfileCompletion');
            $profileCompletionModel->deleteAll( array('ProfileCompletion.field_name' => 'fields_'.$profile_fields_id) );
        }
    }

    public function beforeRender($event)
    {
        $e = $event->subject();   
        $params = $e->request->params;
        if(Configure::read('ProfileCompletion.profile_completion_enabled')){
            $e->Helpers->Html->css( 
                array(
                    'ProfileCompletion.main'
                ),
                array('block' => 'css')
            );
        }
    }

    public function beforeRenderController($event){
        $e = $event->subject();   
        $params = $e->request->params;

        $force_update_profile_info = Configure::read("ProfileCompletion.force_update_profile_info");        
        $viewer = MooCore::getInstance()->getViewer();

        if(Configure::read('ProfileCompletion.profile_completion_enabled') && !empty($viewer) && !$viewer['Role']['is_admin'] && !$viewer['Role']['is_super'] && $force_update_profile_info > 0  && $e->params['prefix'] != 'admin' && !$e->isApi($e->request)){

            $profileCompletionModel = MooCore::getInstance()->getModel('ProfileCompletion.ProfileCompletion');
            $profileFieldModel      = MooCore::getInstance()->getModel('ProfileField');
            $profileFieldValueModel = MooCore::getInstance()->getModel('ProfileFieldValue');
            $userCountryModel       = MooCore::getInstance()->getModel('UserCountry');

            $profile_type_id = $viewer['User']['profile_type_id'];
            
            $ignore_controller  = array();
            
            $is_app = ($e->request->is('androidApp') || $e->request->is('iosApp'));
            
            if ($is_app)
            {
                $ignore_controller = array('activities', 'blogs', 'events', 'topics', 'photos', 'videos', 'groups', 'users', 'albums');
            }

            if( $profile_type_id <= 0 ){
                if($params['action'] != 'do_logout'){
                    if ($is_app)
                    {
                        $text_error = __d('profile_completion',"Please complete <a href='".$e->request->base.'/users/profile'."'>your profile</a> to hide this warning message!",$e->request->base."/users/profile");

                        if(in_array($params['controller'], $ignore_controller) || ($params['controller'] == 'users' && $params['action'] == 'view')){
                            if (!$e->Session->read('Message.confirm_remind')){
                                $e->Session->setFlash( $text_error , 'default', array('class' => 'Metronic-alerts alert error-message fade in'));
                            }
                        }else{
                            if (!$e->Session->read('Message.confirm_remind')){
                                $e->Session->setFlash( $text_error , 'default', array('class' => 'Metronic-alerts alert prc-error-message-app fade in' ), 'confirm_remind');
                            }
                        }                        
                    }else{
                        $text_error = __d('profile_completion',"Please complete <a href='".$e->request->base.'/users/profile'."'>your profile</a> to hide this warning message!",$e->request->base."/users/profile");
                        if (!$e->Session->read('Message.confirm_remind')){
                            $e->Session->setFlash( $text_error , 'default', array('class' => 'Metronic-alerts alert prc-error-message fade in' ), 'confirm_remind');
                        }
                    }
                }
                
            }

            $profile_completion = $profileCompletionModel->find('list', array('fields' => array('field_name', 'field_value'), 'conditions' => array('ProfileCompletion.profile_type_id' => $profile_type_id)));

            $total_per = array_sum($profile_completion);

            if( $total_per  == 100 && $profile_type_id > 0){
                $cond = array(
                    'ProfileField.active' => 0,
                    'ProfileField.profile_type_id' => $profile_type_id,
                    'ProfileField.type <> ?' => 'heading'
                );

                $profile_fields_not_active = $profileFieldModel->find('list', array('conditions' => $cond, 'fields' => array('ProfileField.id')));

                $tmp_profile_completion = array();

                if(count($profile_fields_not_active) > 0){
                    foreach ($profile_fields_not_active as $value) {
                        if(isset($profile_completion['fields_'.$value])) unset($profile_completion['fields_'.$value]);
                    }
                }

                foreach ($profile_completion as $k => $val) {
                    if($k == 'birthday' && empty($viewer['User'][$k])){
                        $tmp_profile_completion[$k] = $profile_completion[$k];
                        unset($profile_completion[$k]);
                    }
                    if(isset($viewer['User'][$k]) && empty($viewer['User'][$k])){
                        $tmp_profile_completion[$k] = $profile_completion[$k];
                        unset($profile_completion[$k]);
                    }                    
                }

                $profile_fields = $profileFieldModel->getFields($profile_type_id, '', true);

                if(!empty($profile_fields)){
                    foreach ($profile_fields as $k => $val) {
                        if($val['ProfileField']['type'] == 'location'){
                            $user_location = $userCountryModel->find('first', array('conditions' => array('UserCountry.user_id' => $viewer['User']['id'])));
                            if( empty($user_location['UserCountry']['country_id']) && empty($user_location['UserCountry']['address']) && empty($user_location['UserCountry']['zip']) ){
                                if(isset($profile_completion['fields_'.$val['ProfileField']['id']]))
                                    $tmp_profile_completion['fields_'.$val['ProfileField']['id']] = $profile_completion['fields_'.$val['ProfileField']['id']];
                                unset($profile_completion['fields_'.$val['ProfileField']['id']]);
                            }
                        }else{
                            $tmp_profile_fields_val = $profileFieldValueModel->find('first', array('conditions' => array('ProfileFieldValue.user_id' => $viewer['User']['id'], 'ProfileFieldValue.profile_field_id' => $val['ProfileField']['id'])));

                            if(empty($tmp_profile_fields_val) || $tmp_profile_fields_val['ProfileFieldValue']['value'] == ''){
                                $tmp_profile_completion['fields_'.$val['ProfileField']['id']] = $profile_completion['fields_'.$val['ProfileField']['id']];
                                unset($profile_completion['fields_'.$val['ProfileField']['id']]);
                            }                         
                        }
                    }
                }

                $cond = array(
                    'ProfileField.active' => 0,
                    'ProfileField.profile_type_id' => $profile_type_id,
                    'ProfileField.type <> ?' => 'heading'
                );

                $profile_fields_not_active = $profileFieldModel->find('list', array('conditions' => $cond, 'fields' => array('ProfileField.id')));

                if(count($profile_fields_not_active) > 0){
                    foreach ($profile_fields_not_active as $value) {
                        if(isset($profile_completion['fields_'.$value])) unset($profile_completion['fields_'.$value]);
                    }
                }

                $array_pc = array(
                    'name'      => __d('profile_completion', 'Full Name'),
                    'email'     => __d('profile_completion', 'Email Address'),
                    'birthday'  => __d('profile_completion', 'Birthday'),
                    'gender'    => __d('profile_completion', 'Gender'),
                    'timezone'  => __d('profile_completion', 'Timezone'),
                    'username'  => __d('profile_completion', 'Username'),
                    'about'     => __d('profile_completion', 'About'),
                    'avatar'    => __d('profile_completion', 'Avatar')
                );

                $next = null;
                $next_percent = 0;
                $tmp_key = '';

                if(count($tmp_profile_completion) > 0){
                    $tmp_key = array_keys($tmp_profile_completion, max($tmp_profile_completion));
                    $tmp_key = current($tmp_key);

                    $next_percent = $tmp_profile_completion[$tmp_key];

                    $next = $tmp_key;
                } 

                if(isset($array_pc[$tmp_key])){
                    $next = $array_pc[$tmp_key];
                }else{
                    $tmp_profile_fields = $profileFieldModel->findById(filter_var($tmp_key, FILTER_SANITIZE_NUMBER_INT));

                    if(!empty($tmp_profile_fields))
                        $next = $tmp_profile_fields['ProfileField']['name'];
                }

                $count_profile_completion = 0;

                if(count($profile_completion)) $count_profile_completion = array_sum($profile_completion);

                $ignore_action = array('avatar', 'profile', 'do_logout', 'member_login');

                $next_url = $e->request->base.'/users/profile';

                if( $count_profile_completion < $force_update_profile_info){
                    
                    if($params['action'] != 'do_logout'){
                        if ($is_app && in_array($params['controller'], $ignore_controller) && $params['action'] != 'profile')
                        {
                            $text_error = sprintf(__d('profile_completion',"Please complete <a href='%s'>your profile</a> to hide this warning message!",$e->request->base."/users/profile"));
                            if (!$e->Session->read('Message.confirm_remind')){
                                $e->Session->setFlash( $text_error , 'default', array('class' => 'Metronic-alerts alert error-message fade in' ));
                            }
                        }else if($is_app && $params['controller'] == 'users' && $params['action'] == 'profile'){           
                            $text_error = sprintf(__d('profile_completion', 'Your current profile completeness is %s, please finish up to %s to hide this warning message. Please update "%s" to get more "%s"'), $count_profile_completion.'%', $force_update_profile_info.'%', $next, $next_percent.'%');
                            if (!$e->Session->read('Message.confirm_remind')){
                                $e->Session->setFlash( $text_error , 'default', array('class' => 'Metronic-alerts alert prc-error-message-app fade in' ));
                            }
                        }else if($is_app && !in_array($params['controller'], $ignore_controller) ){
                            $text_error = sprintf(__d('profile_completion',"Please complete <a href='%s'>your profile</a> to hide this warning message!",$e->request->base."/users/profile"));
                            if (!$e->Session->read('Message.confirm_remind')){
                                $e->Session->setFlash( $text_error , 'default', array('class' => 'Metronic-alerts alert prc-error-message-app fade in' ));
                            }
                        }else{
                            if($params['controller'] == 'users' && $params['action'] == 'profile'){
                                $text_error = sprintf(__d('profile_completion', 'Your current profile completeness is %s, please finish up to %s to hide this warning message. Please update "%s" to get more "%s"'), $count_profile_completion.'%', $force_update_profile_info.'%', $next, $next_percent.'%');

                                $result = $e->Session->read('Message.confirm_remind');
                                
                                if (!$e->Session->read('Message.confirm_remind') || strpos($result['params']['class'], 'prc-error-message')){
                                    $e->Session->destroy();
                                    $e->Session->setFlash( $text_error , 'default', array('class' => 'Metronic-alerts alert prc-error-message-app fade in' ), 'confirm_remind');
                                }                                
                            }else{
                                $text_error = sprintf(__d('profile_completion',"Please complete <a href='%s'>your profile</a> to hide this warning message!",$e->request->base."/users/profile"));
                                if (!$e->Session->read('Message.confirm_remind') && $params['action'] != 'load_shortcut'){
                                    $e->Session->setFlash( $text_error , 'default', array('class' => 'Metronic-alerts alert prc-error-message fade in' ), 'confirm_remind');
                                }
                            }
                            
                        }
                    }
                    
                }
            }            
            
        }
    }
}

 ?>