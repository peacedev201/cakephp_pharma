<?php
/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
App::uses('SpotlightAppModel', 'Spotlight.Model');
class SpotlightSetting extends SpotlightAppModel {

    public function updateSettings($id, $value)
    {
        $data = array( 'value' => $value );
        $this->id = $id;
        $this->save($data);
    }

    public function getValueSetting($id)
    {
        $value = 0;
        $data = $this->findById($id);
        if ( !empty($data) ) {
            $value = $data['SpotlightSetting']['value'];
        }
        return $value;
    }

    public function integrateCredit()
    {
        if( Configure::read('Credit.credit_enabled') ){
            $actionTypeModel = MooCore::getInstance()->getModel('Credit.CreditActiontypes');
            $action_type = $actionTypeModel->getActionTypeFormModule('join_spotlight');
            if(empty($action_type))
            {
                $data = array(
                    'action_type' => 'join_spotlight',
                    'action_type_name' => 'Joined spotlight',
                    'action_module' => 'spotlight',
                    'action_name' => 'Joined spotlight',
                    'plugin' => 'Spotlight',
                );
                $actionTypeModel->save($data);
            }
        }
    }
}
