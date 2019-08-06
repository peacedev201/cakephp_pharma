<?php
Router::connect('/feelings/:action/*', array(
    'plugin' => 'Feeling',
    'controller' => 'feelings'
));

Router::connect('/feelings/*', array(
    'plugin' => 'Feeling',
    'controller' => 'feelings',
    'action' => 'index'
));
