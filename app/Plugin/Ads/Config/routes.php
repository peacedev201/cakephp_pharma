<?php
Router::connect('/admin/ads/ads_settings/:action/*', array(
    'plugin' => 'Ads',
    'admin' => true,
    'controller' => 'ads_settings'
));

Router::connect('/admin/ads/ads_settings/*', array(
    'plugin' => 'Ads',
    'admin' => true,
    'controller' => 'ads_settings',
    'action' => 'index'
));

Router::connect('/admin/ads/ads_placement/:action/*', array(
    'plugin' => 'Ads',
    'admin' => true,
    'controller' => 'ads_placement'
));

Router::connect('/admin/ads/ads_placement/*', array(
    'plugin' => 'Ads',
    'admin' => true,
    'controller' => 'ads_placement',
    'action' => 'index'
));

Router::connect('/admin/ads/ads_transaction/:action/*', array(
    'plugin' => 'Ads',
    'admin' => true,
    'controller' => 'ads_transaction'
));

Router::connect('/admin/ads/ads_transaction/*', array(
    'plugin' => 'Ads',
    'admin' => true,
    'controller' => 'ads_transaction',
    'action' => 'index'
));

Router::connect('/admin/ads/:action/*', array(
    'plugin' => 'Ads',
    'admin' => true,
    'controller' => 'ads'
));

Router::connect('/admin/ads/*', array(
    'plugin' => 'Ads',
    'admin' => true,
    'controller' => 'ads',
    'action' => 'index'
));
// rewrite url to prevent adblocker
Router::connect('/admin/commercial/commercial_placement/:action/*', array(
    'plugin' => 'Ads',
    'admin' => true,
    'controller' => 'ads_placement'
));

Router::connect('/admin/commercial/:action/*', array(
    'plugin' => 'Ads',
    'admin' => true,
    'controller' => 'ads',
));
if(Configure::read('Ads.ads_enabled'))
{
    Router::connect('/ads/:action/*', array(
        'plugin' => 'Ads',
        'controller' => 'ads'
    ));

    Router::connect('/ads/*', array(
        'plugin' => 'Ads',
        'controller' => 'ads',
        'action' => 'index'
    ));

    Router::connect('/ads_placement/:action/*', array(
        'plugin' => 'Ads',
        'controller' => 'ads_placement'
    ));

    Router::connect('/ads_placement/*', array(
        'plugin' => 'Ads',
        'controller' => 'ads_placement',
        'action' => 'index'
    ));
    
    // rewrite url to prevent adblocker
    Router::connect('/commercial/:action/*', array(
        'plugin' => 'Ads',
        'controller' => 'ads'
    ));

    Router::connect('/commercial/*', array(
        'plugin' => 'Ads',
        'controller' => 'ads',
        'action' => 'index'
    ));

    Router::connect('/commercial_placement/:action/*', array(
        'plugin' => 'Ads',
        'controller' => 'ads_placement'
    ));

    Router::connect('/commercial_placement/*', array(
        'plugin' => 'Ads',
        'controller' => 'ads_placement',
        'action' => 'index'
    ));
    Router::connect('/commercial/js/main.js', array(
        'plugin' => 'Ads',
        'controller' => 'ads',
        'action' => 'change_url'
    ));
    
    
}