<?php
if(Configure::read('Gift.gift_enabled'))
{
    Router::connect('/gifts/:action/*', array(
        'plugin' => 'Gift',
        'controller' => 'gift'
    ));

    Router::connect('/gifts/*', array(
        'plugin' => 'Gift',
        'controller' => 'gift',
        'action' => 'index'
    ));
    
    Router::connect('/gift_categories/:action/*', array(
        'plugin' => 'Gift',
        'controller' => 'gift_categories'
    ));

    Router::connect('/gift_categories/*', array(
        'plugin' => 'Gift',
        'controller' => 'gift_categories',
        'action' => 'index'
    ));
}