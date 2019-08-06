<?php
if (Configure::read('Faq.faq_enabled')) {
    
    Router::connect('/faqs/:action/*', array(
        'plugin' => 'Faq',
        'controller' => 'faqs'
    ));

    Router::connect('/faqs/*', array(
        'plugin' => 'Faq',
        'controller' => 'faqs',
        'action' => 'index'
    ));
}