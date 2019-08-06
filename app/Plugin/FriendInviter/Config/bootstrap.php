<?php
if (Configure::read('FriendInviter.friendinviter_enabled')) {
    App::uses('FriendinviterListener', 'FriendInviter.Lib');
    CakeEventManager::instance()->attach(new FriendinviterListener());
}