<?php
Router::connect('/forums/topic/:action/*', array(
    'plugin' => 'Forum',
    'controller' => 'forum_topics',
));

Router::connect('/forums/topic/*', array(
    'plugin' => 'Forum',
    'controller' => 'forum_topics',
    'action' => 'index'
));

Router::connect('/forums/:action/*', array(
    'plugin' => 'Forum',
    'controller' => 'forums'
));

Router::connect('/forums/*', array(
    'plugin' => 'Forum',
    'controller' => 'forums',
    'action' => 'index'
));
