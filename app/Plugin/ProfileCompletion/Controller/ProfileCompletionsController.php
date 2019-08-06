<?php 

class ProfileCompletionsController extends ProfileCompletionAppController
{
    
    public function admin_index(){
        $this->loadModel('ProfileType');
        $this->loadModel('ProfileField');
        $this->loadModel('ProfileCompletion.ProfileCompletion');

//        $profile_type = $this->ProfileType->find('list', array( 'conditions' => array('actived' => 1), 'fields' => array('id', 'name') ) );
        $profile_type = array(
            PHARMACIST_GENERAL => __('Pharmacist general'),
            PHARMACIST_SALE => __('Pharmacist sales'),
            PHARMACIST_OTHER => __('Pharmacist other'),
            OTHER_GENERAL => __('Others general'),
            OTHER_SALE => __('Others sale'),
            OTHER_OTHER => __('Others other'),
            GROUP_STUDENT => __('Student'),
        );

        $profile_type_id = ( isset($_GET['profile_type_id']) ? $_GET['profile_type_id'] : key($profile_type) );

        $profile_fields = $this->ProfileField->getFields($profile_type_id, '', true);

        foreach ($profile_fields as $k => $val) {
            foreach ($val['nameTranslation'] as $translate) {
                if ($translate['locale'] == Configure::read('Config.language'))
                {
                    $profile_fields[$k]['ProfileField']['name'] = $translate['content'];
                }
            }           
        }

        $profile_completion = $this->ProfileCompletion->find('list', array('fields' => array('field_name', 'field_value'), 'conditions' => array('ProfileCompletion.profile_type_id' => $profile_type_id)));

        $this->set('profile_type', $profile_type);
        $this->set('profile_fields', $profile_fields);
        $this->set('profile_completion', $profile_completion);
        $this->set('title_for_layout', __d('profile_completion', 'Profile Completion Manager'));

        if($this->request->is('post')){
            $data = $this->request->data;
            $this->set('data', $data);

            $profile_type_id = $data['profile_type_id'];
            unset($data['profile_type_id']);

            $negative = array_filter($data, function ($v) {
                return $v < 0;
            });

            if(count($negative) > 0){
                $text_error = __d('profile_completion', 'The number entered is not as negative numbers');

                $this->Session->setFlash( $text_error , 'default', array('class' => 'Metronic-alerts alert alert-danger fade in' ));
                return;
            }

            $data = array_map(function($val){ return round($val, 2);}, $data);

            if(array_sum($data) < 100 || array_sum($data) > 100){
                $text_error = sprintf(__d('profile_completion', 'Total profile competeness is not 100%s, please check %s of each field again. <br> Current Total Percent: %s'), '%', '%', ((count($data) > 0) ? array_sum($data) : 0).'%');

                $this->Session->setFlash( $text_error , 'default', array('class' => 'Metronic-alerts alert alert-danger fade in' ));
                // $this->redirect( '/admin/profile_completion/profile_completions?profile_type_id='.$profile_type_id );
                return;
            }

            foreach ($data as $k => $val) {
                $tmp_data = $this->ProfileCompletion->find('first', array('conditions' => array( 'ProfileCompletion.field_name' => $k, 'ProfileCompletion.profile_type_id' => $profile_type_id )));

                if( !empty($tmp_data) ){
                    $tmp_data['ProfileCompletion']['field_value'] = $val;
                    $this->ProfileCompletion->clear();
                    $this->ProfileCompletion->set($tmp_data);
                    $this->ProfileCompletion->save();
                }else{
                    $data_ins['ProfileCompletion'] = array(
                        'field_name'        => $k,
                        'field_value'       => $val,
                        'profile_type_id'   => $profile_type_id
                    );

                    $this->ProfileCompletion->clear();
                    $this->ProfileCompletion->set($data_ins);
                    $this->ProfileCompletion->save();
                }
            }

            $this->Session->setFlash( __d('profile_completion', 'Profile completion have been saved') , 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));
            $this->redirect( '/admin/profile_completion/profile_completions?profile_type_id='.$profile_type_id );
        }
    }

    /*public function admin_ajax_loadfields(){
        $params = $this->request->data;

        $this->loadModel('ProfileType');
        $this->loadModel('ProfileField');
        $this->loadModel('ProfileCompletion.ProfileCompletion');

        $profile_fields = $this->ProfileField->getFields($params['profile_type_id'], '', true);

        $profile_type_id = $params['profile_type_id'];

        foreach ($profile_fields as $k => $val) {
            foreach ($val['nameTranslation'] as $translate) {
                if ($translate['locale'] == Configure::read('Config.language'))
                {
                    $profile_fields[$k]['ProfileField']['name'] = $translate['content'];
                }
            }           
        }

        $profile_completion = $this->ProfileCompletion->find('list', array('fields' => array('field_name', 'field_value'), 'conditions' => array('ProfileCompletion.profile_type_id' => $profile_type_id)));

        $this->set('profile_fields', $profile_fields);
        $this->set('profile_completion', $profile_completion);
    }*/

    public function profile_app($current_view_user_id = null){
        $this->loadModel('ProfileCompletion.ProfileCompletion');
        $this->loadModel('ProfileField');
        $this->loadModel('ProfileFieldValue');
        $this->loadModel('UserCountry');
        $this->loadModel('CoreBlock');
        $this->loadModel('CoreContent');
        
        $viewer = MooCore::getInstance()->getViewer();

        $core_block = $this->CoreBlock->find('first', array('conditions' => array('CoreBlock.path_view' => 'profile_completions.profile')));
        $core_block_id = 0;
        if(!empty($core_block)) $core_block_id = $core_block['CoreBlock']['id'];

        $core_content = $this->CoreContent->find('first', array('conditions' => array('CoreContent.core_block_id' => $core_block_id)));

        if(!empty($core_content)){
            $params = json_decode($core_content['CoreContent']['params'], 'array');
            extract($params);
        }

        $this->set('current_view_user_id', $current_view_user_id);
        $this->set('core_content', $core_content);
        $this->set('viewer', $viewer);

        if(!empty($viewer) && Configure::read('ProfileCompletion.profile_completion_enabled') && !empty($core_content)){

            $profile_type_id = $viewer['User']['profile_type_id'];

            $profile_completion = $this->ProfileCompletion->find('list', array('fields' => array('field_name', 'field_value'), 'conditions' => array('ProfileCompletion.profile_type_id' => $profile_type_id)));

            $cond = array(
                'ProfileField.active' => 0,
                'ProfileField.profile_type_id' => $profile_type_id,
                'ProfileField.type <> ?' => 'heading'
            );

            $profile_fields_not_active = $this->ProfileField->find('list', array('conditions' => $cond, 'fields' => array('ProfileField.id')));

            if(count($profile_fields_not_active) > 0){
                foreach ($profile_fields_not_active as $value) {
                    if(isset($profile_completion['fields_'.$value])) unset($profile_completion['fields_'.$value]);
                }
            }

            $total_per = 0;
            if(count($profile_completion) > 0){
                $total_per = array_sum($profile_completion);
            }

            $this->set('total_per', $total_per);

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

            $profile_fields = $this->ProfileField->getFields($profile_type_id, '', true);

            if(!empty($profile_fields)){
                foreach ($profile_fields as $k => $val) {
                    if($val['ProfileField']['type'] == 'location'){
                        $user_location = $this->UserCountry->find('first', array('conditions' => array('UserCountry.user_id' => $viewer['User']['id'])));
                        if( empty($user_location['UserCountry']['country_id']) && empty($user_location['UserCountry']['address']) && empty($user_location['UserCountry']['zip']) ){
                            if(isset($profile_completion['fields_'.$val['ProfileField']['id']]))
                                $tmp_profile_completion['fields_'.$val['ProfileField']['id']] = $profile_completion['fields_'.$val['ProfileField']['id']];
                            unset($profile_completion['fields_'.$val['ProfileField']['id']]);
                        }
                    }else{
                        $tmp_profile_fields_val = $this->ProfileFieldValue->find('first', array('conditions' => array('ProfileFieldValue.user_id' => $viewer['User']['id'], 'ProfileFieldValue.profile_field_id' => $val['ProfileField']['id'])));

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
                $tmp_profile_fields = $this->ProfileField->findById(filter_var($tmp_key, FILTER_SANITIZE_NUMBER_INT));

                if(!empty($tmp_profile_fields))
                    $next = $tmp_profile_fields['ProfileField']['name'];
            }

            $this->set('profile_completion', $profile_completion);
            $this->set('title', $title);
            $this->set('next', $next);
            $this->set('next_percent', $next_percent);
            $this->set('tmp_key', $tmp_key);
            $this->set('title_enable', $params['title_enable']);
        }
    }

}