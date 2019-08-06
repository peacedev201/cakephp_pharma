<?php
Router::connect('/spotlights/:action/*', array(
    'plugin' => 'Spotlight',
    'controller' => 'spotlights'
));

Router::connect('/spotlights/*', array(
    'plugin' => 'Spotlight',
    'controller' => 'spotlights',
    'action' => 'index'
));
