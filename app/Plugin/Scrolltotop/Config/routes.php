<?php
Router::connect('/scrolltotops/:action/*', array(
    'plugin' => 'Scrolltotop',
    'controller' => 'scrolltotops'
));

Router::connect('/scrolltotops/*', array(
    'plugin' => 'Scrolltotop',
    'controller' => 'scrolltotops',
    'action' => 'index'
));
