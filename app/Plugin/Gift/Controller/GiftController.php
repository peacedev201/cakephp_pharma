<?php 
class GiftController extends GiftAppController{
    public $components = array('Paginator');
    public function __construct($request = null, $response = null) 
    {
        parent::__construct($request, $response);
        $this->url = $this->request->base.'/gifts/';
        $this->admin_url = $this->request->base.'/admin/gift/gift/';
        $this->set('url', $this->url);
        $this->set('admin_url', $this->admin_url);
        $this->loadModel('Gift.Gift');
        $this->loadModel('Gift.GiftCategory');
        $this->loadModel('Gift.GiftSent');
        $this->loadModel('Gift.GiftSetting');
        $this->gift_integrate_credit = $this->GiftSetting->getValueSetting('gift_integrate_credit');
    }
    
    /////////////////////////////////////////////backend/////////////////////////////////////////////
    public function admin_index()
    {
        $keyword = isset($this->request->query['keyword']) ? $this->request->query['keyword'] : null;
        $gifts = $this->Gift->loadManageGiftList($this, $keyword);

        $this->set(array(
            'title_for_layout' => __d('Gift', 'Manage Gifts'),
            'gifts' => $gifts,
            'keyword' => $keyword,
        ));
    }
    
    public function admin_create($id = null)
    {
        $this->create($id, true);
        $this->set(array(
            'title_for_layout' => __d('Gift', 'Create Gift'),
            'is_ffmpeg_installed' => $this->Gift->checkFfmpeg(Configure::read('Gift.gift_path_to_ffmpeg')) === true ? 1 : 0
        ));
        $this->loadModel('Gift.GiftSetting');
        $this->set('gift_integrate_credit', $this->gift_integrate_credit);
    }
    
    public function admin_save()
    {
        $data = $this->request->data['Gift'];
        $data['user_id'] = MooCore::getInstance()->getViewer(true);
        $data['is_public'] = 1;
        $data['extension'] = !empty($data['filename']) ? pathinfo($data['filename'], PATHINFO_EXTENSION) : '';
        $data['price'] = !empty($data['price']) ? $data['price'] : 0;
        $data['enable'] = !empty($data['enable']) ? 1 : 0;
        
        if($data["id"] > 0)
        {
            $this->Gift->id = $data["id"];
        }
        $this->Gift->set($data);
        $this->_validateData($this->Gift);
        if($this->Gift->save($data))
        {
            //increate counter
            if($data['enable'] == 1)
            {
                $this->GiftCategory->updateGiftItemCount($this->Gift->id);
            }
            $this->_jsonSuccess(__d('gift', 'Successfully saved'), __d('gift', 'Successfully saved'), array('url' => $this->admin_url));
        }
        $this->_jsonError(__d('gift', 'Something went wrong, please try again'));
    }
    
    public function admin_delete($id = null)
    {
        if(!empty($this->request->data['cid']))
        {
            foreach($this->request->data['cid'] as $id)
            {
                $gift = $this->Gift->getGift($id);
                $this->Gift->deleteGift($id);
                $this->GiftCategory->decreaseCounter($gift['Gift']['gift_category_id'], 'item_count');
            }
        }
        else if(!empty($id))
        {
            $gift = $this->Gift->getGift($id);
            $this->Gift->deleteGift($id);
            $this->GiftCategory->decreaseCounter($gift['Gift']['gift_category_id'], 'item_count');
        }
        $this->_redirectSuccess(__d('gift', 'Successfully deleted'), '/admin/gift/gift');
    }
    
    public function admin_do_active($iId, $enable = null)
    {
        if( !$this->Gift->isGiftExist($iId) )
        {
            $this->_redirectError(__d('gift', 'This item does not exist'), $this->referer());
        }
        else
        {
            $this->Gift->id = $iId;
            $this->Gift->save(array('enable' => $enable));
            $this->GiftCategory->updateGiftItemCount($iId);
            $this->_redirectSuccess(__d('gift', 'Successfully updated'), $this->referer());
        }
    }
    
