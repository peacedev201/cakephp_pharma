<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
class BusinessFollowController extends BusinessAppController {

    public function beforeFilter() 
    {
        parent::beforeFilter();
        $this->loadModel('Business.Business');
        $this->loadModel('Business.BusinessFollow');
        $this->loadModel('Business.BusinessAdmin');
        $this->loadModel('FriendRequest');
        $this->loadModel('Friend');
    }
    
    public function do_follow($business_id)
    {
        $this->autoRender = false;
        
        if(!$this->isLoggedIn())
        {
            $this->_jsonError(__d('business', 'Login or register to continue'), false, array('require_login' => 1));
        }
        else if(!$this->Business->isBusinessExist($business_id))
        {
            $this->_jsonError(__d('business', 'Business not found'));
        }
        
        $package = $this->Business->getBusinessPackage($business_id);
        if(!$package['follow'])
        {
            $business = $this->Business->getOnlyBusiness($business_id);
            $this->_jsonError($this->Business->upgradeMessage($business));
        }
        else if($this->BusinessFollow->isBanned($business_id))
        {
            $this->_jsonError(__d('business', 'You are banned on this business.'));
        }
        else
        {
            if($this->BusinessFollow->isFollowed($business_id))
            {
                $total = $this->BusinessFollow->unFollow($business_id);
                $text = __d('business', 'Follow');
            }
            else
            {
                $total = $this->BusinessFollow->follow($business_id);
                $text = __d('business', 'Unfollow');
                
                //notification
                $business = $this->Business->getOnlyBusiness($business_id);
                $this->Business->sendNotification(
                    $business['Business']['user_id'], 
                    MooCore::getInstance()->getViewer(true), 
                    'business_follow', 
                    $business['Business']['moo_url'], 
                    $business['Business']['name']
                );
                
                //send notification to followers
                $this->BusinessFollow->sendFollowNotification(
                    $business_id, 
                    'business_follow_follow', 
                    MooCore::getInstance()->getViewer(true)
                );
            }
            
            //update business follow counter
            $this->BusinessFollow->updateBusinessFollowCounter($business_id);

            $this->_jsonSuccess(__d('business', 'Success'), null, array(
                'text' => $text,
            ));
        }
    }
    
    public function myfollow()
    {
        $businesses = $this->BusinessFollow->getBusinessFollowList();
        $this->set(array(
            'businesses' => $businesses,
            'user_id' => MooCore::getInstance()->getViewer(true),
            'more_business_url' => '/business_follow/follow_list/page:2',
        ));
        $this->render('/Elements/myfollow');
    }
    
    public function follow_list()
    {
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $businesses = $this->BusinessFollow->getBusinessFollowList($page);
        $this->set(array(
            'businesses' => $businesses,
            'user_id' => MooCore::getInstance()->getViewer(true),
            'more_business_url' => '/business_follow/follow_list/page:'.($page + 1),
        ));
        $this->render('/Elements/lists/follow_list');
    }
    
    public function load_business_followers($business_id)
    {
        $uid = MooCore::getInstance()->getViewer(true);
        $page = !empty($this->request->named['page']) ? $this->request->named['page'] : 1;
        $search = !empty($this->request->data) ? $this->request->data : array();
        if(!isset($search['follower_type']))
        {
            $search['follower_type'] = 2;
        }
        $business = $this->Business->getOnlyBusiness($business_id);
        $followers = $this->BusinessFollow->getBusinessFollowers($business_id, $search, $page);
        
        if ($uid > 0) {
            $friends = $this->Friend->getFriends($uid);
            $friends_request = $this->FriendRequest->getRequestsList($uid);
            $respond = $this->FriendRequest->getRequests($uid);
            $request_id = Hash::combine($respond, '{n}.FriendRequest.sender_id', '{n}.FriendRequest.id');
            $respond = Hash::extract($respond, '{n}.FriendRequest.sender_id');
            $friends_requests = array_merge($friends, $friends_request);
            $this->set(compact('friends', 'respond', 'request_id', 'friends_request'));
        }

        $this->set(array(
            'admin_list' => $this->BusinessAdmin->loadAdminListId($business_id),
            'followers' => $followers,
            'uid' => MooCore::getInstance()->getViewer(true),
            'permission_can_ban' => $this->Business->permission($business_id, BUSINESS_PERMISSION_BAN, $business['Business']['moo_permissions']),
            'more_url' => '/business_follow/load_business_followers/'.$business_id.'/page:'.($page + 1),
        ));
        $this->render('/Elements/lists/follower_list');
    }
    
    public function ban_follower($business_id, $user_id)
    {
        $business = $this->Business->getOnlyBusiness($business_id);
        if($business == null)
        {
            $this->_jsonError(__d('business', 'Business not found'));
        }
        else if(!$this->Business->permission($business_id, BUSINESS_PERMISSION_BAN, $business['Business']['moo_permissions']))
        {
            $this->_jsonError($this->Business->permissionMessage());
        }
        else if($this->BusinessAdmin->isBusinessAdmin($business_id, $user_id))
        {
            $this->_jsonError(__d('business', 'You can not ban admin'));
        }
        else 
        {
            $this->BusinessFollow->updateAll(array(
                'BusinessFollow.is_banned' => 1
            ), array(
                'BusinessFollow.business_id' => $business_id,
                'BusinessFollow.user_id' => $user_id
            ));
            $this->_jsonSuccess(__d('business', 'User has been banned'));
        }
    }
    
    public function unban_follower($business_id, $user_id)
    {
        $business = $this->Business->getOnlyBusiness($business_id);
        if($business == null)
        {
            $this->_jsonError(__d('business', 'Business not found'));
        }
        else if(!$this->Business->permission($business_id, BUSINESS_PERMISSION_BAN, $business['Business']['moo_permissions']))
        {
            $this->_jsonError($this->Business->permissionMessage());
        }
        else 
        {
            $this->BusinessFollow->updateAll(array(
                'BusinessFollow.is_banned' => 0
            ), array(
                'BusinessFollow.business_id' => $business_id,
                'BusinessFollow.user_id' => $user_id
            ));
            $this->_jsonSuccess(__d('business', 'User has been banned'));
        }
    }
}
