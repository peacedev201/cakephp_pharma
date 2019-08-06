<?php
Router::connect('/social_publishers/:action/*', array(
    'plugin' => 'SocialPublisher',
    'controller' => 'social_publishers'
));

Router::connect('/social_publishers/*', array(
    'plugin' => 'SocialPublisher',
    'controller' => 'social_publishers',
    'action' => 'index'
));
