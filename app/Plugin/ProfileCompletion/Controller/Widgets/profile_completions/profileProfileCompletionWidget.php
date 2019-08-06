<?php
/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
App::uses('Widget','Controller/Widgets');

class profileProfileCompletionWidget extends Widget {
    public function beforeRender(Controller $controller) {
        $controller->loadModel('ProfileCompletion.ProfileCompletion');
        $controller->loadModel('ProfileField');
        $controller->loadModel('ProfileFieldValue');
        $controller->loadModel('UserCountry');
        $controller->loadModel('User');

        $params = $this->params;

        $current_view_user_id = '';

        $tmp_params = $controller->request->params;
        if($tmp_params['controller'] == 'users' && $tmp_params['action'] == 'view'){            
            if(isset($tmp_params['username'])){
                $user = $controller->User->find('first', array('conditions' => array("User.username" => $tmp_params['username'])));
                $current_view_user_id = $user['User']['id'];
            }else{
                $current_view_user_id = current($tmp_params['pass']);
            }   
            $this->setData('current_view_user_id', $current_view_user_id);
        }

        $viewer = MooCore::getInstance()->getViewer();
        $this->setData('viewer', $viewer);

        if(!empty($viewer) && Configure::read('ProfileCompletion.profile_completion_enabled')){

            $profile_type_id = $viewer['User']['profile_group'];

            $profile_completion = $controller->ProfileCompletion->find('list', array('fields' => array('field_name', 'field_value'), 'conditions' => array('ProfileCompletion.profile_type_id' => $profile_type_id, 'ProfileCompletion.field_value <>' => 0)));

            $cond = array(
                'ProfileField.active' => 0,
                'ProfileField.profile_type_id' => $profile_type_id,
                'ProfileField.type <> ?' => 'heading'
            );

            $profile_fields_not_active = $controller->ProfileField->find('list', array('conditions' => $cond, 'fields' => array('ProfileField.id')));

            if(count($profile_fields_not_active) > 0){
                foreach ($profile_fields_not_active as $value) {
                    if(isset($profile_completion['fields_'.$value])) unset($profile_completion['fields_'.$value]);
                }
            }

            $total_per = 0;
            if(count($profile_completion) > 0){
                $total_per = array_sum($profile_completion);
            }

            $this->setData('total_per', $total_per);

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

            $profile_fields = $controller->ProfileField->getFields($profile_type_id, '', true);

            if(!empty($profile_fields)){
                foreach ($profile_fields as $k => $val) {
                    if($val['ProfileField']['type'] == 'location'){
                        $user_location = $controller->UserCountry->find('first', array('conditions' => array('UserCountry.user_id' => $viewer['User']['id'])));
                        if( empty($user_location['UserCountry']['country_id']) && empty($user_location['UserCountry']['address']) && empty($user_location['UserCountry']['zip']) ){
                            if(isset($profile_completion['fields_'.$val['ProfileField']['id']]))
                                $tmp_profile_completion['fields_'.$val['ProfileField']['id']] = $profile_completion['fields_'.$val['ProfileField']['id']];
                            unset($profile_completion['fields_'.$val['ProfileField']['id']]);
                        }
                    }else{
                        $tmp_profile_fields_val = $controller->ProfileFieldValue->find('first', array('conditions' => array('ProfileFieldValue.user_id' => $viewer['User']['id'], 'ProfileFieldValue.profile_field_id' => $val['ProfileField']['id'])));

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
                'avatar'    => __d('profile_completion', 'Avatar'),
                'specialty'    => __d('profile_completion', 'Specialty'),
                'university_id'    => __d('profile_completion', 'University'),
                'admission_year'    => __d('profile_completion', 'Admission year'),
                'mobile'    => __d('profile_completion', 'Mobile'),
                'sub_mail'    => __d('profile_completion', 'Sub email'),
                'submail_confirmed'    => __d('profile_completion', 'Sub email confirmed'),
                'job_belong_to'    => __d('profile_completion', 'Job belong to'),
                'com_phone'    => __d('profile_completion', 'Company phone'),
                'com_name'    => __d('profile_completion', 'Company name'),
                'com_zip'    => __d('profile_completion', 'Zip code'),
                'com_address_1'    => __d('profile_completion', 'Address 1'),
                'com_address_2'    => __d('profile_completion', 'Address 2'),
                'com_fax'    => __d('profile_completion', 'Fax'),
                'com_title'    => __d('profile_completion', 'Title'),
                'com_department'    => __d('profile_completion', 'Company department'),
                'sale_area'    => __d('profile_completion', 'Sale area'),
                'job_interest'    => __d('profile_completion', 'Major job of interests'),
                'major_place'    => __d('profile_completion', 'Major place of employment'),
                'com_homepage'    => __d('profile_completion', 'Home page'),
                'uni_grade'    => __d('profile_completion', 'Grade'),
                'mail_to'    => __d('profile_completion', 'Confirm email'),

            );

            $next = null;
            $next_percent = 0;

            $tmp_key = null;

            if(count($tmp_profile_completion) > 0){
                $tmp_key = array_keys($tmp_profile_completion, max($tmp_profile_completion));
                $tmp_key = current($tmp_key);

                $next_percent = $tmp_profile_completion[$tmp_key];

                $next = $tmp_key;
            }               

            if(isset($array_pc[$tmp_key])){
                $next = $array_pc[$tmp_key];
            }else{
                $tmp_profile_fields = $controller->ProfileField->findById(filter_var($tmp_key, FILTER_SANITIZE_NUMBER_INT));

                if(!empty($tmp_profile_fields))
                    $next = $tmp_profile_fields['ProfileField']['name'];
            }

            $this->setData('profile_completion', $profile_completion);
            $this->setData('next', $next);
            $this->setData('next_percent', $next_percent);
            $this->setData('tmp_key', $tmp_key);
            $this->setData('title_enable', $params['title_enable']);
        }

    }
}