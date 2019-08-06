<?php
App::uses('Widget','Controller/Widgets');

class badgeFriendInviterWidget extends Widget {
    public function beforeRender(Controller $controller) {
        $uid = MooCore::getInstance()->getViewer(true);
        $type = MooCore::getInstance()->getSubjectType();
        $subject = MooCore::getInstance()->getSubject();

        if ($type != 'User')
            $user_id = $uid;
        else
            $user_id = $subject['User']['id'];
        
       $invite_model = MooCore::getInstance()->getModel('FriendInviter.Invite');
       
       $user_model = MooCore::getInstance()->getModel('User');
                  
       $angel_list = $user_model->find('all', array(
           'joins' =>  array(
                array('table' => $user_model->tablePrefix . 'invites',
                    'alias' => 'Invite',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Invite.user_id = User.id',
                    )
                )
            ),
           'conditions' => array('Invite.new_user_id' => $user_id),
       ));
       $this->setData('angel_list', $angel_list);
       
       $angel_invitation = Configure::read('FriendInviter.invites_greate_angel');
       $best_angel = false;
       if(!empty($angel_invitation) && is_numeric($angel_invitation)){
          $total_successful_invite =  $invite_model->getTotalSignupInvite($user_id);
          $best_angel = $total_successful_invite >= $angel_invitation;
       }
       
       $this->setData('best_angel', $best_angel);
    }
}