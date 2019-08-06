<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
class BusinessCheckinController extends BusinessAppController {

    public function beforeFilter() 
    {
        parent::beforeFilter();
        $this->loadModel('Business.Business');
        $this->loadModel('Business.BusinessCheckin');
        $this->loadModel('Business.Business');
        $this->loadModel('Notification');
    }
    
    public function load_business_checkin($business_id)
    {
        
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $checkins = $this->BusinessCheckin->getPeopleCheckin($business_id, $page);

        $this->set(array(
            'checkins' => $checkins,
            'more_url' => '/business_checkin/load_business_checkin/'.$business_id.'/page:'.($page + 1),
        ));
        $this->render('/Elements/lists/checkin_list');
    }
    
    public function do_checkin()
    {
        $data = $this->request->data;
        $package = $this->Business->getBusinessPackage($data['business_id']);
        $business = $this->Business->getOnlyBusiness($data['business_id']);
        if(!$package['checkin'])
        {
            $this->_jsonError($this->Business->upgradeMessage($business));
        }
        if(empty($business))
        {
            $this->_jsonError(__d('business', 'Business not found'));
        }
        else if(empty($data['content']))
        {
            $this->_jsonError(__d('business', 'Content can not be empty'));
        }
        else
        {
 
            $userTagging = isset($this->request->data['userTagging']) ? $this->request->data['userTagging'] : '';
                       
            if($this->BusinessCheckin->save(array(
                'business_id' => $data['business_id'],
                'user_id' => MooCore::getInstance()->getViewer(true),
                'content' => $data['content']
            )))
            {
                $checkin_id = $this->BusinessCheckin->id;
                if (!empty($userTagging)){
                    $user_tagging_arr = explode(',', $userTagging);
                    foreach ($user_tagging_arr as $user_id) {
                        /*$this->BusinessCheckin->clear();
                        $this->BusinessCheckin->save(array(
                            'business_id' => $data['business_id'],
                            'user_id' => $user_id
                        ));*/
                        
                        $this->Business->sendNotification(
                            $user_id, 
                            MooCore::getInstance()->getViewer(true), 
                            'business_checkin_tag', 
                            $business['Business']['moo_url'], 
                            $business['Business']['name']
                        );
                    }                                    
                }
                
                //update business check in counter
                $this->Business->updateCheckinCounter($data['business_id']);
                
                $user_tagging_id = '';
                if(!empty($userTagging)){
                    $this->loadModel('UserTagging');
                    //save for activities
                    /*$this->UserTagging->create();
                    $this->UserTagging->save(array(
                        'item_id' => $activity['Activity']['id'],
                        'item_table' => 'activities',
                        'users_taggings' => $userTagging
                    ));*/
                    
                    //save for checkin
                    $this->UserTagging->create();
                    $this->UserTagging->actsAs['Notification'] = null;
                    $this->UserTagging->save(array(
                        'item_id' => $checkin_id,
                        'item_table' => 'business_checkin',
                        'users_taggings' => $userTagging
                    ));
                    $user_tagging_arr = explode(',', $userTagging);
                    $notis = $this->Notification->find('list', array(
                        'conditions' => array('Notification.action' => 'tagged_status'),
                        'limit' => count($user_tagging_arr),
                        'fields' => array('Notification.id', 'Notification.id'),
                        'order' => array('Notification.id' => 'DESC')
                    ));
                    if($notis != null)
                    {
                        foreach($notis as $noti)
                        {
                            $this->Notification->delete($noti);
                        }
                    }
                    $user_tagging_id = $this->UserTagging->id;
                }
                
                //activity
                $activity = $this->Business->saveCheckinActivity($data['business_id'], $data['content'], $user_tagging_id);
                
                $this->_jsonSuccess(__d('business', 'Successfully checked in'), true, array(
                    'location' => $business['Business']['moo_hrefcheckin']
                ));
            }
            $this->_jsonError(__d('business', 'Can not check in, please try again!'));
        }
    }
}
