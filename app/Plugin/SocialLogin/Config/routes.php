<?php

Router::connect('/sociallogin/:action/*', array(
    'plugin' => 'social_login',
    'controller' => 'social_logins'
));

Router::connect('/sociallogin/*', array(
    'plugin' => 'social_login',
    'controller' => 'social_logins',
    'action' => 'index'
));