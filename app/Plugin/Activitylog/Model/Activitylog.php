<?php

App::uses('ActivitylogAppModel','Activitylog.Model');
class Activitylog extends ActivitylogAppModel {
    public $belongsTo = array(
        'User' => array('counterCache' => true),
        'Owner'    => array(
            'className'     => 'User',
            'foreignKey'    => 'user_target_id'),
    );

    public function getActivityLogs($uid, $type = '', $page = 1, $limit = RESULTS_LIMIT){
        $conditions = array(  'Activitylog.user_id' => $uid);
        if($type != '') {
            switch ($type) {
                case 'like':
                    $conditions['Activitylog.action LIKE'] = 'like_%';
                    break;
                case 'dislike':
                    $conditions['Activitylog.action LIKE'] = 'dislike_%';
                    break;
                case 'comment':
                    $conditions['Activitylog.action LIKE'] = 'comment_%';
                    break;
                case 'share':
                    $conditions['Activitylog.action LIKE'] = 'share_%';
                    break;
                case 'blog':
                    $conditions['OR'] = array(
                        'Activitylog.type' => 'Blog_Blog',
                        'Activitylog.item_type' => 'Blog_Blog',
                    );
                    break;
                case 'topic':
                    $conditions['OR'] = array(
                        'Activitylog.type' => 'Topic_Topic',
                        'Activitylog.item_type' => 'Topic_Topic',
                    );
                    break;
                case 'photo':
                    $conditions['OR'] = array(
                        'Activitylog.type' => 'Photo_Photo',
                        'Activitylog.item_type' => 'Photo_Photo',
                    );
                    break;
                case 'album':
                    $conditions['OR'] = array(
                        'Activitylog.type' => 'Photo_Album',
                        'Activitylog.item_type' => 'Photo_Album',
                    );
                    break;
                case 'video':
                    $conditions['OR'] = array(
                        'Activitylog.type' => 'Video_Video',
                        'Activitylog.item_type' => 'Video_Video',
                    );
                    break;
                case 'group':
                    $conditions['OR'] = array(
                        'Activitylog.type' => 'Group_Group',
                        'Activitylog.item_type' => 'Group_Group',
                    );
                    break;
                case 'event':
                    $conditions['OR'] = array(
                        'Activitylog.type' => 'Event_Event',
                        'Activitylog.item_type' => 'Event_Event',
                    );
                    break;
            }
        }
        $results = $this->find('all', array(
            'conditions' => $conditions,
            'limit' => $limit,
            'order' => 'Activitylog.created DESC',
            'page' => $page
        ));
        return $results;
    }
}
