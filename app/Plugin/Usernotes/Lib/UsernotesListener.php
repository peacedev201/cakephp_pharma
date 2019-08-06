<?php

App::uses('CakeEventListener', 'Event');

class UsernotesListener implements CakeEventListener {

    public function implementedEvents() {
        return array(
            'profile.afterRenderMenu' => 'afterRenderMenu',
            'MooView.beforeRender' => 'beforeRender',
            'Controller.Role.afterSave'=>'afterSaveRoles',
            'profile.mooApp.afterRenderMenu' => 'apiAfterRenderMenu'
        );
    }

    public function apiAfterRenderMenu($e)
    {
        if (Configure::read('Usernotes.usernotes_enabled')) {
            $viewer_id = MooCore::getInstance()->getViewer('id');
            
            $subject = MooCore::getInstance()->getSubject();
            if($subject['User']['id'] != $viewer_id){
                $e->data['result']['usernotes'] = array(
                        'text' => __d('usernotes','Notes'),
                        'url' => FULL_BASE_URL . $e->subject()->request->base . '/usernotess/profile_user_add_note/'. $subject['User']['id'],
                        'cnt' => 0
                );
            }else{
                $e->data['result']['usernotes'] = array(
                        'text' => __d('usernotes','My Notes'),
                        'url' => FULL_BASE_URL . $e->subject()->request->base . '/usernotess/browser',
                        'cnt' => 0
                );
            }  
        }
    }

    public function beforeRender($event) {

        if (Configure::read('Usernotes.usernotes_enabled')) {
            $e = $event->subject();
            $e->Helpers->Html->css(array(
                'Usernotes.main'
                    ), array('block' => 'css')
            );
            $e->Helpers->MooRequirejs->addPath(array(
                "mooUsernotes" => $e->Helpers->MooRequirejs->assetUrlJS("Usernotes.js/main.js"),
            ));
            $e->Helpers->MooPopup->register('usernotesModal');
            $e->addPhraseJs(array(
                'usn_confirm' => __d('usernotes','Confirm'),
                'usn_ok' => __d('usernotes','Ok'),
                'usn_cancel' => __d('usernotes','Cancel'),
                'usn_please_confirm' => __d('usernotes','Please Confirm'),
                'usn_please_confirm_remove_this_note' => __d('usernotes','Are you sure you want to remove this note?'),
            ));
        }
    }
    
        public function afterSaveRoles($event){
        $e = $event->subject();
        if (Configure::read('Usernotes.usernotes_enabled')) {
            $data = $e->data;
            $is_admin = $data['is_admin'];
            $mCMitem = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
            $noteItem = $mCMitem->findByPlugin('Usernotes');
            $item_roles = array();
            if($noteItem){
                $noteItem = $noteItem['CoreMenuItem'];
                $item_roles = json_decode($noteItem['role_access'],true);
            }

            if(empty($data['param_usernotes_can_write_note'])  && !$is_admin){
                // eleminate ads in core menuitem
                 if($item_roles){
                     $item_roles = array_diff($item_roles,array($data['id']));
                     $item_roles = array_values($item_roles);
                     $mCMitem->save(array('id'=>$noteItem['id'] ,'role_access'=>json_encode($item_roles)));
                 }
            }else{
                if($item_roles){
                    $item_roles[] = $data['id'];
                    $item_roles = array_unique($item_roles);
                    $mCMitem->save(array('id'=>$noteItem['id'] ,'role_access'=>json_encode($item_roles)));
                }
            }
            
        }

    }
    
        public function afterRenderMenu($event) {
        $e = $event->subject();
        $noteModel = MooCore::getInstance()->getModel('Usernotes.Usernote');
        $role_note_write_note = $noteModel->noteCheckUserRoles(ROLE_UNOTES_CAN_WRITE_NOTE);
        if (Configure::read('Usernotes.usernotes_enabled') && $role_note_write_note) {
            $user_id = AuthComponent::user('id');
            if (!empty($user_id)) {
                $userProile = MooCore::getInstance()->getSubject();
                $user_profile_id = 0;
                if (!empty($userProile)) {
                    $user_profile_id = $userProile['User']['id'];
                }
                if ($user_profile_id == $user_id) {
                    $total_notes = $noteModel->countAllNoteByUser($user_id);
                    if(!$e->request->is('androidApp') && !$e->request->is('iosApp')){
                        echo $e->element('Usernotes./link_to_my_notes', array('user_id' => $user_id,'total'=>$total_notes));
                    }
                }else{
                    if($e->request->is('androidApp') || $e->request->is('iosApp')){
                        echo $e->element('Usernotes./add_to_note', array());
                    }                    
                }
            }
        }
    }

}