    /////////////////////////////////////////////frontend/////////////////////////////////////////////
    public function index($type = null, $param = null)
    {
        $more_url = '/gifts/ajax_browse/'.$type.'/page:2';
        if($type == 'my' && MooCore::getInstance()->getViewer(true) == null)
        {
            $type = '';
        }
        if($type == 'my' && $param == GIFT_SENT)
        {
            $gifts = $this->GiftSent->getListGifts(MooCore::getInstance()->getViewer(true), null);
            $more_url = '/gifts/ajax_browse/'.$type.'/'.$param.'/page:2';
        }
        else if($type == 'my' && $param == GIFT_RECEIVED)
        {
            $gifts = $this->GiftSent->getListGifts(null, MooCore::getInstance()->getViewer(true));
            $more_url = '/gifts/ajax_browse/'.$type.'/'.$param.'/page:2';
        }
        else
        {
            $gifts = $this->Gift->getListGifts($type, 1, 0, $param);
        }
        $this->set(array(
            'gifts' => $gifts,
            'type' => $type,
            'param' => $param,
            'more_url' => $more_url,
            'page' => 1
        ));
    }
    
    public function ajax_browse($type = null, $param = null)
    {
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $more_url = '/gifts/ajax_browse/page:'.($page + 1);
        if($type == 'my')
        {
            $more_url = '/gifts/ajax_browse/'.$type.'/page:'.($page + 1);
        }
        if($type == 'my' && $param == GIFT_SENT)
        {
            $gifts = $this->GiftSent->getListGifts(MooCore::getInstance()->getViewer(true), null, $page);
            $more_url = '/gifts/ajax_browse/'.$type.'/'.$param.'/page:'.($page + 1);
        }
        else if($type == 'my' && $param == GIFT_RECEIVED)
        {
            $gifts = $this->GiftSent->getListGifts(null, MooCore::getInstance()->getViewer(true), $page);
            $more_url = '/gifts/ajax_browse/'.$type.'/'.$param.'/page:'.($page + 1);
        }
        else
        {
            $gifts = $this->Gift->getListGifts($type, $page, 0, $param);
            if($type == "cat")
            {
                $more_url = '/gifts/ajax_browse/cat/'.$param.'/page:'.($page + 1);
            }
        }
        $this->set(array(
            'gifts' => $gifts,
            'more_url' => $more_url,
            'type' => $type,
            'param' => $param,
            'page' => $page
        ));
        if($type == 'my')
        {
            $this->render('Elements/lists/my_gifts_list');
        }
        else
        {
            $this->render('Elements/lists/gifts_list');
        }
    }

    public function create($id = null, $is_admin = false)
    {
        if(!MooCore::getInstance()->getViewer(true))
        {
            $this->redirect('/users/member_login');
        }
        if (!$is_admin && (!$this->permission_can_send_gift || !$this->permission_can_create_gift) && 
            !$this->Gift->isGiftExist($id, null, MooCore::getInstance()->getViewer(true)))
        {
            $this->redirect('/404');
        }
        
        //check clone from public gift
        $is_clone = false;
        if($this->Gift->isGiftExist($id, null, null, true))
        {
            $is_clone = true;
        }
        
        //check edit or create new
        if($is_clone || $this->Gift->isGiftExist($id, null, MooCore::getInstance()->getViewer(true)))
        {
            $gift = $this->Gift->getGift($id);
        }
        else
        {
            $gift = $this->Gift->initFields();
        }
        
        
        $this->request->data = $gift;
        
        //gift categories
        $categories = $this->GiftCategory->getCategories('list');
        
        $this->set(array(
            'categories' => $categories,
            'gift' => $gift,
            'is_clone' => $is_clone,
            'is_ffmpeg_installed' => $this->Gift->checkFfmpeg(Configure::read('Gift.gift_path_to_ffmpeg')) === true ? 1 : 0
        ));
        $this->set('gift_integrate_credit', $this->gift_integrate_credit);
    }
    
