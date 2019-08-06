<?php
Router::connect('/usernotess/:action/*', array(
    'plugin' => 'Usernotes',
    'controller' => 'usernotess'
));

Router::connect('/usernotess/*', array(
    'plugin' => 'Usernotes',
    'controller' => 'usernotess',
    'action' => 'index'
));
