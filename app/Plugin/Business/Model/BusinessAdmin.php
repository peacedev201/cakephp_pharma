<?php
class BusinessAdmin extends BusinessAppModel 
{
    public $validationDomain = 'business';
    public $belongsTo = array( 
        'User'  => array('counterCache' => true),
        'Business'  => array('counterCache' => true),
    );
    public $validate = array(   
        'user_id' =>   array(   
            'rule' => 'notBlank',
            'message' => 'Please select user'
        )
    );
    
    public function isBusinessAdmin($business_id, $user_id = null)
    {
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        //check owner
        if($mBusiness->isBusinessOwner($business_id, $user_id))
        {
            return true;
        }
        
        //check admin
        $cond = array(
            'BusinessAdmin.business_id' => $business_id,
        );
        if($user_id > 0)
        {
            $cond['BusinessAdmin.user_id'] = $user_id;
        }
        else
        {
            $cond['BusinessAdmin.user_id'] = MooCore::getInstance()->getViewer(true);
        }
        return $this->hasAny($cond);
    }
    
    public function hasAdminPermission($business_id)
    {
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        if(MooCore::getInstance()->getViewer(true) == null)
        {
            return false;
        }
        if($this->isBusinessAdmin($business_id) || $mBusiness->isBusinessExist($business_id, MooCore::getInstance()->getViewer(true)))
        {
            return true;
        }
        return false;
    }
    
    public function suggestAdmin($business_id, $keyword)
    {
        $mHelper = MooCore::getInstance()->getHelper('Core_Moo');
        $mUser = MooCore::getInstance()->getModel('User');
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        
        //find exist admin
        $business_admins = $this->find('list', array(
            'conditions' => array(
                'BusinessAdmin.business_id' => $business_id
            ),
            'fields' => array('BusinessAdmin.user_id')
        ));
        $business_admins[] = MooCore::getInstance()->getViewer(true);
        
        //except business owner
        $business = $mBusiness->getOnlyBusiness($business_id);
        $business_admins[] = $business['Business']['user_id'];

        //search
        $data = $mUser->find('all', array(
            'conditions' => array(
                "(User.name LIKE '%$keyword%' OR User.email LIKE '%$keyword%')",
                'User.active' => 1,
                'User.confirmed' => 1,
                'User.id NOT IN('.implode(',', $business_admins).')'
            )
        ));
        $result = array();
        if($data != null)
        {
            foreach($data as $item)
            {
                $result[] = array(
                    'value' => $item['User']['id'],
                    'label' => $item['User']['name'],
                    'image' => $mHelper->getImageUrl(array('User' => $item['User']), array('prefix' => '50_square'))
                );
            }
        }
        return $result;
    }
    
    public function loadAdminList($business_id, $page)
    {
        return $this->find('all', array(
            'conditions' => array(
                'BusinessAdmin.business_id' => $business_id
            ),
            'limit' => 6,
            'page' => $page
        ));
    }
    
    public function getAdminList($business_id)
    {        
        return $this->find('all', array('conditions' => array('BusinessAdmin.business_id' => $business_id)));
    }
    
    public function countAdminList($business_id, $page)
    {
        return $this->find('count', array(
            'conditions' => array(
                'BusinessAdmin.business_id' => $business_id
            )
        ));
    }
    
    public function deleteAdmin($business_id, $user_id = null)
    {
        if (empty($business_id) || empty($user_id)) {
            return false;
        }
        
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        $aBusiness = $mBusiness->getOnlyBusiness($business_id);
        
        /* update owner */
        
        // activities
        //$mBusiness->updateOwnerActivities($business_id, $aBusiness['Business']['user_id'], $user_id);
        
        // photos
        //$this->Business->updateOwnerAlbumPhotos($aBusiness['Business']['album_id'], $aBusiness['Business']['user_id'], $user_id);

        // branches
        //$this->Business->updateOwnerBranches($business_id, $aBusiness['Business']['user_id'], $user_id, $business_id);

        /* update owner */
        
        $cond = array(
            'BusinessAdmin.business_id' => $business_id,
            'BusinessAdmin.user_id' => $user_id,
        );
        return $this->deleteAll($cond);
    }
    
    public function loadAdminListId($business_id)
    {
        return $this->find('list', array(
            'conditions' => array(
                'BusinessAdmin.business_id' => $business_id
            ),
            'fields' => array('BusinessAdmin.user_id', 'BusinessAdmin.user_id')
        ));
    }
    
    public function sendAdminNotification($business_id, $task, $sender_id = null, $link = '')
    {
        $user_ids = $this->loadAdminListId($business_id);
        if($user_ids == null)
        {
            return;
        }
        $business = $mBusiness->getOnlyBusiness($business_id);
        foreach($user_ids as $user_id)
        {
            $this->Business->sendNotification(
                $user_id, 
                $sender_id, 
                $task, 
                $link,
                $business['Business']['name']
            );
        }
    }
}