    public function save()
    {
        $data = $this->request->data['Gift'];
        if ((!$this->permission_can_send_gift || !$this->permission_can_create_gift) && ($data['id'] == null ||
            ($data['id'] > 0 && !$this->Gift->isGiftExist($data['id'], null, MooCore::getInstance()->getViewer(true)))))
        {
            $this->_jsonError(__d('gift', 'You don\'t have permission to create gift'));
        }
        
        $isEdit = false;
        $data['user_id'] = MooCore::getInstance()->getViewer(true);
        if(!empty($data['filename']))
        {
            $data['extension'] = pathinfo($data['filename'], PATHINFO_EXTENSION);
        }
        //$data['thumb'] = !empty($data['filename']) ? $data['filename'] : '';
        if(!empty($data['type']))
        {
            switch($data['type'])
            {
                case GIFT_TYPE_PHOTO:
                    $data['price'] = Configure::read('Gift.gift_photo_price');
                    break;
                case GIFT_TYPE_AUDIO:
                    $data['price'] = Configure::read('Gift.gift_audio_price');
                    break;
                case GIFT_TYPE_VIDEO:
                    $data['price'] = Configure::read('Gift.gift_video_price');
                    break;
            }
        }
        if($data['id'] > 0)
        {
            $isEdit = true;
        }
        
        //save from public gift
        $public_gift = false;
        if($this->Gift->isGiftExist($data['id'], null, null, true))
        {
            $clonedata = $this->Gift->getGift($data['id']);
            $clonedata['Gift']['saved'] = $data['saved'];
            $clonedata['Gift']['friend_id'] = $data['friend_id'];
            $clonedata['Gift']['message'] = $data['message'];
            $clonedata['Gift']['clone_id'] = $clonedata['Gift']['id'];
            $clonedata['Gift']['c_type'] = $data['c_type'];
            $clonedata['Gift']['user_id'] = MooCore::getInstance()->getViewer(true);
            unset($clonedata['Gift']['id']);
            unset($clonedata['Gift']['is_public']);
            unset($clonedata['Gift']['enable']);
            unset($clonedata['Gift']['created']);
            unset($clonedata['Gift']['updated']);
            $data = $clonedata['Gift'];
            $public_gift = true;
        }

        //valid gift
        $this->Gift->set($data);
        $this->_validateData($this->Gift);
        
        //credit validation
        if($data['saved'] != 1 && $data['price'] > 0)
        {
            if(!$this->gift_integrate_credit || !Configure::read('Credit.credit_enabled'))
            {
                $this->_jsonError(__d('gift','Credit plugin is not enable. Can\'t send gift now, contact site admin for more details.'));
            }
            else if($this->gift_integrate_credit)
            {
                $balance = $this->Gift->getCurrentBalance();
                if($balance < $data["price"])
                {
                    $this->_jsonError(sprintf(__d('gift','Your current credit is %s. You do not have enough credits to send this gift.'), $balance));
                }
            }
        }
        $this->loadModel('User');
        $loggedin_user = $this->User->findById(MooCore::getInstance()->getViewer(true));
        $friend_user = $this->User->findById($data['friend_id']);

        if($data['c_type'] != 1){            
            $conditions = [
                'GiftSent.receiver_id'           =>  $data['friend_id'],
                'GiftSent.plugin'                =>  $data['plugin'],
                'GiftSent.extend'                =>  GIFT_EXTEND_FOREVER
            ];
            $n0 = $this->GiftSent->find('first',array('conditions' => $conditions));
            
            $conditions = [
                'GiftSent.receiver_id'           =>  $data['friend_id'],
                'GiftSent.sender_id'             =>  $loggedin_user['User']['id'],
                'GiftSent.sender_department'     =>  'sales',
                'GiftSent.gift_category_id'      =>  $data['gift_category_id']
            ];
            $n1 = $this->GiftSent->find('first',array('conditions' => $conditions));
            
            $conditions = [
                'GiftSent.receiver_id'  =>  $data['friend_id'],
                'GiftSent.plugin'       =>  $data['plugin']
            ];
            $n2 = $this->GiftSent->find('first',array('conditions' => $conditions));
            
            $return_url = $this->request->base.'/gifts/index/my/sent';
            if(!empty($n0)){
                // $message2 = 'User "'.ucfirst($friend_user['User']['name']).'" '.ucfirst($data['plugin']).' is "forever" already. Please select another Plugin item.';
                // $this->_jsonError(sprintf(__d('gift',$message2)),false);
                $message2 = 'User %s %s is "forever" already. Please select another Plugin item.';
                $this->_jsonError(sprintf(__d('gift',$message2,ucfirst($friend_user['User']['name']),ucfirst($data['plugin']))),false);
            }
            elseif(!empty($n1)){
                // $message2 = 'You already sent '.$data['plugin'].' to user "'.$friend_user['User']['name'].'". Sales people can send '.$data['plugin'].' "free of charge" to his customer. But, One plugin to One customer only.';
                // $this->_jsonError(sprintf(__d('gift',$message2)),false,['c_type'=>1,'button'=>'Use My Credit','url'=>$return_url]);
                $message2 = 'You already sent %s to user "%s". Sales people can send %s "free of charge" to his customer. But, One plugin to One customer only.';
                $this->_jsonError(sprintf(__d('gift',$message2,$data['plugin'],$friend_user['User']['name'],$data['plugin'])),false,['c_type'=>1,'button'=>__d('gift','Use My Credit'),'url'=>$return_url]);
            }
            elseif(!empty($n2)){
                $message2 = 'User "%s" already installed %s. If you send %s gift again, it will extend Valid period. Otherwise, you can cancel now, and, present another plugin.';
                $this->_jsonError(sprintf(__d('gift',$message2,$friend_user['User']['name'],ucfirst($data['plugin']),ucfirst($data['plugin']))),false,['c_type'=>1,'button'=>__d('gift','Extend'),'url'=>$return_url]);
            }
        }
        if($this->Gift->save($data))
        {
            $gift_id = $this->Gift->id;
            $return_url = $this->request->base.'/gifts/index/my/';
            
            //send gift
            if($data['saved'] != 1)
            {
                $this->GiftSent->set(array(
                    'sender_id'             => MooCore::getInstance()->getViewer(true),
                    'gift_id'               => $gift_id,
                    'receiver_id'           => $data['friend_id'],
                    'message'               => $data['message'],
                    'gift_category_id'      =>  $data['gift_category_id'],
                    'plugin'                =>  $data['plugin'],
                    'extend'                =>  $data['extend'],
                    'sender_department'     =>  $loggedin_user['User']['com_department'],
                    'receiver_job'          =>  $friend_user['User']['job_belong_to'],
                ));
                if($this->GiftSent->save())
                {
                    //save credits
                    if($data['price'] > 0)
                    {
                        $this->Gift->checkCredit($data['price'], $this->GiftSent->id);
                    }
                    
                    $return_url = $this->request->base.'/gifts/index/my/sent';
                
                    //notification
                    $gift = $this->Gift->findById($gift_id);
                    $this->Gift->sendNotification($data['friend_id'], MooCore::getInstance()->getViewer(true), 'gift_receive', '/gifts/index/my/received', $gift['Gift']['title']);

                    //update sent count
                    if($public_gift)
                    {
                        $this->Gift->increaseCounter($gift['Gift']['clone_id'], 'send_count');
                    }

                    $this->_jsonSuccess(__d('gift', 'Successfully sent'), __d('gift', 'Successfully sent'), array(
                        'url' => $return_url
                    ));
                }
                else 
                {
                    $this->Gift->delete($gift_id);
                    $this->_jsonError(__d('gift', 'Something went wrong, please try again'));
                }
            }
            else 
            {
                $this->_jsonSuccess(__d('gift', 'Successfully saved'), __d('gift', 'Successfully saved'), array(
                    'url' => $return_url
                ));
            }
        }
        $this->_jsonError(__d('gift', 'Something went wrong, please try again'));
    }
    
