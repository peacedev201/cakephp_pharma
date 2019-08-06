<?php
if(Configure::read('Business.business_enabled'))
{
    Router::connect('/businesses/my/*', array(
        'plugin' => 'Business',
        'controller' => 'business',
        'action' => 'index',
        'task' => 'my',
    ));
    Router::connect('/businesses/my_reviews/*', array(
        'plugin' => 'Business',
        'controller' => 'business',
        'action' => 'index',
        'task' => 'my_reviews',
    ));
    Router::connect('/businesses/my_following/*', array(
        'plugin' => 'Business',
        'controller' => 'business',
        'action' => 'index',
        'task' => 'my_following',
    ));
    Router::connect('/businesses/my_favourites/*', array(
        'plugin' => 'Business',
        'controller' => 'business',
        'action' => 'index',
        'task' => 'my_favourites',
    ));
    
    Router::connect('/businesses/verifies/:action/*', array(
        'plugin' => 'Business',
        'controller' => 'business_verifies'
    ));

    Router::connect('/businesses/verifies/*', array(
        'plugin' => 'Business',
        'controller' => 'business_verifies',
        'action' => 'index'
    ));
    
    Router::connect('/businesses/claims/:id', array(
        'plugin' => 'Business',
        'controller' => 'business_claims',
        'action' => 'index'
    ), array(
        'id' => '[0-9]+'
    ));
    
    Router::connect('/businesses/claims/:action/*', array(
        'plugin' => 'Business',
        'controller' => 'business_claims'
    ));

    Router::connect('/businesses/claims/*', array(
        'plugin' => 'Business',
        'controller' => 'business_claims',
        'action' => 'index'
    ));
    
    Router::connect('/businesses/:action/*', array(
        'plugin' => 'Business',
        'controller' => 'business'
    ));

    Router::connect('/businesses/*', array(
        'plugin' => 'Business',
        'controller' => 'business',
        'action' => 'index'
    ));
    
    Router::connect('/business_payment/:action/*', array(
        'plugin' => 'Business',
        'controller' => 'business_payment'
    ));

    Router::connect('/business_payment/*', array(
        'plugin' => 'Business',
        'controller' => 'business_payment',
        'action' => 'index'
    ));
    Router::connect('/categories/*', array(
        'plugin' => 'Business',
        'controller' => 'business_categories',
        'action' => 'categories'
    ));
    Router::connect('/locations/*', array(
        'plugin' => 'Business',
        'controller' => 'business_locations',
        'action' => 'locations'
    ));
    Router::connect('/business_search/*', array(
        'plugin' => 'Business',
        'controller' => 'business',
        'action' => 'search'
    ));
    
    Router::connect('/business_admin/:action/*', array(
        'plugin' => 'Business',
        'controller' => 'business_admin'
    ));

    Router::connect('/business_admin/*', array(
        'plugin' => 'Business',
        'controller' => 'business_admin',
        'action' => 'index'
    ));
    
    Router::connect('/business_photo/:action/*', array(
        'plugin' => 'Business',
        'controller' => 'business_photo'
    ));

    Router::connect('/business_photo/*', array(
        'plugin' => 'Business',
        'controller' => 'business_photo',
        'action' => 'index'
    ));
    
    Router::connect('/business_branch/:action/*', array(
        'plugin' => 'Business',
        'controller' => 'business_branch'
    ));

    Router::connect('/business_branch/*', array(
        'plugin' => 'Business',
        'controller' => 'business_branch',
        'action' => 'index'
    ));
    
    Router::connect('/business_follow/:action/*', array(
        'plugin' => 'Business',
        'controller' => 'business_follow'
    ));

    Router::connect('/business_follow/*', array(
        'plugin' => 'Business',
        'controller' => 'business_follow',
        'action' => 'index'
    ));
    
    Router::connect('/business_checkin/:action/*', array(
        'plugin' => 'Business',
        'controller' => 'business_checkin'
    ));

    Router::connect('/business_checkin/*', array(
        'plugin' => 'Business',
        'controller' => 'business_checkin',
        'action' => 'index'
    ));
    
    Router::connect('/business_review/:action/*', array(
        'plugin' => 'Business',
        'controller' => 'business_review'
    ));

    Router::connect('/business_review/*', array(
        'plugin' => 'Business',
        'controller' => 'business_review',
        'action' => 'index'
    ));
    Router::connect('/business_activities/:action/*', array(
        'plugin' => 'Business',
        'controller' => 'business_activities'
    ));

    Router::connect('/business_activities/*', array(
        'plugin' => 'Business',
        'controller' => 'business_activities',
        'action' => 'index'
    ));
    
    //route to business detail
    Router::connect('/business-feeds/*', array(
        'plugin' => 'Business',
        'controller' => 'business',
        'action' => 'view'
    ));
    
    Router::connect('/business-reviews/*', array(
        'plugin' => 'Business',
        'controller' => 'business',
        'action' => 'view'
    ));

    Router::connect('/business-photos/*', array(
        'plugin' => 'Business',
        'controller' => 'business',
        'action' => 'view'
    ));
    
    Router::connect('/business-products/*', array(
        'plugin' => 'Business',
        'controller' => 'business',
        'action' => 'view'
    ));

    Router::connect('/business-branches/*', array(
        'plugin' => 'Business',
        'controller' => 'business',
        'action' => 'view'
    ));
    
    Router::connect('/business-checkin/*', array(
        'plugin' => 'Business',
        'controller' => 'business',
        'action' => 'view'
    ));
    
    Router::connect('/business-follower/*', array(
        'plugin' => 'Business',
        'controller' => 'business',
        'action' => 'view'
    ));
    
    Router::connect('/business-contact/*', array(
        'plugin' => 'Business',
        'controller' => 'business',
        'action' => 'view'
    ));
    
    Router::connect('/business/:action/*', array(
        'plugin' => 'Business',
        'controller' => 'business'
    ));
}