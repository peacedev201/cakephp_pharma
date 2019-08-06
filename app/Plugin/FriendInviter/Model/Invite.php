<?php

/**
 * Invites Model
 *
 * @property Client $Client
 */
class Invite extends AppModel {

    public function isIdExist($id) {
        return $this->hasAny(array('id' => $id));
    }

    public function getInvites($type = null, $param = null, $page = 1, $limit = RESULTS_LIMIT) {
        $cond = array();

        switch ($type) {
            case 'email':
                $cond = array('Invite.user_id' => $param, "Invite.recipient <> ''", 'Invite.new_user_id = 0');
                break;
            case 'message':
                $message_provider = array('linkedin', 'twitter', 'facebook');
                $cond = array('Invite.user_id' => $param, 'Invite.service' => $message_provider);
                break;
            case 'pending':
                $cond = array('Invite.user_id' => $param, 'Invite.new_user_id = 0');
                break;
            default:
                $cond = array('1 = 1');
        }

        $invites = $this->find('all', array('conditions' => $cond, 'limit' => $limit, 'page' => $page));

        return $invites;
    }

    public function deleteInvite($id) {
        $this->delete($id);
    }

    public function getPopularInviters($limit = 2) {

        $params = array(
            'conditions' => array(),
            'fields' => array('Invite.user_id', 'COUNT(Invite.id) AS invites_count', 'usr.id', 'usr.name', 'usr.email', 'usr.role_id', 'usr.avatar', 'usr.photo', 'usr.cover', 'usr.username'),
            'order' => array('invites_count DESC'),
            'group' => array('Invite.user_id'), //fields to GROUP BY
            'limit' => $limit,
        );

        
        $params['joins'] = array(
            array(
                    'table' => 'users',
                    'alias' => 'usr',
                    'type' => 'INNER',
                    'conditions' => array('usr.id = Invite.user_id')
                ),              
        );
        
        $inviters = $this->find('all', $params);
       
        return $inviters;
    }
    
      
  // DENTUIT-1121
  public function getTotalSignupInvite($uid) {
        $invites_count = $this->find('count', array(
           'joins' =>  array(
                array('table' => $this->tablePrefix . 'users',
                    'alias' => 'User',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Invite.new_user_id = User.id',
                    )
                )
            ),
            'conditions' => array('Invite.user_id' => $uid , 'Invite.new_user_id <> 0'),
            'fields' => 'DISTINCT Invite.recipient',
        ));
              
        return $invites_count;
  }
    
}
