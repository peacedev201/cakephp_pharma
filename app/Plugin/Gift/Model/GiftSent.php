<?php
class GiftSent extends GiftAppModel 
{
    public $validate = array(   
        'sender_id' =>   array(   
            'rule'     => 'notBlank',
            'message'  => 'Sender is required'
        ),
        'receiver_id' =>   array(   
            'rule'     => 'notBlank',
            'message'  => 'Please select friend'
        ),
        'gift_id' =>   array(   
            'rule'     => 'notBlank',
            'message'  => 'Gift not found'
        )
    );

    public $mooFields = array('message');

    public $belongsTo = array(
        'Gift'=> array(
            'className' => 'Gift',
            'foreignKey' => 'gift_id',
            'dependent' => true
        ),
        'Sender'=> array(
            'className' => 'User',
            'foreignKey' => 'sender_id',
            'dependent' => true
        ),
        'Receiver'=> array(
            'className' => 'User',
            'foreignKey' => 'receiver_id',
            'dependent' => true
    ));
    
    public function isSentGiftExist($id)
    {
        $uid = MooCore::getInstance()->getViewer(true);
        return $this->hasAny(array(
            'GiftSent.id' => $id,
            "(GiftSent.receiver_id = $uid OR GiftSent.sender_id = $uid)"
        ));
    }
    
    public function getListGifts($sender_id = null, $receiver_id = null, $page = 1, $limit = 0)
    {
        if($limit == 0)
        {
            $limit = Configure::read('Gift.gift_items_per_page');
        }
        $cond = array();
        if($sender_id != null)
        {
            $cond['GiftSent.sender_id'] = $sender_id;
        }
        if($receiver_id != null)
        {
            $cond['GiftSent.receiver_id'] = $receiver_id;
        }
        return $this->find('all', array(
            'conditions' => $cond,
            'limit' => $limit,
            'order' => array('GiftSent.created' => 'DESC'),
            'page' => $page
        ));
    }
    
    public function getGift($id)
    {
        return $this->find('first', array(
            'conditions' => array(
                'GiftSent.id' => $id
            )
        ));
    }
    
    public function updateViewed($id)
    {
        $this->updateAll(array(
            'GiftSent.viewed' => 1
        ), array(
            'GiftSent.id' => $id,
            'GiftSent.receiver_id' => MooCore::getInstance()->getViewer(true)
        ));
    }
    
    public function deleteGiftSent($id)
    {
        $mGift = MooCore::getInstance()->getModel('Gift.Gift');
        $giftSent = $this->getGift($id);
        if($this->delete($id))
        {
            $mGift->delete($giftSent['GiftSent']['gift_id']);
            return true;
        }
        return false;
    }

    public function getMessage(&$row)
    {
        if (isset($row['message']))
        {
            $row['message'] = htmlspecialchars($row['message']);
            return $row['message'];
        }
        return '';
    }
}