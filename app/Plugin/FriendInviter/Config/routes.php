<?php
if (Configure::read('FriendInviter.friendinviter_enabled')) {

    Router::connect('/friend_inviters/:action/*', array(
        'plugin' => 'FriendInviter',
        'controller' => 'friend_inviters'
    ));

    Router::connect('/friend_inviters/*', array(
        'plugin' => 'FriendInviter',
        'controller' => 'friend_inviters',
        'action' => 'index'
    ));

    Router::connect('/home/index/tab:invite-friends', array(
        'plugin' => 'FriendInviter',
        'controller' => 'friend_inviters',
        'action' => 'index'
    ));
    
    Router::connect('/friends/ajax_invite', array(
        'plugin' => 'FriendInviter',
        'controller' => 'friend_inviters',
        'action' => 'index'
    ));
}
