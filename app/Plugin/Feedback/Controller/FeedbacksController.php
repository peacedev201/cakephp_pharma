<?php 
class FeedbacksController extends FeedbackAppController{    

    public $components = array('Feedback.Core');
    
    public function __construct($request = null, $response = null) 
    {
        parent::__construct($request, $response);
        $this->loadModel('Feedback.Feedback');
        $this->loadModel('Feedback.FeedbackImage');
        $this->loadModel('Feedback.FeedbackStatus');
        $this->loadModel('Feedback.FeedbackCategory');
        $this->loadModel('Feedback.FeedbackSeverity');

        $this->url_feedbacks = '/feedbacks';
        $this->set('url_feedbacks', '/feedbacks');

        $this->url_ajax_add_status = '/ajax_add_status';
        $this->set('url_ajax_add_status', '/ajax_add_status');

        $this->url_ajax_save_status = '/ajax_save_status';
        $this->set('url_ajax_save_status', '/ajax_save_status');             
       
    }
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->set('is234', $this->Core->is234());        
    }

    public function admin_index(){

        $this->_checkPermission(array('super_admin' => 1));
        $keyword = isset($this->request->query['keyword']) ? $this->request->query['keyword'] : null;
        $cond = array();
        if($keyword != '')
        {
            $keyword = str_replace("'", "\'", $keyword);
            $cond[] = "Feedback.title LIKE '%$keyword%'";
        }
        $this->Paginator->settings = array(
            'conditions' => $cond,
            'limit' => 15,
            'order' => array(
                'created' => 'DESC', 
                )
        );
        $this->Feedback->recursive = 2;

        $aFeedbacks = $this->Paginator->paginate('Feedback'); 
        $aFeedbacks =$this->Feedback->parseDataLanguage($aFeedbacks);
        $this->set('aFeedbacks', $aFeedbacks);
        $this->set('keyword', $keyword);
        $this->set('title_for_layout', __d('feedback', 'Feedback'));
        $this->set('permission_approve_feedback', $this->checkFeedbackPermission('feedback_approve_feedback'));
    }

    public function index( $type = null , $param = null){
        if(!$this->checkFeedbackPermission('feedback_view_feedback_listing'))
        {
            $this->Session->setFlash(__d('feedback', 'You dont\'t have permission.'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in'));
            $this->redirect('/home');
        }
        $uid = MooCore::getInstance()->getViewer(true);
        $this->set('uid', $uid);
        $this->set('type', $type);

        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $url = ( !empty( $param ) ) ? $type . '/' . $param : $type;

        $this->_checkBlock();

        //$cond['Feedback.privacy'] = 1;
        $cond = array();
        switch ( $type )
        {
            case 'my':
                unset($cond);
                $cond['Feedback.user_id'] = $uid;  
                break;
            case 'cat':
                $cond = urldecode( $param );
                break;
            case 'sta':
                $cond = urldecode( $param );
                break;
        }   
        
        $this->Feedback->recursive = 2;
       
        $aFeedbacks = $this->Feedback->getFeedbacks( $type, $cond, 1,RESULTS_LIMIT );

        // $aFeedbacks = $this->Feedback->find('all', array(
        //                     'conditions' => array($cond), 
        //                     'order'      => array('Feedback.created DESC'),
        //                    )); 
        
        //menu categories
        $aCategories = $this->FeedbackCategory->find('all', array(
            'conditions' => array('is_active' => 1)
        ));
       
        //menu statustes
        $aStatuses = $this->FeedbackStatus->find('all', array(
            'conditions' => array('is_active' => 1)
        ));
        
        $more_feedbacks = $this->Feedback->getFeedbacks( $type, $param, $page + 1);
        $more_result = 0;
        if (!empty($more_feedbacks)) $more_result = 1;

        $this->set('aFeedbacks', $aFeedbacks);  
        $this->set('more_url', $this->url_feedback. '/ajax_browse/' . h($url) . '/page:' . ( $page + 1 ) );
        $this->set('more_result', $more_result);

        $this->loadModel( 'Tag' );
        $tags = $this->Tag->getTags('Feedback_Feedback', Configure::read('core.popular_interval'));
        $this->set('tags', $tags);
        $this->set('aStatuses', $aStatuses);
        $this->set('aCategories', $aCategories);
        $this->set('param', $param);
        $this->set('title_for_layout', __d('feedback', 'Feedback'));
        $this->set('permission_create_feedback', $this->checkFeedbackPermission('feedback_create_feedback'));
    }

    public function _checkBlock(){

        $uid = MooCore::getInstance()->getViewer(true);
        $uip = $this->request->clientIp();

        $this->loadModel('Feedback.FeedbackBlockuser');
        $this->loadModel('Feedback.FeedbackBlockip');

        $aBlockuser = $this->FeedbackBlockuser->findById( $uid );
        $aBlockip = $this->FeedbackBlockip->find( 'first', array(
                                            'conditions' => array('blockip_address' => $uip)
                                        ) );

        $bBlockFeeback = false;
        $bBlockComment = false;

        if($aBlockuser){
            if($aBlockuser['FeedbackBlockuser']['block_feedback']){
                $bBlockFeeback = true;
            }
            if($aBlockuser['FeedbackBlockuser']['block_comment']){              
                $bBlockComment = true;
            }           
        }

        if($aBlockip){
            if($aBlockip['FeedbackBlockip']['blockip_feedback']){
                $bBlockFeeback = true;
            }
            if($aBlockip['FeedbackBlockip']['blockip_comment']){                
                $bBlockComment = true;
            }           
        }

        $this->set('bBlockFeeback', $bBlockFeeback);
        $this->set('bBlockComment', $bBlockComment);

    }

    public function ajax_browse( $type = null, $param = null )
    {
        if(!$this->checkFeedbackPermission('feedback_view_feedback_listing'))
        {
            echo __d('feedback', 'You dont\'t have permission.');
            exit;
        }
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $url = ( !empty( $param ) ) ? $type . '/' . $param : $type;
        $uid = MooCore::getInstance()->getViewer(true); 
        $this->set('uid', $uid);
        
        switch ( $type )
        {
            case 'my':
                $this->set('user_feedback', true);
                $param = $uid;
                break;
                
            case 'friends':
                $param = $uid;
                break;

            case 'cat':
                $param = urldecode( $param );
                break;

            case 'sta':
                $param = urldecode( $param );
                break;

            case 'search':
                $param = urldecode( $param );
                
                if ( !Configure::read('core.guest_search') && empty( $uid ) )
                    $this->_checkPermission();              
                
                break;
        }   
        
        $this->Feedback->recursive = 2;
        $aFeedbacks = $this->Feedback->getFeedbacks( $type, $param, $page );
        $more_feedbacks = $this->Feedback->getFeedbacks( $type, $param, $page + 1);
        $more_result = 0;
        if (!empty($more_feedbacks)) $more_result = 1;

        $this->set('aFeedbacks', $aFeedbacks);  
        $this->set('more_url', $this->url_feedback. '/ajax_browse/' . h($url) . '/page:' . ( $page + 1 ) );
        $this->set('more_result', $more_result);
        if ($this->isApp())
        {
            $this->set('type',$type);
            $this->set('page', $page);
            $this->set('permission_create_feedback', $this->checkFeedbackPermission('feedback_create_feedback'));
            $this->_checkBlock();
            $this->render('Feedbacks/browse');
        }
        else
        {
            $this->render('Elements/lists/feedbacks_list');
        }
    }

    public function view( $iId ){

        $cUser = $this->_getUser();
        $uid = MooCore::getInstance()->getViewer(true);
        $this->set('uid', $uid);
        $data = array () ;

        if($cUser != null && !$cUser['Role']['is_admin'] && !$this->Feedback->isFeedbackExist($iId) ){
            $this->Session->setFlash(__d('feedback', 'This feedback does not exist'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in' ));
            $this->redirect('/feedbacks');
        }
                
        $this->_checkBlock();

        $this->Feedback->recursive = 2;
        $aFeedback = $this->Feedback->findById($iId);
        $this->_checkExistence($aFeedback);
        $this->_checkPermission( array('user_block' => $aFeedback['Feedback']['user_id']) );
        $this->Feedback->updateFeedbackView($iId);
       
        if($cUser != null && !$cUser['Role']['is_admin'] && $aFeedback['Feedback']['user_id'] != $uid){
            if($aFeedback['Feedback']['approved'] != 1){
                $this->redirect( '/feedbacks/feedbacks' );
            }
            
            $canView = false;
            switch ( $aFeedback['Feedback']['privacy'] )
            {
                case PRIVACY_EVERYONE:
                    $canView = true;
                    break;
                        
                case PRIVACY_FRIENDS:  
                    $this->loadModel('Friend'); 
                    $areFriends = $this->Friend->areFriends( $uid, $aFeedback['Feedback']['user_id'] );
                                 
                    if ( $areFriends )
                        $canView = true;
                    
                    break;
                    
                case PRIVACY_ME:
                    if ( $uid == $aFeedback['Feedback']['user_id'] )
                        $canView = true;
                        
                    break;
            }      
            if(!$canView)
            $this->redirect( '/feedbacks/feedbacks' );
        }

        $areFriends = false;
        if ( !empty($uid) ) //  check if user is a friend
        {
            $this->loadModel( 'Friend' );
            $this->loadModel( 'FriendRequest' );
            $areFriends = $this->Friend->areFriends( $uid, $aFeedback['User']['id'] );
            
            $friends = $this->Friend->getFriends($uid);
            $requests = $this->FriendRequest->getRequestsList($uid);

            $friends_requests = array_merge($friends, $requests);

            $this->set('friends', $friends);

            $this->set('friends_request', $requests);
            
        }      
        // $this->loadModel( 'Photo' );
        // $photos = $this->Photo->getPhotos( 'feedback', $iId );

        //like
        $this->loadModel('Like');
        $likes = $this->Like->getLikes($aFeedback['Feedback']['id'], 'Feedback_Feedback');
        $this->set('likes', $likes);

        MooCore::getInstance()->setSubject($aFeedback);
        $this->loadModel( 'Tag' );
        $tags = $this->Tag->getContentTags( $iId, 'Feedback_Feedback' );
        $this->set('tags', $tags);  
        $aFeedback =$this->Feedback->parseResultLanguage($aFeedback);
        $this->set('aFeedback', $aFeedback);
        $this->set('areFriends', $areFriends);
        
        $page = 1;
        $data['bIsCommentloadMore'] = $aFeedback['Feedback']['comment_count'] - $page*RESULTS_LIMIT ;
        $data['more_comments'] = '/comments/browse/topic/' . $aFeedback['Feedback']['id'] . '/page:' . ($page + 1) ;
        $this->set('cUser', $cUser);
        $this->set('data', $data);
        $this->set('permission_edit_own_feedback', $this->checkFeedbackPermission('feedback_edit_own_feedback', $aFeedback['Feedback']['user_id']));
        $this->set('permission_delete_own_feedback', $this->checkFeedbackPermission('feedback_delete_own_feedback', $aFeedback['Feedback']['user_id']));
        $this->set('permission_edit_all_feedbacks', $this->checkFeedbackPermission('feedback_edit_all_feedbacks'));
        $this->set('permission_delete_all_feedbacks', $this->checkFeedbackPermission('feedback_delete_all_feedbacks'));
        $this->set('permission_set_status', $this->checkFeedbackPermission('feedback_set_status'));
		$this->set('title_for_layout', htmlspecialchars($aFeedback['Feedback']['title']));
        $this->set('description_for_layout', CakeText::truncate(strip_tags($aFeedback['Feedback']['body']), 160, array('ellipsis' => '...', 'html' => false, 'exact' => false)));
    }

    public function ajax_create( $iId = null ){

        //$this->_checkPermission( array( 'confirm' => true ) );
        $this->_checkBlock();
        $noPermission = false;
        $this->Feedback->recursive = 2;
        $aFeedback = $this->Feedback->findById($iId);    
        if($aFeedback != null){
            if(!$this->checkFeedbackPermission('feedback_edit_own_feedback', $aFeedback['Feedback']['user_id']) &&
               !$this->checkFeedbackPermission('feedback_edit_all_feedbacks'))
            {
                $noPermission = true;
            }
            //$this->_checkExistence($aFeedback);
        }else{
            $aFeedback = $this->Feedback->initFields();
        }
        
        //categories
        $aCategories = $this->FeedbackCategory->find('list', array('conditions' => array('is_active' => 1)));
        $this->set('aCategories', $aCategories);        

        //severtity
        $aSeverities = $this->FeedbackSeverity->find('list', array('conditions' => array('is_active' => 1)));
        $this->set('aSeverities', $aSeverities);  

        //most vote feedbacks
        $aFeedbacks = $this->Feedback->find('all', array(
                            'conditions' => array('Feedback.privacy' => 1, 'Feedback.approved' => 1),
                            'order'      => array('total_votes DESC'),
                            'limit'      => 5,
            )); 
        $aFeedbacks = $this->Feedback->parseDataLanguage($aFeedbacks);
        $this->request->data = $aFeedback;
        $this->set('isLoggedin', MooCore::getInstance()->getViewer(true) > 0 ? true :false);
        $this->set('aFeedbacks', $aFeedbacks);
        $this->set('noPermission', $noPermission);
        $this->set('aFeedback', $aFeedback);
        $this->set('permission_can_upload_photo', $this->checkFeedbackPermission('feedback_can_upload_photo'));
        $this->set('tags', $this->Feedback->getListTag($iId));
        $this->loadModel('Plugin');
        $plugin_info = $this->Plugin->findByKey('Feedback',array('name','id'));
        $this->set('plugin_feedback_id', $plugin_info['Plugin']['id']);
    }
    
    public function ajax_thanks()
    {
        $this->set(array(
            'feedback_id' => $this->request->data['id'],
            'approved' => (bool)$this->request->data['approved']
        ));
    }

    public function edit( $iId = null ){

        $this->_checkPermission( array( 'confirm' => true ) );
        $this->_checkBlock();
        // $this->_checkPermission( array('aco' => 'feedback_create') );

        $aCategories = $this->FeedbackCategory->find('list', array('conditions' => array('is_active' => 1)));
        $this->set('aCategories', $aCategories);        

        $aSeverities = $this->FeedbackSeverity->find('list', array('conditions' => array('is_active' => 1)));
        $this->set('aSeverities', $aSeverities);

        $this->loadModel('Tag');
        $tags = $this->Tag->getContentTags( $iId, 'Feedback_Feedback' );
        $this->set('tags', $tags);  

        $aFeedbacks = $this->Feedback->find('all', array(
                            'conditions' => array('Feedback.privacy' => 2),
                            'order'      => array('total_votes DESC'),
                            'limit'      => 5,
            ));      
        $this->set('aFeedbacks', $aFeedbacks);

        if( $iId ){
            $this->Feedback->recursive = 2;
            $aFeedback = $this->Feedback->findById($iId);
            $this->set('aFeedback', $aFeedback);
            $this->_checkExistence($aFeedback);
        }else{
            $aFeedback = $this->Feedback->initFields();
        }
        
        $this->request->data = $aFeedback;

    }

    public function ajax_save(){

        //$this->_checkPermission( array( 'confirm' => true ) );                      
        $this->autoRender = false;
        $cUser = $this->_getUser();
        $isEdit = false;
        $oCategoryId = '';
        if(!$this->checkFeedbackPermission('feedback_create_feedback'))
        {
            $this->_jsonError(__d('feedback', 'You dont\'t have permission.'));
            exit;
        }
        else if(empty($this->request->data['Feedback']['id']) && $this->Feedback->isReachToLimitPost($this->request->clientIp()))
        {
            $this->_jsonError(__d('feedback', 'You have reached limit for posting feedback'));
            exit;
        }
        else if(empty($this->request->data['Feedback']['id']) && ($timer = $this->Feedback->isReachToPostFrequency($this->request->clientIp())) != false)
        {
            $this->_jsonError(sprintf(__d('feedback', 'Please wait %s for next post'), $timer));
            exit;
        }
        else if(!empty($this->request->data['Feedback']['id']))
        {
            $isEdit = true;
            $oFeedback = $this->Feedback->findById($this->request->data['Feedback']['id']);
            $oCategoryId = $oFeedback['Feedback']['category_id'];
            $this->Feedback->id = $this->request->data['Feedback']['id'];
        }
        if($this->checkFeedbackPermission('feedback_approve_feedback_before_public'))
        {
            $this->request->data['Feedback']['approved'] = 1;
        }
        if(empty($this->request->data['Feedback']['id']))
        {
            $this->request->data['Feedback']['user_id'] = MooCore::getInstance()->getViewer(true);
            $this->request->data['Feedback']['ip_address'] = $this->request->clientIp();
        }

        $this->Feedback->set($this->request->data);
        $this->_validateData($this->Feedback);
        
        // check captcha
        if (Configure::read('Feedback.feedback_enable_captcha') && MooCore::getInstance()->isRecaptchaEnabled())
        {
            $recaptcha_privatekey = Configure::read('core.recaptcha_privatekey');
            App::import('Vendor', 'recaptchalib');
            $reCaptcha = new ReCaptcha($recaptcha_privatekey);
           
            try 
            {
                $resp = $reCaptcha->verifyResponse(
                    $_SERVER["REMOTE_ADDR"], $this->request->data["g-recaptcha-response"]
                );

                if ($resp != null && $resp->errorCodes != null) 
                {
                    $this->_jsonError(__d('feedback', 'Invalid security code'));
                    exit;
                }
            } 
            catch (Exception $ex) {}
        }
        
        if($this->Feedback->save())
        {
            $aFeedback = $this->Feedback->findById($this->Feedback->id);
            $iId = $aFeedback['Feedback']['id'];

            if($iCategoryId = $this->request->data['Feedback']['category_id']){
                if ($oCategoryId)
                {
                    $oCategory = $this->FeedbackCategory->findById($oCategoryId);
                    $this->FeedbackCategory->id = $oCategoryId;                 
                    $this->FeedbackCategory->save(array('use_time' => $this->Feedback->totalApproveFeedback($oCategoryId)));
                }
                $aCategory = $this->FeedbackCategory->findById($iCategoryId);
                $this->FeedbackCategory->id = $iCategoryId;                 
                $this->FeedbackCategory->save(array('use_time' => $this->Feedback->totalApproveFeedback($iCategoryId)));
            }

            if(!empty($this->request->data['Feedback']['severity_id']) &&
               $iSeverityId = $this->request->data['Feedback']['severity_id']){
                $aSeverity = $this->FeedbackSeverity->findById($iSeverityId);
                $this->FeedbackSeverity->id = $iSeverityId;                 
                $this->FeedbackSeverity->save(array('use_time' => $this->Feedback->totalApproveFeedback(null, $iSeverityId)));
            }

            //tags
            $this->Feedback->deleteFeedbackTag($iId);
            $this->loadModel( 'Tag' );
            $this->Tag->saveTags( $this->request->data['Feedback']['tags'], $this->Feedback->id, 'Feedback_Feedback' );

            //save image
            if($this->checkFeedbackPermission('feedback_can_upload_photo'))
            {
                $this->FeedbackImage->clearImageByFeedbackId($this->Feedback->id);
                if(!empty($this->request->data['Feedback']['attachments']))
                {
                    $this->FeedbackImage->updateImageFeedbackId($this->Feedback->id, $this->request->data['Feedback']['attachments']);
                }
            }
            
            //send email
            if(!$aFeedback['Feedback']['approved'])
            {
                if(($emails = $this->Feedback->getEmailReceive()) != null)
                {
                    foreach($emails as $email)
                    {
                        $this->MooMail->send($email, 'feedback_create', array(  
                            'username' => !empty($cUser) ? $cUser['name'] : $aFeedback['Feedback']['fullname'],
                            'link' => Router::url('/', true ).'admin/feedback/feedbacks'
                        ));
                    }
                }
            }else{
                if(!$isEdit && Configure::read('Feedback.feedback_enable_activity')){
                    $this->addActivityFeed($aFeedback);
                }else{
                    $this->loadModel('Activity');
                    $this->Activity->updateAll(array('privacy'=>$aFeedback['Feedback']['privacy']),array('action'=>'feedback_create','item_type'=>'Feedback_Feedback','item_id'=>$aFeedback['Feedback']['id']));
                }
            }
            
            $response['result'] = 1;
            $response['id'] = $iId;
            $response['approved'] = $aFeedback['Feedback']['approved'] == false ? 0 : 1;
            if ($this->isApp())
            {
                if (!$isEdit)
                {
                    $this->Session->setFlash(__d('feedback','Your Feedback was successfully posted. Please wait for publication approval from Admin'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
                }
                else
                {
                    $this->Session->setFlash(__d('feedback','Feedback has been successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
                }
            }
            if($isEdit || $this->isApp())
            {
                $response['url'] = $aFeedback['Feedback']['moo_href'];
            }

            echo json_encode($response);
            exit;
        }
        $this->_jsonError(__d('feedback', 'Something went wrong, please try again.'));
        exit;
    }

    public function admin_delete($iId = null){
        
        $aDeletes = $this->request->data;
        $aFeedback = $this->Feedback->findById($iId);
        if(empty($aDeletes) && empty($aFeedback)){
            $this->Session->setFlash(__d('feedback', 'Please select Feedback to delete!'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));
            $this->redirect( $this->referer() );
        }

        if($iId){           
            $aDeletes['feedbacks'][] = $iId;
        }

        foreach ($aDeletes['feedbacks'] as $iFeedback_id) {         
            $aDeletedFeedback = $this->Feedback->findById($iFeedback_id);
            $this->Feedback->deleteFeedback($iFeedback_id);       
            
            if(!empty($aDeletedFeedback['Feedback']['category_id']) && $iCategoryId = $aDeletedFeedback['Feedback']['category_id']){
                $this->FeedbackCategory->id = $iCategoryId;                 
                $this->FeedbackCategory->save(array('use_time' => $this->Feedback->totalApproveFeedback($iCategoryId)));
            }

            
            if(!empty($aDeletedFeedback['Feedback']['severity_id']) && $iSeverityId = $aDeletedFeedback['Feedback']['severity_id']){
                $this->FeedbackSeverity->id = $iSeverityId;                 
                $this->FeedbackSeverity->save(array('use_time' => $this->Feedback->totalApproveFeedback(null, $iSeverityId)));
            }
        }
        $this->FeedbackStatus->updateStatusCount();
        $this->Session->setFlash(__d('feedback', 'Successfully deleted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));
        
        $this->redirect( '/admin/feedback/feedbacks' );

    }

    public function delete($iId = null){
        $aDeletes = $this->request->data;
        $aFeedback = $this->Feedback->findById($iId);
        $this->_checkExistence($aFeedback);
        
         if(!$this->checkFeedbackPermission('feedback_delete_own_feedback', $aFeedback['Feedback']['user_id']) &&
            !$this->checkFeedbackPermission('feedback_delete_all_feedbacks'))
        {
            $this->Session->setFlash(__d('feedback', 'You dont\'t have permission to delete this feedback.'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in' ));
            $this->redirect( $this->referer() );
        }
        
        if(empty($aDeletes) && empty($aFeedback)){
            $this->Session->setFlash(__d('feedback', 'Please select Feedback to delete!'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));
                $this->redirect( $this->referer() );
        }
        
        if(!$this->checkFeedbackPermission('feedback_delete_own_feedback', $aFeedback['Feedback']['user_id']) &&
            !$this->checkFeedbackPermission('feedback_delete_all_feedbacks'))
        {
            $this->Session->setFlash(__d('feedback', 'You dont\'t have permission to delete this feedback.'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in' ));
            $this->redirect( $this->referer() );
        }
        

        if($iId){           
            $aDeletes['feedbacks'][] = $iId;
        }

        foreach ($aDeletes['feedbacks'] as $iFeedback_id) {   
            $aDeletedFeedback = $this->Feedback->findById($iFeedback_id);
                        
            $this->Feedback->deleteFeedback($iFeedback_id); 
            
            if(!empty($aDeletedFeedback['Feedback']['category_id']) && $iCategoryId = $aDeletedFeedback['Feedback']['category_id']){
                $this->FeedbackCategory->id = $iCategoryId;                 
                $this->FeedbackCategory->save(array('use_time' => $this->Feedback->totalApproveFeedback($iCategoryId)));
            }

            
            if(!empty($aDeletedFeedback['Feedback']['severity_id']) && $iSeverityId = $aDeletedFeedback['Feedback']['severity_id']){
                $this->FeedbackSeverity->id = $iSeverityId;                 
                $this->FeedbackSeverity->save(array('use_time' => $this->Feedback->totalApproveFeedback(null, $iSeverityId)));
            }
        }
        
        $this->FeedbackStatus->updateStatusCount();
        $this->Session->setFlash(__d('feedback', 'Feedback has been deleted'));
        if (!$this->isApp())
    	{
    		$this->redirect( '/feedbacks' );
    	}

    }

    public function admin_do_active($iId, $style, $is_active = null )
    {
        $this->active($iId, $style, $is_active);
    }
    
    public function do_active($iId, $style, $is_active = null)
    {
        $cUser = $this->_getUser();
        if($cUser != null && $cUser['Role']['is_admin'])
        {
            $this->active($iId, $style, $is_active);
        }
        else
        {
            $this->Session->setFlash(__d('feedback', 'You don\'t have permission to do this'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in' ));
            $this->redirect($this->url_feedbacks);
        }
    }
    
    private function active($iId, $style, $is_active = null)
    {
        $cUser = $this->_getUser();
        if( !$this->Feedback->isIdExist($iId) )
        {
            $this->Session->setFlash(__d('feedback', 'This category does not exist'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in' ));
        }
        else
        {
            $this->Feedback->id = $iId;
            if($this->Feedback->save(array($style => $is_active)))
            {
                $feedback = $this->Feedback->findById($iId);
                if($style == 'approved' && $is_active == 1)
                {
                    if($feedback != null)
                    {
                        $email = !empty($feedback['User']['email']) ? $feedback['User']['email'] : $feedback['Feedback']['email'];
                        $this->MooMail->send($email, 'feedback_approve', array(  
                            'username' => !empty($cUser) ? $cUser['name'] : $feedback['Feedback']['fullname'],
                            'link' => Router::url('/', true ).$feedback['Feedback']['moo_url']
                        ));
                        if (Configure::read('Feedback.feedback_enable_activity'))
                        {
                            $this->addActivityFeed($feedback);
                        } 
                    }                  
                }
                //update counter
                if($style == 'approved')
                {
                    if($feedback['Feedback']['category_id'] > 0)
                    {
                        $this->FeedbackCategory->id = $feedback['Feedback']['category_id'];                 
                        $this->FeedbackCategory->save(array('use_time' => $this->Feedback->totalApproveFeedback($feedback['Feedback']['category_id'])));
                    }
                    if($feedback['Feedback']['severity_id'] > 0)
                    {
                        $this->FeedbackSeverity->id = $feedback['Feedback']['severity_id'];                 
                        $this->FeedbackSeverity->save(array('use_time' => $this->Feedback->totalApproveFeedback(null, $feedback['Feedback']['severity_id'])));
                    }
                    if(empty($is_active)){
                        $activityModel = MooCore::getInstance()->getModel('Activity');
                        // delete activity
                       $parentActivity = $activityModel->find('list', array('fields' => array('Activity.id') , 'conditions' =>
                           array('Activity.item_type' => 'Feedback_Feedback', 'Activity.item_id' => $iId)));

                       $activityModel->deleteAll( array( 'Activity.item_type' => 'Feedback_Feedback', 'Activity.item_id' => $iId ), true, true );
                       $activityModel->deleteAll( array( 'Activity.target_id' => $iId, 'Activity.type' => 'Feedback' ), true, true );

                       // delete child activity
                       $activityModel->deleteAll(array('Activity.item_type' => 'Feedback_Feedback', 'Activity.parent_id' => $parentActivity)); 
                    }
                }
                
                //update status counter
                $this->FeedbackStatus->updateStatusCount();
                $this->Session->setFlash(__d('feedback', 'Successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));          
            }
        }
      
        if($this->isApp() && isset($feedback)){
            $this->redirect( '/feedbacks/view/' . $iId . '/' . seoUrl($feedback['Feedback']['title']) );
        }else{
            $this->redirect( $this->referer() );
        }
    }

    public function admin_ajax_add_status( $iId ){

        $aStatuses = $this->FeedbackStatus->find('list', array(
            'conditions' => array('FeedbackStatus.is_active' => 1)
        ));

        $aFeedback = $this->Feedback->findById( $iId );
        if($aFeedback['Feedback']['status_id']){
            $this->set('iDefault_id', $aFeedback['Feedback']['status_id']);
        }

        $this->set('aStatuses', $aStatuses);
        $this->set('aFeedback', $aFeedback);
        $this->set('iFeedback_id', $iId);
        $this->set('permission_set_status', $this->checkFeedbackPermission('feedback_set_status'));
    }

    public function ajax_add_status( $iId ){

        $this->admin_ajax_add_status($iId);
    }

    public function admin_ajax_save_status(){

        if(!$this->checkFeedbackPermission('feedback_set_status'))
        {
            $this->_jsonError(__d('feedback', 'You dont\'t have permission.'));
            exit;
        }
        $this->autoRender = false;

        $aData = $this->request->data['AddStatus'];

        $this->Feedback->id = $aData['iFeedback_id'];
        $cUser = $this->_getUser();
        if(!empty($aData['status_id']))
        {
            $old_feedback = $this->Feedback->findById($aData['iFeedback_id']);
            if($this->Feedback->save(array('status_id' => $aData['status_id'], 'status_body' => $aData['status_body'])))
            {
                $feedback = $this->Feedback->findById($aData['iFeedback_id']);
                $this->FeedbackStatus->updateStatusCount();
                if($feedback != null && $aData['status_id'] != null && $old_feedback['Feedback']['status_id'] != $aData['status_id'])
                {
                    $email = !empty($feedback['User']['email']) ? $feedback['User']['email'] : $feedback['Feedback']['email'];
                    $this->MooMail->send($email, 'feedback_change_status', array(  
                        'username' => $cUser['name'],
                        'message' => $aData['status_body'],
                        'status' => $feedback['FeedbackStatus']['name'],
                        'link' => Router::url('/', true ).$feedback['Feedback']['moo_url']
                    ));
                }
            }
        }
        $this->Session->setFlash(__d('feedback', 'Successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));  

        $response['result'] = 1;
        
        echo json_encode($response);
    }

    public function ajax_save_status(){

        $this->admin_ajax_save_status();

    }

    public function admin_ajax_edit($iId = null){

        $this->_checkPermission( array( 'confirm' => true ) );

        $aCategories = $this->FeedbackCategory->find('list', array('conditions' => array('is_active' => 1)));
        $this->set('aCategories', $aCategories);

        $aSeverities = $this->FeedbackSeverity->find('list', array('conditions' => array('is_active' => 1)));
        $this->set('aSeverities', $aSeverities);

        if( $iId ){
            $aFeedback = $this->Feedback->findById($iId);
            //$this->_checkExistence($aFeedback);
        }else{
            $aFeedback = $this->Feedback->initFields();
        }

        $this->request->data = $aFeedback;
        $this->set('tags', $this->Feedback->getListTag($iId));
    }

    public function admin_ajax_save(){

        $this->_checkPermission( array( 'confirm' => true ) );
        $this->autoRender = false;

        $iId = $this->request->data['Feedback']['id'];

        if( !empty($iId) ){  //edit
            $this->Feedback->id = $iId;

            if($this->Feedback->save($this->request->data)){
                $this->Feedback->deleteFeedbackTag($iId);
                $this->loadModel( 'Tag' );
                $this->Tag->saveTags( $this->request->data['Feedback']['tags'], $this->Feedback->id, 'Feedback_Feedback' );
                $this->Session->setFlash(__d('feedback', 'Successfully updated'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));
            }
        }

        $this->Feedback->set($this->request->data);
        $this->_validateData($this->Feedback);

        $response['result'] = 1;
        $response['id'] = $iId;

        echo json_encode($response);

    }

    public function mostview_feedback(){

        if ($this->request->is('requested')) {
            $num_mostview = $this->request->named['num_mostview'];
            $this->loadModel('User');
            $this->Feedback->recursive = 2;
            $aMostVoteFeedbacks = $this->Feedback->find('all', array(
                                                            'conditions' => array('Feedback.privacy' => 1),
                                                            'order'      => array('Feedback.total_votes DESC'),
                                                            'limit'      => $num_mostview,   
                                                            ));
            
            return $aMostVoteFeedbacks;
        }
    }

    public function add_image( $iId ){

        $this->set('iId', $iId);

    }

    public function upload()
    {
        $this->autoRender = false;
        if(!$this->checkFeedbackPermission('feedback_can_upload_photo'))
        {
            echo json_encode(array(
                'result' => 0,
                'message' => __d('feedback', 'You dont\'t have permission.')
            ));
            exit;
        }

        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
        
        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $original_filename = $this->request->query['qqfile'];
        $result = $uploader->handleUpload(FEEDBACK_IMAGE_PATH);

        if (!empty($result['success'])) 
        {
            //save image
            $this->FeedbackImage->create();
            if($this->FeedbackImage->save(array(
                'name' => $original_filename,
                'image_url' => $result['filename']
            )))
            {
                $result['path'] = $this->request->base.FEEDBACK_IMAGE_URL.$result['filename'];
                $result['file'] = FEEDBACK_IMAGE_PATH . $result['filename'];
                $result['feedback_image_id'] = $this->FeedbackImage->id;
            }
        }
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        exit;
    }
    
    public function attachments($plugin_id, $target_id = 0) {
        $uid = $this->Auth->user('id');

        if (!$plugin_id || !$uid) {
            return;
        }
        $this->autoRender = false;
        $allowedExtensions = MooCore::getInstance()->_getFileAllowedExtension();
        
        $maxFileSize = MooCore::getInstance()->_getMaxFileSize();

        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions, $maxFileSize);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $path = 'uploads' . DS . 'attachments';
        $url = 'uploads/attachments';

        $original_filename = $this->request->query['qqfile'];
        $ext = $this->_getExtension($original_filename);

        $result = $uploader->handleUpload($path);

        if (!empty($result['success'])) {
            if (in_array(strtolower($ext), array('jpg', 'jpeg', 'png', 'gif'))) {
                
                $this->loadModel('Photo.Photo');

                $this->Photo->create();
                $this->Photo->set(array(
                    'target_id' => 0,
                    'type' => 'Topic',
                    'user_id' => $uid,
                    'thumbnail' => $path . DS . $result['filename']
                ));
                $this->Photo->save();

                $photo = $this->Photo->read();

                $view = new View($this);
                $mooHelper = $view->loadHelper('Moo');
                $result['thumb'] = $mooHelper->getImageUrl($photo, array('prefix' => '450'));
                $result['large'] = $mooHelper->getImageUrl($photo, array('prefix' => '1500'));
                $result['attachment_id'] = 0;
            }else {
                // save to db
                $this->loadModel('Attachment');
                $this->Attachment->create();
                $this->Attachment->set(array('user_id' => $uid,
                    'target_id' => $target_id,
                    'plugin_id' => $plugin_id,
                    'filename' => $result['filename'],
                    'original_filename' => $original_filename,
                    'extension' => $ext
                ));
                $this->Attachment->save();

                $result['attachment_id'] = $this->Attachment->id;
                $result['original_filename'] = $original_filename;
            }
        }
        
        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }
    
    public function _getExtension($filename = null) {
        $tmp = explode('.', $filename);
        $re = array_pop($tmp);
        return $re;
    }
    
    private function addActivityFeed($feedback){
        $this->loadModel('Activity');
        $activity = $this->Activity->find('first', array('conditions' => array(
                            'Activity.item_type' => 'Feedback_Feedback',
                            'Activity.item_id' => $feedback['Feedback']['id'],
        )));
        if(!$activity){
            $this->Activity->save(array(
                                    'type' => APP_USER,
                                    'action' => 'feedback_create',
                                    'user_id' => $feedback['User']['id'],                       
                                    'item_type' => 'Feedback_Feedback',
                                    'privacy' => $feedback['Feedback']['privacy'],
                                    'item_id' => $feedback['Feedback']['id'],
                                    'query' => 1,
                                    'params' => 'item',
                                    'plugin' => 'Feedback',
                                    'share' => 1
            ));
        }                
    }
}