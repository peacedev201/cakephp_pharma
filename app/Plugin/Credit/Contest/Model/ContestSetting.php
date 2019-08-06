<?php
/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
App::uses('ContestAppModel', 'Contest.Model');
class ContestSetting extends ContestAppModel {

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
            $value = $data['ContestSetting']['value'];
        }
        return $value;
    }

    public function integrateCredit()
    {
        if( Configure::read('Credit.credit_enabled') ){
            $actionTypeModel = MooCore::getInstance()->getModel('Credit.CreditActiontypes');
            $action_contest = $actionTypeModel->getActionTypeFormModule('contest');
            if(empty($action_contest))
            {
                $data = array(
                    'action_type' => 'contest',
                    'action_type_name' => 'Create Contest',
                    'action_module' => 'Contest',
                    'action_name' => 'Create a new contest',
                    'plugin' => 'Contest'
                );
                $actionTypeModel->save($data);
            }
            $actionTypeModel->clear();
            $transfer_submit_entry_from = $actionTypeModel->getActionTypeFormModule('transfer_submit_entry_from');
            if(empty($transfer_submit_entry_from))
            {
                $data_submit = array(
                    'action_type' => 'transfer_submit_entry_from',
                    'action_type_name' => 'Receiving credits',
                    'action_module' => 'Contest',
                    'action_name' => 'Receiving credits',
                    'plugin' => 'Contest',
                    'show' => 0
                );
                $actionTypeModel->save($data_submit);
            }
            $actionTypeModel->clear();
            $transfer_submit_entry_to = $actionTypeModel->getActionTypeFormModule('transfer_submit_entry_to');
            if(empty($transfer_submit_entry_to))
            {
                $data_submit = array(
                    'action_type' => 'transfer_submit_entry_to',
                    'action_type_name' => 'Sending credits',
                    'action_module' => 'Contest',
                    'action_name' => 'Sending credits',
                    'plugin' => 'Contest',
                    'show' => 0
                );
                $actionTypeModel->save($data_submit);
            }
            $actionTypeModel->clear();
            $transfer_contest_from = $actionTypeModel->getActionTypeFormModule('transfer_contest_from');
            if(empty($transfer_contest_from))
            {
                $data_submit = array(
                    'action_type' => 'transfer_contest_from',
                    'action_type_name' => 'Receiving credits',
                    'action_module' => 'Contest',
                    'action_name' => 'Receiving credits',
                    'plugin' => 'Contest',
                    'show' => 0
                );
                $actionTypeModel->save($data_submit);
            }
            $actionTypeModel->clear();
            $transfer_contest_to = $actionTypeModel->getActionTypeFormModule('transfer_contest_to');
            if(empty($transfer_contest_to))
            {
                $data_submit = array(
                    'action_type' => 'transfer_contest_to',
                    'action_type_name' => 'Sending credits',
                    'action_module' => 'Contest',
                    'action_name' => 'Sending credits',
                    'plugin' => 'Contest',
                    'show' => 0
                );
                $actionTypeModel->save($data_submit);
            }
            
        }
    }
}
