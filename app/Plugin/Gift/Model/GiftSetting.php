<?php
/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
App::uses('GiftAppModel', 'Gift.Model');
class GiftSetting extends GiftAppModel {

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
            $value = $data['GiftSetting']['value'];
        }
        return $value;
    }

    public function integrateCredit()
    {
        if( Configure::read('Credit.credit_enabled') ){
            $actionTypeModel = MooCore::getInstance()->getModel('Credit.CreditActiontypes');
            $action_type = $actionTypeModel->getActionTypeFormModule('send_gift');
            if(empty($action_type))
            {
                $data = array(
                    'action_type' => 'send_gift',
                    'action_type_name' => 'Sent a gift',
                    'action_module' => 'gift',
                    'action_name' => 'Sent a gift',
                    'plugin' => 'Gift'
                );
                $actionTypeModel->save($data);
            }
        }
    }
}
