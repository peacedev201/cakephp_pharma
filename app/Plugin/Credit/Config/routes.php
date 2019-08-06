<?php
if(Configure::read('Credit.credit_enabled')) {
    Router::connect('/credits/:action/*', array(
        'plugin' => 'Credit',
        'controller' => 'credits'
    ));

    Router::connect('/credits/*', array(
        'plugin' => 'Credit',
        'controller' => 'credits',
        'action' => 'index'
    ));

    Router::connect('/credits/index/*', array(
        'plugin' => 'Credit',
        'controller' => 'credits',
        'action' => 'index'
    ));
}
