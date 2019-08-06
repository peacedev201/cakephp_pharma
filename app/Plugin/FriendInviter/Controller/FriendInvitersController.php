<?php

class FriendInvitersController extends FriendInviterAppController {

    public $components = array('FriendInviter.Inviter','Paginator');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->set('is232', $this->Inviter->is232());
        $this->_checkPermission(array('aco' => 'friendinviter_invite'));
    }
    
    public function admin_index() {
        
    }

    public function index() {
        $this->_checkPermission(array('confirm' => true));
        $providers_config = Configure::read('FriendInviter.web_account_services');

        $providers_sample = array(
            'google' => array('title' => 'GMail',
                'logo' => 'gmail'
            ),
            'yahoo' => array('title' => 'Yahoo!',
                'logo' => 'yahoo'
            ),
            'live' => array('title' => 'Live/Hotmail',
                'logo' => 'hotmail'
            )
        );

        $providers = array();

        if(!empty($providers_config)){
            if (is_array($providers_config)) {
                foreach ($providers_config as $value) {
                    if (isset($providers_sample[$value])) {
                        $providers[$value] = $providers_sample[$value];
                    }
                }
            }else{
                $providers[$providers_config] = $providers_sample[$providers_config];
            }
        }

        $this->set('providers', $providers);
        
        $this->loadmodel('FriendInviter.UserSuggestCode');
        $uid = $this->Auth->user('id');
        $suggest_row = $this->UserSuggestCode->findByUserId($uid);
        
        if($suggest_row){
            $suggest_code = $suggest_row['UserSuggestCode']['suggest_code'];
       }else{
            $this->loadModel('FriendInviter.Invite');
            do {
                $suggest_code = substr(md5(rand(0, 999)), 10, 7);
            } while (count($this->Invite->findByCode($suggest_code)));
            $saved_data = array(
                                'user_id' => $uid,
                                'suggest_code' => $suggest_code
                            );

            $this->UserSuggestCode->create();
            $this->UserSuggestCode->save($saved_data);
        }
        $ssl_mode = Configure::read('core.ssl_mode');
        $http = (!empty($ssl_mode)) ? 'https' :  'http';
        $invite_link = $http.'://'.$_SERVER['SERVER_NAME'].$this->request->base.'/users/register' . '/suggest_code:' . $suggest_code;
        $this->set('invite_link', $invite_link);
        $this->set('site_link', $http.'://'.$_SERVER['SERVER_NAME']);
    }

    public function getcontacts() {    
        if((isset($_GET['error']) && $_GET['error'] == 'access_denied') || isset($_GET['denied'])){
            $this->redirect('/friend_inviters');
	}
	          
        if(isset($this->request->params['pass'][0]) && $this->request->params['pass'][0] == 'livedone'){
            $_GET['hauth_done'] = 'Live';
        }
        
        $this->_checkPermission(array('confirm' => true));
        ini_set('display_errors', FALSE);
        error_reporting(0);

        App::import('Lib/SocialIntegration', 'Auth');

        if (isset($_GET['hauth_start'])) {
            $provider_id = trim(strip_tags($_GET['hauth_start']));

            $provider = ucfirst($provider_id);
            $hauth = $this->_getAuthAdapter($provider);

            try {
                $hauth->adapter->loginBegin();
            } catch (Exception $e) {
                $hauth->returnToCallbackUrl();
            }
        } else if (isset($_GET['hauth_done'])) {
            
            $provider_id = trim($_GET['hauth_done']);

            $provider = ucfirst($provider_id);

            $hauth = $this->_getAuthAdapter($provider);        
            $hauth->adapter->loginFinish();

            $provider_id = strtolower($provider_id);

            $_SESSION[$provider_id . 'redirect'] = 1;
            
            if(!$this->isApp()){
                return $this->redirect('/friend_inviters/getcontacts?provider='. $provider_id);
            }else{
                return $this->redirect('/friend_inviters/getcontacts?provider='. $provider_id . '&app_no_tab=1');
            }
        } else {

            $provider_id = $_GET['provider'];

            $providers_config = Configure::read('FriendInviter.web_account_services');

            if (is_array($providers_config) && !in_array($provider_id, $providers_config)) {
                header("HTTP/1.0 404 Not Found");

                die("This provider is not enable");
            }elseif(is_string($providers_config) && $provider_id != $providers_config){
                header("HTTP/1.0 404 Not Found");

                die("This provider is not enable");
            }

            $config = $this->Inviter->getSocialProvidersConfigs();

            $provider = ucfirst($provider_id);
            
            if (isset($config['providers'][$provider]['keys'])) {

                $url1 = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';

                $url = $url1 . $_SERVER['HTTP_HOST'] . $this->request->base . '/friend_inviters/getcontacts';

                $config['base_url'] = $url;
                
                if($provider_id == 'live'){
                    $redirect_uri = $url1 . $_SERVER['HTTP_HOST'] . $this->request->base . '/friend_inviters/getcontacts/livedone';
                }else{
                    $redirect_uri = $url1 . $_SERVER['HTTP_HOST'] . $this->request->base . '/friend_inviters/getcontacts?hauth_done=' . $provider;
                }
               
                $config['providers'] = array(
                    ucfirst($provider) => array(
                                                "enabled" => true,
                                                "keys" => $config['providers'][$provider]['keys'],
                                                "redirect_uri" => $redirect_uri
                                            )
                );
                
                $authnObj = new SocialIntegration_Auth($config);
               
                $storage = new SocialIntegration_Storage();

                if ($authnObj->isConnectedWith($provider)) {

                    try {
                        $hauth = $authnObj->setup($provider);
                        $user_contacts = $hauth->adapter->getUserContacts();
                        
                       
                    } catch (Exception $e) {
                        if($e->getMessage() == 'unauthorized'){
                            
                        }                      
                        $this->Session->setFlash( __d('friend_inviter','Authentication failed!'), 'default', array('class' => 'error-message'));
                    }
                                  
                    foreach ($user_contacts as $key => $value) {
                        if ( empty($user_contacts[$key]['email']) || !Validation::email($user_contacts[$key]['email']) ){
                            unset($user_contacts[$key]);
                        }
                    }
                    
                    $SiteNonSiteFriends = $this->parseUserContacts($user_contacts);
                   
                    if (!empty($SiteNonSiteFriends[0])) {
                        $this->set('addtofriend', $SiteNonSiteFriends[0]);
                        $this->loadmodel('Friend');
                        $this->loadModel('FriendRequest');
                        $uid = $this->Auth->user('id');
                        $friends = $this->Friend->getFriends($uid);
                        $friends_request = $this->FriendRequest->getRequestsList($uid);
                        $respond = $this->FriendRequest->getRequests($uid);
                        $request_id = Hash::combine($respond, '{n}.FriendRequest.sender_id', '{n}.FriendRequest.id');
                        $respond = Hash::extract($respond, '{n}.FriendRequest.sender_id');
                        $friends_requests = array_merge($friends, $friends_request);
                        $this->set(compact('friends', 'respond', 'request_id', 'friends_request'));                        
                    }

                    if (!empty($SiteNonSiteFriends[1])) {
                        $this->set('user_contacts', $SiteNonSiteFriends[1]);
                    }

                    $plug_type = 'social';
                    
                    $adapter_email = array('google', 'yahoo','live');
                    if (in_array($provider_id, $adapter_email)) {
                        $plug_type = 'email';
                    }
                    
                    $this->set('plugType', $plug_type);

                    $max_invitation = Configure::read('FriendInviter.maximum_emails');

                    $this->set('max_invitation', $max_invitation);

                    $this->set('show_photo', true);

                    $this->set('provide_id', $provider_id);
                    
                    if (empty($SiteNonSiteFriends[0]) && empty($SiteNonSiteFriends[1])) {
                        $this->set('errormessage', true);
                    }
                    
                   $this->render('/Elements/lists/contacts_list');
                } else {
                    // Authen with provider
                    $storage->clear();
                    $adapter = $authnObj->authenticate($provider);
                }
                
            } else {
                echo 'No setting for ' . ucfirst($provider_id) . ' Api';
                die();
            }
        }
    }

    public function invitetosite() {
        $this->_checkPermission(array('confirm' => true));
        
        $this->autoRender = false;
        
        $cuser = $this->_getUser();
        
        $friendsToJoin = $_POST['nonsitemembers'];

        $socialtype = $_POST['socialtype'];

        $recepients = array();

        foreach ($friendsToJoin as $friend) {
            $friend_info = explode("#", $friend);

            if (isset($friend_info[1]))
                $recepients[$friend_info[0]] = (string) $friend_info[1];
            else
                $recepients[$friend] = (string) $friend;
        }

        $user_id = $this->Auth->user('id');

        $adapter_email = array('google', 'yahoo', 'csv', 'live');

        $this->loadModel('FriendInviter.Invite');

        $site_name = Configure::read('core.site_name');

        if (!empty($_POST['custom_message'])) {
            $mes = $_POST['custom_message'];
        } else {
            $mes = '';
        }

        if (in_array($socialtype, $adapter_email)) {

            if (!empty($recepients)) {
                $i = 1;
                $this->loadmodel('FriendInviter.UserSuggestCode');
                
                $suggest_row = $this->UserSuggestCode->findByUserId($user_id);
        
                if($suggest_row){
                    $suggest_code = $suggest_row['UserSuggestCode']['suggest_code'];
               }else{
                    $this->loadModel('FriendInviter.Invite');
                    do {
                        $suggest_code = substr(md5(rand(0, 999)), 10, 7);
                    } while (count($this->Invite->findByCode($suggest_code)));
                    $saved_data = array(
                                        'user_id' => $user_id,
                                        'suggest_code' => $suggest_code
                                    );

                    $this->UserSuggestCode->create();
                    $this->UserSuggestCode->save($saved_data);
                }
                foreach ($recepients as $email) {
                    if ($i <= 20 && !empty($email)) {
                        if (!empty($email) && Validation::email(trim($email))) {
                        	$ssl_mode = Configure::read('core.ssl_mode');
        					$http = (!empty($ssl_mode)) ? 'https' :  'http';
        					
                        	$this->MooMail->send(trim($email),'site_invite',
			    				array(
			    					'email' => trim($email),
			    					'sender_title' => $cuser['moo_title'],
			    					'sender_link' => $http.'://'.$_SERVER['SERVER_NAME'].$cuser['moo_href'],
			    					'message' => $mes,
			    					'signup_link' => $http.'://'.$_SERVER['SERVER_NAME'].$this->request->base.'/users/register' . '/suggest_code:' . $suggest_code,
                                                                'site_name' => Configure::read('core.site_name')
			    				)
			    			);
                                                        
                            $saved_data = array(
                                'user_id' => $user_id,
                                'code' => $suggest_code,
                                'timestamp' => date('Y-m-d h:m:s'),
                                'message' => $mes,
                                'recipient' => $email,
                                'service' => $socialtype,
                                'social_profileid' => 0
                            );

                            $this->Invite->create();
                            $this->Invite->save($saved_data);

                            $i++;
                        }
                    }
                }
            }
        }
        
       $response['result'] = 1;
       
        echo json_encode($response);
        
    }

    protected function _getAuthAdapter($provider) {
        App::import('Lib/SocialIntegration', 'Storage');
        $storage = new SocialIntegration_Storage();
       
        // Check if SocialIntegration_Auth session already exist
        if (!$storage->config("CONFIG")) {
            header("HTTP/1.0 404 Not Found");
            die("You cannot access this page directly.");
        }

        SocialIntegration_Auth::initialize($storage->config("CONFIG"));

        $hauth = SocialIntegration_Auth::setup($provider);
        $hauth->adapter->initialize();

        return $hauth;
    }
    
    public function parseUserContacts($UserContacts) {

        $user_id = $this->Auth->user('id');
        $this->loadModel('User');
        $this->loadModel('Friend');

        $SiteNonSiteFriends = array();

        foreach ($UserContacts as $values) {
            //FIRST WE WILL FIND IF THIS USER IS SITE MEMBER
            if (isset($values['email']) && !empty($values['email'])) {
                $exist_user = $this->User->findByEmail($values['email']);
                if (count($exist_user)) {
                    if ($exist_user['User']['id'] != $user_id && !$this->Friend->areFriends($exist_user['User']['id'], $user_id)) {
                        $SiteNonSiteFriends[0][] = $exist_user['User'];
                    }
                } else {
                    $SiteNonSiteFriends[1][] = $values;
                }
            } else {
                $SiteNonSiteFriends[1][] = $values;
            }
        }
        $result[0] = '';
        $result[1] = '';
        if (!empty($SiteNonSiteFriends[1]))
            $result[1] = array_map("unserialize", array_unique(array_map("serialize", $SiteNonSiteFriends[1])));

        if (!empty($SiteNonSiteFriends[0]))
            $result[0] = array_map("unserialize", array_unique(array_map("serialize", $SiteNonSiteFriends[0])));
        return $result;
    }
        
    public function invited(){
        
    }
    
    public function csv_guide(){
        
    }
    
    public function uploads() {
        $uid = $this->Auth->user('id');

        if (!$uid)
            return; 
        
        $this->autoRender = false;
        
        $allowedExtensions = array('csv', 'txt');

        App::import('Vendor', 'qqFileUploader');
        $uploader = new qqFileUploader($allowedExtensions);

        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
        $type = 'friendinviter';
        $path = 'uploads' . DS . Inflector::pluralize($type);
        $path = WWW_ROOT . $path;
         
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
            file_put_contents($path . DS . 'index.html', '');
        }

        $original_filename = $this->request->query['qqfile'];
        $ext = $this->_getExtension($original_filename);
        
        $result = $uploader->handleUpload($path);

        if (!empty($result['success'])) {
            $result['filename'] = $result['filename'];
          //  $this->Session->write('upload_filename', $result['filename']);
        }
        
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }
    
     public function _getExtension($filename = null)
	{
		$tmp = explode('.', $filename);
		$re = array_pop($tmp);
		return $re;
	}

    public function getcsvcontacts() {
        ini_set('display_errors', FALSE);
        error_reporting(0);

        $this->_checkPermission(array('confirm' => true));
        
        if(!isset($_GET['filename']) || empty($_GET['filename']))
            return;
                
        $type = 'friendinviter';
        $path = 'uploads' . DS . Inflector::pluralize($type);
        $filebaseurl = WWW_ROOT . $path . DS;
        $filename = $_GET['filename'];

        $probable_delimiters = array(",", ";", "|", " ");
        foreach ($probable_delimiters as $delimiter) {

            $fp = fopen($filebaseurl . $filename, 'r') or die("can't open file");
            $k = 0;

            while ($csv_line = fgetcsv($fp, 4096, $delimiter)) {              
                for ($i = 0, $j = count($csv_line); $i < $j; $i++) {                 
                    try {                                                 
                        if (Validation::email(trim($csv_line[$i]))) {
                            $usercontacs_csv[$k]['email'] = $csv_line[$i];
                            $usercontacs_csv[$k]['name'] = $csv_line[$i];
                            $k++;
                            break;
                        }
                    } catch (Exception $e) {
                        continue;
                    }
                }
            }

            if (!empty($usercontacs_csv[0]['contactMail'])) {
                break;
            }
            //CLOSING THE FILE AFTER READING. 
            fclose($fp) or die("can't close file");
        }
        //AFTER READING THE FILE WE ARE UNLINKING THE FILE.
        $filebaseurl = $filebaseurl . $filename;
     //   @unlink($filebaseurl);
    
        
        if (!empty($usercontacs_csv)) {
            $SiteNonSiteFriends = $this->parseUserContacts($usercontacs_csv);
            if (!empty($SiteNonSiteFriends[0])) {
                $this->set('addtofriend', $SiteNonSiteFriends[0]);
                  
                $this->loadmodel('Friend');
                $this->loadModel('FriendRequest');
                $uid = $this->Auth->user('id');
                $friends = $this->Friend->getFriends($uid);
                $friends_request = $this->FriendRequest->getRequestsList($uid);
                $respond = $this->FriendRequest->getRequests($uid);
                $request_id = Hash::combine($respond, '{n}.FriendRequest.sender_id', '{n}.FriendRequest.id');
                $respond = Hash::extract($respond, '{n}.FriendRequest.sender_id');
                $friends_requests = array_merge($friends, $friends_request);
                $this->set(compact('friends', 'respond', 'request_id', 'friends_request'));
              
            }
            if (!empty($SiteNonSiteFriends[1])) {
                $this->set('user_contacts', $SiteNonSiteFriends[1]);
            }
        }

        if (empty($SiteNonSiteFriends[0]) && empty($SiteNonSiteFriends[1])) {
            $this->set('errormessage', true);
        }
        
        $this->set('plugType', 'email');

        $max_invitation = Configure::read('FriendInviter.maximum_emails');
        $this->set('max_invitation', $max_invitation);

        $this->set('show_photo', true);

        $this->set('provide_id', 'csv');
                
        $this->render('/Elements/lists/contacts_list');
    }

    public function pending(){
        $this->_checkPermission(array('confirm' => true));
                
        $this->loadModel('FriendInviter.Invite');
            
        $more_result = 0;

        $uid = $this->Auth->user('id');
        
        $cond = array();

        $cond = array('Invite.user_id' => $uid, "Invite.recipient <> ''", 'Invite.new_user_id = 0');
        
        $invites = $this->Paginator->paginate('Invite', $cond);
                               
        $this->set('invites', $invites);
        
        $invite_signup_count = $this->Invite->getTotalSignupInvite($uid);
        
        $this->set('invite_signup_count', $invite_signup_count);
    }
    
    public function ajax_delete() {
        if (empty($this->request->data['id'])) {
            return;
        }
        
        $this->_checkPermission(array('confirm' => true));
        
        $this->autoRender = false;
        
        $id = intval($this->request->data['id']);
        
        $this->loadModel('FriendInviter.Invite');
        $invite = $this->Invite->findById($id);
        $this->_checkExistence($invite);
        $this->Invite->deleteInvite($id);
    }
    
    public function deleteinvites() {
        $selected_invites = $_GET['selectedinvites'];

        if (!empty($selected_invites) && is_array($selected_invites)) {
            $this->loadModel('FriendInviter.Invite');
            foreach ($selected_invites as $id) {
                if (is_numeric($id)) {
                    $invite = $this->Invite->findById($id);
                    $this->_checkExistence($invite);
                    $this->_checkPermission(array('admins' => array($invite['Invite']['user_id'])));
                    $this->Invite->deleteInvite($id);
                }
            }
        }
    }
    
    public function ajax_resend($id = null) {
        if (empty($this->request->data['id'])) {
            return;
        }
        
        $this->_checkPermission(array('confirm' => true));
        
        $this->autoRender = false;
        
        $id = intval($this->request->data['id']);
        
        $this->loadModel('FriendInviter.Invite');
        $invite = $this->Invite->findById($id);
        $this->_checkExistence($invite);
        
        $cuser = $this->_getUser();
        $email = $invite['Invite']['recipient'];
        if (!empty($email) && Validation::email(trim($email))) {
            $this->loadmodel('FriendInviter.UserSuggestCode');
            $uid = $this->Auth->user('id');
            $suggest_row = $this->UserSuggestCode->findByUserId($uid);

            if($suggest_row){
                $suggest_code = $suggest_row['UserSuggestCode']['suggest_code'];
            }else{
                $this->loadModel('FriendInviter.Invite');
                do {
                    $suggest_code = substr(md5(rand(0, 999)), 10, 7);
                } while (count($this->Invite->findByCode($suggest_code)));
                $saved_data = array(
                                    'user_id' => $uid,
                                    'suggest_code' => $suggest_code
                                );

                $this->UserSuggestCode->create();
                $this->UserSuggestCode->save($saved_data);
            }
            $mes = $invite['Invite']['message'];
            try {
                $ssl_mode = Configure::read('core.ssl_mode');
                $http = (!empty($ssl_mode)) ? 'https' :  'http';
        					
                $this->MooMail->send(trim($email),'site_invite',
			    				array(
			    					'email' => trim($email),
			    					'sender_title' => $cuser['moo_title'],
			    					'sender_link' => $http.'://'.$_SERVER['SERVER_NAME'].$cuser['moo_href'],
			    					'message' => $mes,
			    					'signup_link' => $http.'://'.$_SERVER['SERVER_NAME'].$this->request->base.'/users/register'. '/suggest_code:' . $suggest_code,
                                                                'site_name' => Configure::read('core.site_name')
			    				)
                );
            } catch (Exception $e) {
                
            }
        }
    }
    
    public function ajax_invite(){
        if ($this->request->is('post')){
            if ( !empty( $this->request->data['to'] ) )
            {
                $this->autoRender = false;
                $cuser = $this->_getUser();

                $emails = explode( ',', $this->request->data['to'] );
                $i = 1;
                
                $valid = true;
                
                if(count($emails) > 10){
                    $this->_jsonError( __d('friend_inviter','Can not send more than 10 email addresses.'));
                    $valid = false;
                }
                
                if($valid){
                    foreach ($emails as $email)
                    {
                         if ( !Validation::email( trim($email) ) ){
                             $valid = false;
                             $this->_jsonError( __d('friend_inviter','Email addresses should be valid.'));
                             break;
                         }
                    }
                }
                
                if($valid){
                    $this->loadmodel('FriendInviter.UserSuggestCode');
                    $uid = $this->Auth->user('id');
                    $suggest_row = $this->UserSuggestCode->findByUserId($uid);
                    $this->loadModel('FriendInviter.Invite');
                    if($suggest_row){
                        $suggest_code = $suggest_row['UserSuggestCode']['suggest_code'];
                    }else{                      
                        do {
                            $suggest_code = substr(md5(rand(0, 999)), 10, 7);
                        } while (count($this->Invite->findByCode($suggest_code)));
                                        $saved_data = array(
                                            'user_id' => $uid,
                                            'suggest_code' => $suggest_code
                                        );

                        $this->UserSuggestCode->create();
                        $this->UserSuggestCode->save($saved_data);
                    }
                    foreach ($emails as $email)
                    {
                        if ( $i <= 10 )
                        {
                            if ( Validation::email( trim($email) ) )
                            {
                                    $ssl_mode = Configure::read('core.ssl_mode');
                                                    $http = (!empty($ssl_mode)) ? 'https' :  'http';

                                    $this->MooMail->send(trim($email),'site_invite',
                                                            array(
                                                                    'email' => trim($email),
                                                                    'sender_title' => $cuser['moo_title'],
                                                                    'sender_link' => $http.'://'.$_SERVER['SERVER_NAME'].$cuser['moo_href'],
                                                                    'message' => $this->request->data['message'],
                                                                    'signup_link' => $http.'://'.$_SERVER['SERVER_NAME'].$this->request->base.'/users/register'. '/suggest_code:' . $suggest_code
                                                            )
                                                    );
                                    
                                     $saved_data = array(
                                        'user_id' => $uid,
                                        'code' => $suggest_code,
                                        'timestamp' => date('Y-m-d h:m:s'),
                                        'recipient' => trim($email),
                                        'service' => '',
                                        'social_profileid' => 0
                                    );

                                    $this->Invite->create();
                                    $this->Invite->save($saved_data);

                            }
                        }
                        $i++;
                    }
               
                    $response = array();
                    $response['result'] = 1;
                    echo json_encode($response);
                }
            }else{
                $this->_jsonError( __d('friend_inviter','Email Addresses are required.'));
            }
        }
    }   

    //POST accept friend request from user.
    public function acceptfriend() {
        $this->request->data['status'] = 1;
        $respond = parent::ajax_respond(false);
        if($respond) {
            $this->_throwException($respond);
        }
        else {
            $this->autoRender = true;
            $this->set(array(
                'success' => true,
                '_serialize' => array('success')
            ));
        }
    }

    //POST reject friend request from user.
    public function rejectfriend() {
        $this->request->data['status'] = 0;
        $respond = parent::ajax_respond(false);
        if($respond) {
            $this->_throwException($respond);
        }
        else {
            $this->autoRender = true;
            $this->set(array(
                'success' => true,
                '_serialize' => array('success')
            ));
        }
    }

    //POST cancel friend request sent to user.
    public function cancelfriend() {
        $id = intval($this->request->data['user_id']);
        $uid = $this->Auth->user('id');

        $this->loadModel('FriendRequest');
        if ($this->FriendRequest->existRequest($uid, $id)) {
            $this->autoRender = false;
            $id = intval($id);           
            $this->FriendRequest->deleteAll(array('FriendRequest.sender_id' => $uid, 'FriendRequest.user_id' => $id));
            // Issue: counterCache not working when using deleteAll, have to using updateCounterCache
            $this->FriendRequest->updateCounterCache(array('user_id' => $id));
        } else {
            throw new ApiNotFoundException(__d('api', 'Friend request not found'));
        }
    }

    //POST delete friendship to a user
    public function deletefriend() {
        $requestdata = $this->request->data;
        $uid = $this->Auth->user('id');
        $friend_id = $requestdata['user_id'];

        if (is_numeric($friend_id)) {
            $user = $this->User->findById($friend_id);
            if (empty($user)) {
                throw new ApiNotFoundException(__d('api', 'User does not exist.'));
            }
        } else {
            throw new ApiBadRequestException(__d('api', 'User id not correct.'));
        }
        if ($this->Friend->areFriends($uid, $friend_id)) {
            
            parent::ajax_removeRequest(false);
            
            $this->autoRender = true;
            $this->set(array(
                'success' => true,
                '_serialize' => array('success')
            ));
        }
        else {
            throw new ApiBadRequestException(__('You are not a friend of this user'));
        }
    }
}
