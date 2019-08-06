<?php

App::uses('Widget', 'Controller/Widgets');

class notesUsernotesWidget extends Widget {

    public function beforeRender(Controller $controller) {
        $type = MooCore::getInstance()->getSubjectType();
        $viewer = MooCore::getInstance()->getViewer();
        $controller->loadModel('Usernotes.Usernote');
        $is_enable = false;
        $note_content = "";
        $role_can_write_note = $controller->Usernote->noteCheckUserRoles(ROLE_UNOTES_CAN_WRITE_NOTE);
        if ($viewer && Configure::read('Usernotes.usernotes_enabled') && $type == 'User' && $role_can_write_note) {
            $is_enable = true;
            $data = $this->params;
            $user_id = $viewer['User']['id'];
            $noteModel = MooCore::getInstance()->getModel('Usernotes.Usernote');
            $userModel = MooCore::getInstance()->getModel('User');
            $target = $controller->params->params;
            $target_info = $target['pass'][0];
            if (is_numeric($target_info)) {
                $targetData = $userModel->findById($target_info);
            } else {
                $targetData = $userModel->findByUsername($target_info);
            }
            
            $target_id = $targetData['User']['id'];
            $note = $noteModel->getNoteDetail($user_id,$target_id);
            $note_id = 0;
            if($note){
                $note_content = $note['Usernote']['content'];
                $note_id = $note['Usernote']['id'];
            }
            if($target_id == $user_id){
                $is_enable = false;
            }
           $this->setData('note_id', $note_id);
           $this->setData('target_id', $target_id);
           $this->setData('note_content', $note_content);
           $this->setData('note_title', $data['title']);
        }
      
        $this->setData('is_enable', $is_enable);
        
    }

}