    public function preview()
    {
        $giftHelper = MooCore::getInstance()->getHelper('Gift_Gift');
        $data = $this->request->data['Gift'];
        $type = '';
        if(!empty($data['id']))
        {
            $gift = $this->Gift->getGift($data['id']);
            $type = $gift['Gift']['type'];
            $filename = $gift['Gift']['filename'];
            $extension = $gift['Gift']['extension'];
            if($gift['Gift']['type'] == 'photo'){
                $file_url = $giftHelper->getImage($gift['Gift']);
            }else{
                $file_url = $giftHelper->getFile($gift['Gift']);
            }

            $title = $gift['Gift']['title'];
            $message = $gift['Gift']['message'];
            $credits = $gift['Gift']['price'];
        }
        else
        {
            $type = $data['type'];
            $filename = $data['filename'];
            $extension = !empty($data['filename']) ? pathinfo($data['filename'], PATHINFO_EXTENSION) : '';
            if($data['type'] == 'photo'){
                $data['thumb'] = $data['filename'];
                $file_url = $giftHelper->getImage($data);
            }else{
                $file_url = $giftHelper->getFile($data);
            }
            $title = $data['title'];
            $message = $data['message'];
            $credits = "";
        }
        $this->set(array(
            'type' => $type,
            'filename' => $filename,
            'extension' => $extension,
            'file_url' => $file_url,
            'title' => $title,
            'message' => $message,
            'credits' => $credits
        ));
    }
    
    public function view($id)
    {
        if(!$this->Gift->isGiftExist($id, null, null, true))
        {
            $this->_redirectError(__d('gift', 'Gift not found'), '/gifts');
        }
        else
        {
            //increase view count
            $this->Gift->increaseCounter($id, 'view_count');
            
            $gift = $this->Gift->getGift($id);
            MooCore::getInstance()->setSubject($gift);
            
            $this->set(array(
                'gift' => $gift,
            ));
        }
    }
    
