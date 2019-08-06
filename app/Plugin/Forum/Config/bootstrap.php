<?php
require_once(APP . DS . 'Plugin' . DS . 'Forum' . DS .'Config' . DS . 'constants.php');
if(Configure::read('Forum.forum_enabled')) {
    App::uses('ForumListener', 'Forum.Lib');
    MooSeo::getInstance()->addSitemapEntity("Forum", array(
        'forum'
    ));
    CakeEventManager::instance()->attach(new ForumListener());
}
?>