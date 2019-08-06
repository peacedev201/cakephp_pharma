<?php
Router::connect('/popups/:action/*', array(
    'plugin' => 'Popup',
    'controller' => 'popups'
));

Router::connect('/popups/*', array(
    'plugin' => 'Popup',
    'controller' => 'popups',
    'action' => 'index'
));

Router::connect('/admin/popups_for_page/:controller/:action/*', array(
    'plugin' => 'Popup',
    'admin' => true
));
Router::connect('/admin/popups_for_page/:controller/*', array(
    'plugin' => 'Popup',
    'action' => 'index',
    'admin' => true
));