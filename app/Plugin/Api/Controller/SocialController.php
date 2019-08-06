<?php

App::uses('ApiAppController', 'Api.Controller');
define('SOCIAL_FACEBOOK', 'facebook');
define('SOCIAL_GOOGLE', 'google');

/**
 * Social Controller
 *
 */
class SocialController extends ApiAppController
{

    /**
     * Scaffold
     *
     * @var mixed
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->loadModel('User');
        $this->loadModel('Api.SocialUser');
        $this->loadModel('ProfileField');
        $this->loadModel('ProfileFieldValue');
        $this->loadModel('Activity');
        $this->loadModel('Friend');
        $this->loadModel('OauthAccessToken');
        $this->loadModel('OauthRefreshToken');
        $this->OAuth2 = $this->Components->load('OAuth2');

        $this->provider_user = array(
            'provider' => '',
            'provider_uid' => '',
            'social_email' => '',
            'display_name' => '',
            'social_token' => '',
            'photo_url' => '',
        );
    }

    public $scaffold;

    public function auth()
    {
        $data = $this->request->data;
        
        //validate provider
        $this->_validateProvider($data);

        //set provider user
        $this->provider_user = $provider_user = array(
            'provider' => $data['provider'],
            'provider_uid' => $data['provider_uid'],
            'social_email' => $data['social_email'],
            'display_name' => $data['display_name'],
            'social_token' => $data['social_token'],
            'photo_url' => !empty($data['photo_url']) ? $data['photo_url'] : '',
            'confirm_password' => !empty($data['confirm_password']) ? $data['confirm_password'] : false,
            'password' => !empty($data['password']) ? $data['password'] : ''
        );

        //get user by social email
        $user = $this->User->findByEmail($provider_user['social_email']);

        //check banned
        if (!empty($user) && $this->isBanned($user['User']['email']))
        {
            $this->autoRender = false;
            $result = array(
                'status' => 'error',
                'data' => array(),
                'message' => __d('api', 'You are banned')
            );
            exit(json_encode($result));
        }

        // special case for user who registered via facebook email , example identifier@facebook.com
        /*if ($provider_user['provider'] == SOCIAL_FACEBOOK && empty($provider_user['social_email']))
        {
            $social_user = $this->SocialUser->getSocialUser(array(
                'provider' => $provider_user['provider'],
                'provider_uid' => $provider_user['provider_uid']
            ));
            if (!empty($social_user))
            {
                $user = $this->User->findById($social_user['SocialUser']['user_id']);
                if (!empty($user))
                {
                    $this->ownerIdResouseRequest = $user['User']['id'];
                    $token = $this->createToken($user['User']['id']);
                    $this->set(array(
                        'message' => __d('api', 'success'),
                        'token' => $token,
                        '_serialize' => array('message', 'token'),
                    ));
                }
            }
        }*/
        if (!empty($user))
        {
            $social_user = $this->SocialUser->getSocialUser(array(
                'provider' => $provider_user['provider'],
                'user_id' => $user['User']['id']
            ));

            //use for authentication when confirm password
            if($provider_user['confirm_password'] && !empty($provider_user['password']))
            {
                $this->request->data('User.email', $provider_user['social_email']);
                $this->request->data('User.password', $provider_user['password']);
            }
            
            if (!$user['User']['active']) {
                throw new ApiNotFoundException(__('This account has been disabled'));
            }
            elseif (!$user['User']['approved']) {
                throw new ApiNotFoundException(__('Your account is pending for approval'));
            }
            elseif ($provider_user['confirm_password'] && empty($provider_user['password'])) {
                throw new ApiNotFoundException(__('Missing parameter : Password is REQUIRED'));
            }
            if($provider_user['confirm_password'] && !empty($provider_user['password']) && !$this->Auth->login())
            {
                throw new ApiNotFoundException(__('Parameter error : password is not correct'));
            }
            else if (!$user['User']['is_social'] && empty($social_user)) // need confirm password before sync account with social
            {
                //__d('api', 'Please confirm your email and password to associate your facebook/google account with this account'));
                if(!empty($provider_user['password']))
                {
                    $this->saveSocialUser($user['User']['id'], $provider_user);                    
                }
                else
                {
                    $this->set(array(
                        'message' => __d('api', 'success'),
                        'confirm_password' => true,
                        '_serialize' => array('message', 'confirm_password'),
                    ));
                }
            }
            else if ($user['User']['email'] == $provider_user['social_email'] && empty($social_user)) // different provider but same email
            {
                $this->saveSocialUser($user['User']['id'], $provider_user);
            }
            else if (!empty($social_user) || $user['User']['email'] == $provider_user['social_email'])
            {
                $token = $this->createToken($user['User']['id']);
                $this->set(array(
                    'message' => __d('api', 'success'),
                    'token' => $token,
                    '_serialize' => array('message', 'token'),
                ));
            }
        }
        else //no account then register
        {
            //generate password
            $gen_password = $this->SocialUser->generatePassword();

            // Fix bugs email not alway returned, due to some reason
            $email = !empty($provider_user['social_email']) ? $provider_user['social_email'] : $provider_user['identifier'] . '@facebook.com';

            $reg_data = array(
                'email' => $email,
                'name' => $provider_user['display_name'],
                'password' => $gen_password,
                'password2' => $gen_password
            );

            //get user avatar on facebook or google
            switch ($provider_user['provider'])
            {
                case SOCIAL_FACEBOOK:
                    $provider_user['photo_url'] = str_replace('width=150&height=150', 'width=200&height=200', $provider_user['photo_url']);
                    break;
                case SOCIAL_GOOGLE:
                    $provider_user['photo_url'] = str_replace('sz=50', 'sz=200', $provider_user['photo_url']);
                    break;
            }
            if (!empty($provider_user['photo_url']))
            {
                $thumb = file_get_contents($provider_user['photo_url']);

                $avatar_name = md5(uniqid()) . '.jpg';
                file_put_contents(WWW_ROOT . 'uploads' . DS . 'tmp' . DS . $avatar_name, $thumb);
                $reg_data['avatar'] = 'uploads' . DS . 'tmp' . DS . $avatar_name;
            }

            //do registration
            list($user_id, $verify_email) = $this->_saveRegistration($reg_data);
            
            //save social user
            $this->saveSocialUser($user_id, $provider_user, $verify_email);
        }
    }
    
    private function saveSocialUser($user_id, $provider_user, $verify_email = false)
    {
        $this->SocialUser->create();
        $this->SocialUser->set(array(
            'user_id' => $user_id,
            'provider' => strtolower($provider_user['provider']),
            'provider_uid' => $provider_user['provider_uid'],
            'access_token' => $provider_user['social_token']
        ));
        if($this->SocialUser->save())
        {
            //update is social for user
            $this->User->updateAll(array(
                'User.is_social' => 1
            ), array(
                'User.id' => $user_id
            ));
                    
            $token = $this->createToken($user_id);
            $this->set(array(
                'message' => __d('api', 'success'),
                'token' => $token,
                'verify_email' => $verify_email,
                '_serialize' => array('message', 'token', 'verify_email'),
            ));
        }
        else
        {
            throw new ApiNotFoundException(__d('api', 'Can not save social user'));
        }
    }

    public function register()
    {
        $data = $this->request->data;

        //validate provider
        $this->_validateProvider($data);

        //do registration
        list($user_id, $verify_email) = $this->_saveRegistration($data);
        
        $this->set(array(
            'message' => __d('api', 'success'),
            'user_id' => $user_id,
            'verify_email' => $verify_email,
            '_serialize' => array('message', 'user_id', 'verify_email'),
        ));
    }

    private function _saveRegistration($data)
    {
        // check if registration is disabled            
        if (Configure::read('core.disable_registration'))
        {
            throw new ApiNotFoundException(__d('api', 'The admin has disabled registration on this site'));
        }

        // check registration code            
//        if (Configure::read('core.enable_registration_code') && $data['registration_code'] != Configure::read('core.registration_code'))
//        {
//            throw new ApiNotFoundException(__d('api', 'Invalid registration code'));
//        }

        if (!empty($data['email']))
        {
            if ($this->isBanned($data['email']))
            {
                $this->autoRender = false;
                throw new ApiNotFoundException(__d('api', 'You are not allowed to view this site'));
            }
        }

        $data['role_id'] = ROLE_MEMBER;
        $clientIP = getenv('HTTP_X_FORWARDED_FOR') ? getenv('HTTP_X_FORWARDED_FOR') : $_SERVER['REMOTE_ADDR'];
        $data['ip_address'] = $clientIP;
        $data['code'] = md5($data['email'] . microtime());
        $data['confirmed'] = ( Configure::read('core.email_validation') ) ? 0 : 1;
        $data['last_login'] = date("Y-m-d H:i:s");
        $data['privacy'] = Configure::read('core.profile_privacy');
        $data['featured'] = 0;
        $data['username'] = '';
        $data['is_social'] = 1;

        if (!Configure::read('core.approve_users'))
        {
            $data['approved'] = 1;
        }

        $this->User->set($data);
        $this->User->validator()->remove('username');
        if (!$this->User->validates())
        {
            $errors = $this->User->invalidFields();
            throw new ApiNotFoundException(current(current($errors)));
        }

        // fixed issue: require real email, not using Facebook fake email
        if (strstr($data['email'], "facebook.com"))
        {
            throw new ApiNotFoundException(__d('api', 'Please using your real email to continue signup'));
        }

        // check custom required fields
        $custom_fields = $this->ProfileField->getRegistrationFields(true);
        $helper = MooCore::getInstance()->getHelper("Core_Moo");

        /*foreach ($custom_fields as $field)
        {
            if (!in_array($field['ProfileField']['type'], $helper->profile_fields_default))
            {
                $helper = MooCore::getInstance()->getHelper("Core_Moo");
                if ($field['ProfileField']['plugin'])
                {
                    $helper = MooCore::getInstance()->getHelper($field['ProfileField']['plugin'] . '_' . $field['ProfileField']['plugin']);
                }
                if (method_exists($helper, 'checkProfileField'))
                {
                    $result = $helper->checkProfileField($field['ProfileField']['type'], $field, $data);
                    if ($result)
                    {
                        throw new ApiNotFoundException($result);
                    }
                }
                continue;
            }
            $value = $data['field_' . $field['ProfileField']['id']];

            if ($field['ProfileField']['required'] && empty($value) && !is_numeric($value))
            {
                throw new ApiNotFoundException($field['ProfileField']['name'] . __d('api', ' is required'));
            }
        }*/

        // keep a copy of avatar for Profile Album picture, because after uploaded, behavior deleted original file
        $newTmpAvatar = '';
        $tmp_avatar_string = md5(microtime());
        if (!empty($data['avatar']))
        {
            $file = $data['avatar'];
            $epl = explode('.', $file);
            $extension = $epl[count($epl) - 1];
            $newTmpAvatar = WWW_ROOT . 'uploads' . DS . 'tmp' . DS . $tmp_avatar_string . '.' . $extension;
            copy(WWW_ROOT . $file, $newTmpAvatar);
        }

        if ($this->User->save())
        { // successfully saved
            // save profile field values
            /*foreach ($custom_fields as $field)
            {
                if (!in_array($field['ProfileField']['type'], $helper->profile_fields_default))
                {
                    $helper = MooCore::getInstance()->getHelper("Core_Moo");
                    if ($field['ProfileField']['plugin'])
                    {
                        $helper = MooCore::getInstance()->getHelper($field['ProfileField']['plugin'] . '_' . $field['ProfileField']['plugin']);
                    }
                    if (method_exists($helper, 'saveProfileField'))
                    {
                        $helper->saveProfileField($field['ProfileField']['type'], $field, $data, $this->User->id);
                    }
                    $value = '';
                    if (isset($data['field_' . $field['ProfileField']['id']]))
                    {
                        $value = $data['field_' . $field['ProfileField']['id']];
                    }

                    $this->ProfileFieldValue->create();
                    $this->ProfileFieldValue->save(array('user_id' => $this->User->id,
                        'profile_field_id' => $field['ProfileField']['id'],
                        'value' => $value
                    ));

                    continue;
                }
                if (isset($data['field_' . $field['ProfileField']['id']]))
                {
                    $value = $data['field_' . $field['ProfileField']['id']];
                    $value = (is_array($value)) ? implode(', ', $value) : $value;

                    $this->ProfileFieldValue->create();
                    $this->ProfileFieldValue->save(array('user_id' => $this->User->id,
                        'profile_field_id' => $field['ProfileField']['id'],
                        'value' => $value
                    ));
                }
            }*/

            // insert into activity feed
            $this->Activity->save(array('type' => APP_USER,
                'action' => 'user_create',
                'user_id' => $this->User->id
            ));

            $user = $this->User->read();
            $ssl_mode = Configure::read('core.ssl_mode');
            $http = (!empty($ssl_mode)) ? 'https' : 'http';
            if ($data['confirmed'])
            {
                $this->MooMail->send($data['email'], 'welcome_user', array(
                    'email' => $data['email'],
                    'password' => $data['password'],
                    'recipient_title' => $user['User']['name'],
                    'recipient_link' => $http . '://' . $_SERVER['SERVER_NAME'] . $user['User']['moo_href'],
                    'site_name' => Configure::read('core.site_name'),
                    'login_link' => $http . '://' . $_SERVER['SERVER_NAME'] . $this->request->base . '/users/member_login',
                ));
            }
            else
            {
                $this->MooMail->send($data['email'], 'welcome_user_confirm', array(
                    'email' => $data['email'],
                    'password' => $data['password'],
                    'recipient_title' => $user['User']['name'],
                    'recipient_link' => $http . '://' . $_SERVER['SERVER_NAME'] . $user['User']['moo_href'],
                    'site_name' => Configure::read('core.site_name'),
                    'confirm_link' => $http . '://' . $_SERVER['SERVER_NAME'] . $this->request->base . '/users/do_confirm/' . $data['code'],
                ));
            }

            // Send an email to admin if enabled
            if (Configure::read('core.registration_notify'))
            {
                $this->MooMail->send(Configure::read('core.site_email'), 'new_registration', array(
                    'new_user_title' => $user['User']['name'],
                    'new_user_link' => $http . '://' . $_SERVER['SERVER_NAME'] . $user['User']['moo_href'],
                    'site_name' => Configure::read('core.site_name'),
                ));
            }

            //custom: upload avatar after sign up
            if (!empty($newTmpAvatar))
            {
                $uid = $this->User->id;
                $this->loadModel('Photo.Album');
                $album = $this->Album->getUserAlbumByType($uid, 'profile');
                $title = 'Profile Pictures';
                if (empty($album))
                {
                    $this->Album->save(array('user_id' => $uid, 'type' => 'profile', 'title' => $title), false);
                    $album_id = $this->Album->id;
                }
                else
                {
                    $album_id = $album['Album']['id'];
                }


                $tmp_photo_url = 'uploads' . DS . 'tmp' . DS . $tmp_avatar_string . '.' . $extension;
                // save to db
                $this->loadModel('Photo.Photo');
                $this->Photo->create();
                $this->Photo->set(array('user_id' => $uid,
                    'target_id' => $album_id,
                    'type' => 'Photo_Album',
                    'thumbnail' => $tmp_photo_url,
                ));

                $this->Photo->save();
                $this->Album->id = $album_id;
                $filename = explode('/', $tmp_photo_url);
                $filename1 = $filename[count($filename) - 1];
                $this->Album->save(array('cover' => $filename1));
            }

            $auto_add_friend = Configure::read('core.auto_add_friend');
            if (!empty($auto_add_friend))
            {
                $list_friend = explode(',', $auto_add_friend);
                $this->Friend->autoFriends($this->User->id, $list_friend);
            }

            // avatar social
            $this->getEventManager()->dispatch(new CakeEvent('UserController.doAfterRegister', $this));

            //return
            $verify_email = false;
            if (Configure::read('core.email_validation'))
            {
                $verify_email = true;
            }
            return array($this->User->id, $verify_email);
        }
        else
        {
            throw new ApiNotFoundException($field['ProfileField']['name'] . __d('api', 'Something went wrong. Please contact the administrators'));
        }
    }
    
    public function createToken($user_id) {
        $config = array(
            'token_type' => 'bearer',
            'access_lifetime' => 86400,
            'refresh_token_lifetime' => 1209600,
        );

        $token = array(
            'access_token' => $this->generateToken(),
            'token_type' => $config['token_type'],
            'expires_in' => $config['access_lifetime'],
            'refresh_token' => $this->generateToken("refresh"),
            'scope' => null,
        );

        $expires = date('Y-m-d H:i:s', time() + $config['access_lifetime']);
        
        $accessTokenSaved = $this->OauthAccessToken->save(array('OauthAccessToken' => array(
            'client_id' => null,
            'expires' => $expires,
            'user_id' => $user_id,
            'scope' => null,
            'access_token' => $token["access_token"],
        )));
        $expires = date('Y-m-d H:i:s', time() + $config['refresh_token_lifetime']);
        $RefressTokenSaved = $this->OauthRefreshToken->save(array('OauthRefreshToken' => array(
            'client_id' => null,
            'expires' => $expires,
            'user_id' => $user_id,
            'scope' => null,
            'refresh_token' => $token["refresh_token"],
        )));
        return ($accessTokenSaved && $RefressTokenSaved) ? $token : false;
    }

    private function generateToken($type = null) {
        if ($type == "refresh") {

        }
        if (function_exists('mcrypt_create_iv')) {
            $randomData = mcrypt_create_iv(20, MCRYPT_DEV_URANDOM);
            if ($randomData !== false && strlen($randomData) === 20) {
                return bin2hex($randomData);
            }
        }
        if (function_exists('openssl_random_pseudo_bytes')) {
            $randomData = openssl_random_pseudo_bytes(20);
            if ($randomData !== false && strlen($randomData) === 20) {
                return bin2hex($randomData);
            }
        }
        if (@file_exists('/dev/urandom')) { // Get 100 bytes of random data
            $randomData = file_get_contents('/dev/urandom', false, null, 0, 20);
            if ($randomData !== false && strlen($randomData) === 20) {
                return bin2hex($randomData);
            }
        }
        // Last resort which you probably should just get rid of:
        $randomData = mt_rand() . mt_rand() . mt_rand() . mt_rand() . microtime(true) . uniqid(mt_rand(), true);
        return substr(hash('sha512', $randomData), 0, 40);
    }
    
    private function _validateProvider($data)
    {
        $social_list = array(SOCIAL_FACEBOOK, SOCIAL_GOOGLE);
        if (empty($data['provider']))
        {
            throw new ApiNotFoundException(__d('api', 'Missing parameter : Provider is REQUIRED'));
        }
        else if (!in_array($data['provider'], $social_list))
        {
            throw new ApiBadRequestException(__d('api', 'Missing parameter : Provider cound not be found'));
        }
        else if (empty($data['provider_uid']))
        {
            throw new ApiBadRequestException(__d('api', 'Missing parameter : Provider uid is REQUIRED'));
        }
        else if (empty($data['social_email']))
        {
            throw new ApiBadRequestException(__d('api', 'Missing parameter : Social email is REQUIRED'));
        }
        else if (filter_var($data['social_email'], FILTER_VALIDATE_EMAIL) === false) 
        {
            throw new ApiBadRequestException(__d('api','Parameter error : please enter a valid email'));
        }
        else if (empty($data['display_name']))
        {
            throw new ApiBadRequestException(__d('api', 'Missing parameter : Display name is REQUIRED'));
        }
        else if (empty($data['social_token']))
        {
            throw new ApiBadRequestException(__d('api', 'Missing parameter : Token is REQUIRED'));
        }
        else if($data['provider'] == SOCIAL_FACEBOOK && !$this->validateFacebookToken($data['social_token']))
        {
            throw new ApiBadRequestException(__d('api', 'Parameter error : The token could not be found'));
        }
        else if($data['provider'] == SOCIAL_GOOGLE && !$this->validateGoogleToken($data['social_token']))
        {
            throw new ApiBadRequestException(__d('api', 'Parameter error : The token could not be found'));
        }
    }
    
    private function validateFacebookToken($access_token)
    {
        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL,'https://graph.facebook.com/me?access_token='.$access_token);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $response = json_decode($response, true);
        if(isset($response['error']))
        {
            return false;
        }
        return true;
    }
    
    private function validateGoogleToken($access_token)
    {
        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL,'https://www.googleapis.com/oauth2/v1/tokeninfo?access_token='.$access_token);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        $response = json_decode($response, true);
        if(isset($response['error']))
        {
            return false;
        }
        return true;
    }
}
