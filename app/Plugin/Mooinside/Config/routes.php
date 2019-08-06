<?php
Router::connect('/mooinsides/:action/*', array(
    'plugin' => 'Mooinside',
    'controller' => 'mooinsides'
));

Router::connect('/mooinsides/*', array(
    'plugin' => 'Mooinside',
    'controller' => 'mooinsides',
    'action' => 'index'
));