    public function ajax_view($gift_sent_id)
    {
        if(!$this->GiftSent->isSentGiftExist($gift_sent_id))
        {
            $this->set(array(
                'error' => __d('gift', 'Gift not found')
            ));
        }
        else
        {
            //update viewed
            $this->GiftSent->updateViewed($gift_sent_id);
            
            $gift = $this->GiftSent->getGift($gift_sent_id);
            
            $this->set(array(
                'gift' => $gift,
            ));
        }
    }
   
    public function ajax_delete_gift()
    {
        $data = $this->request->data;
        if((empty($data['gift_sent_id']) && empty($data['gift_id'])) ||
           (!empty($data['gift_sent_id']) && !$this->GiftSent->isSentGiftExist($data['gift_sent_id'])) ||
           (!empty($data['gift_id']) && !$this->Gift->isGiftExist($data['gift_id'], null, MooCore::getInstance()->getViewer(true))))
        {
            $this->_jsonError(__d('gift', 'Gift not found'));
        }
        else if(!empty($data['gift_id']) && $this->Gift->deleteGift($data['gift_id']))
        {
            $this->_jsonSuccess(__d('gift', 'Successfully deleted'));
        }
        else if(!empty($data['gift_sent_id']) && $this->GiftSent->deleteGiftSent($data['gift_sent_id']))
        {
            $this->_jsonSuccess(__d('gift', 'Successfully deleted'));
        }
        $this->_jsonError(__d('gift', 'Something went wrong, please try again'));
    }
   
    public function ajax_activate_gift()
    {
        $data = $this->request->data;
        if((empty($data['id'])) ||
           (!empty($data['id']) && !$this->GiftSent->isSentGiftExist($data['id'])))
        {
            return $this->_jsonError(__d('gift', 'Gift not found'));
        }else{
            $gift_sent = $this->GiftSent->getGift($data['id']);
            if(empty($gift_sent['GiftSent']['activated'])){
                $this->loadModel('User');
                $loggedin_user = $this->User->findById(MooCore::getInstance()->getViewer(true));
                date_default_timezone_set($loggedin_user['User']['timezone']);
                $gift_sent['GiftSent']['activated'] = date('Y-m-d H:i:s');
                $this->GiftSent->save($gift_sent);
                $message = '';
                $title = '';
                $this->loadModel('User');
                $attributes = $this->Gift->getAttribute($gift_sent['GiftSent']['plugin']);
                if($gift_sent['GiftSent']['extend'] != GIFT_EXTEND_FOREVER){
                    $loggedin_user['User'][$attributes['field']] = 1;
                    $valid_date = $loggedin_user['User'][$attributes['valid']];
                    if(empty($loggedin_user['User'][$attributes['valid']]) || date('Ymd',strtotime($loggedin_user['User'][$attributes['valid']])) < date('Ymd'))
                        $valid_date = date('Y-m-d');

                    // $valid_date = (!empty($loggedin_user['User'][$attributes['valid']])) ? $loggedin_user['User'][$attributes['valid']] : date('Y-m-d');
                    $date = date('Y-m-d',strtotime('+'.$gift_sent['GiftSent']['extend'].' Months',strtotime($valid_date)));

                    $loggedin_user['User'][$attributes['valid']] = $date;
                    // $message = $gift_sent['Gift']['plugin'].' is valid until '.$date.'.';
                    $message = __d('gift', '%s is valid until %s.',$gift_sent['Gift']['plugin'],$date);
                    $title = $gift_sent['Gift']['title'].' Activated';
                }else{
                    $loggedin_user['User'][$attributes['field']] = GIFT_EXTEND_FOREVER;
                    $message = __d('gift', '%s is valid forever.',$gift_sent['Gift']['plugin']);
                    $title = $gift_sent['Gift']['title'].' Activated';
                }
                if($this->User->save($loggedin_user))
                    $this->_jsonSuccess($message,false,['title'=>$title]);

            }else
                return $this->_jsonError(__d('gift', 'This gift is already used on %s',$gift_sent['GiftSent']['activated']));    
        }
        return $this->_jsonError(__d('gift', 'Something went wrong, please try again'));
    }
    
    public function ajax_send_gift_dialog($id)
    {
        if(!$this->Gift->isGiftExist($id, null, MooCore::getInstance()->getViewer(true)))
        {
            $this->_jsonError(__d('gift', 'Gift not found'));
        }
        else
        {
            $gift = $this->Gift->getGift($id);
            $this->set(array(
                'gift' => $gift
            ));
        }
    }
    
