<?php
 if (Configure::read('SocialLogin.social_login_enable')) {
    App::uses('SocialLoginListener', 'SocialLogin.Lib');
    CakeEventManager::instance()->attach(new SocialLoginListener());
}