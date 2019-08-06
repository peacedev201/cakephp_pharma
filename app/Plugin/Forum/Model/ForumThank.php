<?php
class ForumThank extends ForumAppModel
{
    public $belongsTo = array( 'User' );

    public function getUserThank( $id, $uid )
    {
        $thank = $this->find( 'first', array( 'conditions' => array(
            'ForumThank.target_id' => $id,
            'ForumThank.user_id' => $uid
        ) ) );
        return $thank;
    }

    public function getThanks( $id, $limit = null, $page = 1 )
    {
        $thanks = $this->find( 'all', array( 'conditions' => array(
            'ForumThank.target_id' => $id,
            ),
            'limit' => $limit,
            'page' => $page
        ));
        return $thanks;
    }

    public function getCountThanks($id)
    {
        return $this->find('count',array(
            'conditions'=> array(
                    'ForumThank.target_id' => $id,
                 )
            ));
    }

    public function getReplyThanks($uid, $parent_id = 0 )
    {
        $reply_thanks = $this->find( 'list', array( 'conditions' => array(
                'user_id' => $uid,
                'parent_id' => $parent_id,
            ),
            'fields' => array( 'ForumThank.target_id', 'ForumThank.target_id' )
        ) );

        return $reply_thanks;
    }
}