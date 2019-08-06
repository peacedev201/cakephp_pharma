<?php
Router::connect('/avatars/:action/*', array(
    'plugin' => 'Avatar',
    'controller' => 'avatars'
));
if (Configure::read('Avatar.avatars_enabled')) {
    Router::connect('/avatars/*', array(
        'plugin' => 'Avatar',
        'controller' => 'avatars',
        'action' => 'index'
    ));
}