    public function ajax_send_gift()
    {
        $data = $this->request->data;
        if(!$this->Gift->isGiftExist($data['id'], null, MooCore::getInstance()->getViewer(true)))
        {
            $this->_jsonError(__d('gift', 'Gift not found'));
        }
        else
        {
            $gift = $this->Gift->getGift($data['id']);
            $this->GiftSent->set(array(
                'sender_id' => MooCore::getInstance()->getViewer(true),
                'receiver_id' => $gift['Gift']['friend_id'],
                'gift_id' => $gift['Gift']['id'],
                'message' => $gift['Gift']['message']
            ));
            $this->_validateData($this->GiftSent);
            // check setting credit
            $this->loadModel('Gift.GiftSetting');

            //credit validation
            if($gift['Gift']['price'] > 0)
            {
                if(!$this->gift_integrate_credit || !Configure::read('Credit.credit_enabled'))
                {
                    $this->_jsonError(__d('gift','Credit plugin is not enable. Can\'t send gift now, contact site admin for more details.'));
                }
                else if($this->gift_integrate_credit)
                {
                    $balance = $this->Gift->getCurrentBalance();
                    if($balance < $gift['Gift']['price'])
                    {
                        $this->_jsonError(sprintf(__d('gift','Your current credit is %s. You do not have enough credits to send this gift.'), $balance));
                    }
                }
            }
            
            if($this->GiftSent->save())
            {
                $this->Gift->id = $data['id'];
                $this->Gift->save(array(
                    'saved' => 0
                ));
                
                //save credits
                if($gift['Gift']['price'] > 0)
                {
                    $this->Gift->checkCredit($gift['Gift']['price'], $this->GiftSent->id);
                }
                
                //update sent count
                if($gift['Gift']['clone_id'] > 0)
                {
                    $this->Gift->increaseCounter($gift['Gift']['clone_id'], 'send_count');
                }
                
                //notification
                $this->Gift->sendNotification($gift['Gift']['friend_id'], MooCore::getInstance()->getViewer(true), 'gift_receive', '/gifts/index/my/received', $gift['Gift']['title']);
                
                $this->_jsonSuccess(__d('gift', 'Gift has been sent'));
            }
            $this->_jsonError(__d('gift', 'Something went wrong, please try again'));
        }
    }
    
    public function suggest_friend()
    {
        $friends = $this->Gift->getSuggestFriendList($this->request->data['keyword']);
        echo json_encode($friends);
        exit;
    }
    
    public function upload_file($type)
    {
        $this->autoRender = false;
        // save this picture to album
        switch($type)
        {
            case GIFT_TYPE_AUDIO:
                $allowedExtensions = explode(',', GIFT_EXT_AUDIO);
                break;
            case GIFT_TYPE_VIDEO:
                if(($checkFfmpeg = $this->Gift->checkFfmpeg(Configure::read('Gift.gift_path_to_ffmpeg'))) !== true)
                {
                    echo htmlspecialchars(json_encode(array(
                        'success' => false,
                        'message' => $checkFfmpeg
                    )), ENT_NOQUOTES);
                    exit;
                }
                $allowedExtensions = explode(',', GIFT_EXT_VIDEO);
                break;
            default :
                $allowedExtensions = explode(',', GIFT_EXT_PHOTO);
        }
        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $result = $uploader->handleUpload(GIFT_FILE_PATH);

        if (!empty($result['success'])) 
        {
            //resize image or convert video
            switch($type)
            {
                case GIFT_TYPE_VIDEO: //convert video
                    $result['filename'] = $this->Gift->convertVideo($result['filename']);
                    break;
                case GIFT_TYPE_PHOTO: //resize image
                    App::import('Vendor', 'phpThumb', array('file' => 'phpThumb/ThumbLib.inc.php'));
                    $photo = PhpThumbFactory::create(GIFT_FILE_URL.$result['filename']);
                    $photo->adaptiveResize(GIFT_THUMB_WIDTH, GIFT_THUMB_HEIGHT)->save(GIFT_THUMB_URL.$result['filename']);
                    $result['thumb'] = $this->request->base.'/'.GIFT_THUMB_URL.$result['filename'];
            }
            
            $result['url'] = GIFT_FILE_URL;
            $result['path'] = $this->request->base.'/'.GIFT_FILE_URL.$result['filename'];
            $result['file'] = GIFT_FILE_PATH . DS . $result['filename'];
        }
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        exit;
    }
}