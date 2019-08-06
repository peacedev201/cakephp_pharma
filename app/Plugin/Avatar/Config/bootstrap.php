<?php
if (Configure::read('Avatar.avatars_enabled')) {
    App::uses('AvatarListener', 'Avatar.Lib');
    CakeEventManager::instance()->attach(new AvatarListener());
}