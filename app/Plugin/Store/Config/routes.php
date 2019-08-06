<?php
//route to store categories
Router::connect('/stores/store_categories', array(
    'plugin' => 'Store',
    'controller' => 'StoreCategories',
    'action' => 'index', 
));

Router::connect('/stores/store_categories/:action/*', array(
    'plugin' => 'Store',
    'controller' => 'StoreCategories',
));
if(Configure::read('Store.store_enabled'))
{
    //manager routes
    $prefix = 'manager';
    Router::connect('/stores/'.$prefix.'/settings', array(
        'plugin' => 'Store',
        'controller' => 'Stores',
        'action' => 'settings', 
        'prefix' => $prefix, 
        $prefix => true
    ));

    Router::connect('/stores/'.$prefix.'/help', array(
        'plugin' => 'Store',
        'controller' => 'Stores',
        'action' => 'help', 
        'prefix' => $prefix, 
        $prefix => true
    ));
    
    Router::connect('/stores/'.$prefix.'/load_manager_menu', array(
        'plugin' => 'Store',
        'controller' => 'Stores',
        'action' => 'load_manager_menu', 
        'prefix' => $prefix, 
        $prefix => true
    ));
    
    Router::connect('/stores/'.$prefix.'/delete', array(
        'plugin' => 'Store',
        'controller' => 'Stores',
        'action' => 'delete', 
        'prefix' => $prefix, 
        $prefix => true
    ));

    Router::connect('/stores/'.$prefix.'/save_setting', array(
        'plugin' => 'Store',
        'controller' => 'Stores',
        'action' => 'save_setting', 
        'prefix' => $prefix, 
        $prefix => true
    ));
    
    Router::connect('/stores/'.$prefix.'/transactions/:action/*', array(
        'plugin' => 'Store',
        'controller' => 'store_transactions',
        'prefix' => $prefix, 
        $prefix => true
    ));

    Router::connect('/stores/'.$prefix.'/transactions', array(
        'plugin' => 'Store',
        'controller' => 'store_transactions',
        'action' => 'index',
        'prefix' => $prefix, 
        $prefix => true
    ));
    
    Router::connect('/stores/'.$prefix.'/shippings/:action/*', array(
        'plugin' => 'Store',
        'controller' => 'store_shippings',
        'prefix' => $prefix, 
        $prefix => true
    ));

    Router::connect('/stores/'.$prefix.'/shippings', array(
        'plugin' => 'Store',
        'controller' => 'store_shippings',
        'action' => 'index',
        'prefix' => $prefix, 
        $prefix => true
    ));
    
    Router::connect('/stores/'.$prefix.'/shipping_zones/:action/*', array(
        'plugin' => 'Store',
        'controller' => 'store_shipping_zones',
        'prefix' => $prefix, 
        $prefix => true
    ));

    Router::connect('/stores/'.$prefix.'/shipping_zones', array(
        'plugin' => 'Store',
        'controller' => 'store_shipping_zones',
        'action' => 'index',
        'prefix' => $prefix, 
        $prefix => true
    ));
    
    Router::connect('/stores/'.$prefix.'/attributes', array(
        'plugin' => 'Store',
        'controller' => 'store_attributes',
        'action' => 'index',
        'prefix' => $prefix, 
        $prefix => true
    ));
    
    Router::connect('/stores/'.$prefix.'/attributes/:action/*', array(
        'plugin' => 'Store',
        'controller' => 'store_attributes',
        'prefix' => $prefix, 
        $prefix => true
    ));
    
    Router::connect('/stores/'.$prefix.'/products', array(
        'plugin' => 'Store',
        'controller' => 'store_products',
        'action' => 'index',
        'prefix' => $prefix, 
        $prefix => true
    ));
    
    Router::connect('/stores/'.$prefix.'/products/:action/*', array(
        'plugin' => 'Store',
        'controller' => 'store_products',
        'prefix' => $prefix, 
        $prefix => true
    ));
    
    Router::connect('/stores/'.$prefix.'/producers', array(
        'plugin' => 'Store',
        'controller' => 'store_producers',
        'action' => 'index',
        'prefix' => $prefix, 
        $prefix => true
    ));
    
    Router::connect('/stores/'.$prefix.'/producers/:action/*', array(
        'plugin' => 'Store',
        'controller' => 'store_producers',
        'prefix' => $prefix, 
        $prefix => true
    ));
    
    Router::connect('/stores/'.$prefix.'/orders', array(
        'plugin' => 'Store',
        'controller' => 'store_orders',
        'action' => 'index',
        'prefix' => $prefix, 
        $prefix => true
    ));
    
    Router::connect('/stores/'.$prefix.'/orders/:action/*', array(
        'plugin' => 'Store',
        'controller' => 'store_orders',
        'prefix' => $prefix, 
        $prefix => true
    ));

    Router::connect('/stores/'.$prefix.'/:controller/:action/*', array(
        'plugin' => 'Store',
        'prefix' => $prefix, 
        $prefix => true
    ));

    Router::connect('/stores/'.$prefix.'/:controller', array(
        'plugin' => 'Store',
        'action' => 'index',
        'prefix' => $prefix, 
        $prefix => true
    ));

    Router::connect('/stores/'.$prefix, array(
        'plugin' => 'Store',
        'controller' => 'Stores',
        'action' => 'index',
        'prefix' => $prefix, 
        $prefix => true
    ));
    
    Router::connect('/manager/moo_app/gzip/:action/*', array(
        'plugin' => 'MooApp',
        'controller' => 'Gzip',
        'action' => 'chunk'
    ));
    
    //route to store list
    Router::connect('/stores/load_store_list/', array(
        'plugin' => 'Store',
        'controller' => 'stores',
        'action' => 'load_store_list'
    ));
    
    Router::connect('/stores/load_store_list/*', array(
        'plugin' => 'Store',
        'controller' => 'stores',
        'action' => 'load_store_list'
    ));
    
    //route to store upload image
    Router::connect('/stores/upload_image', array(
        'plugin' => 'Store',
        'controller' => 'stores',
        'action' => 'upload_image'
    ));
    
    //route to store payment
    Router::connect('/stores/store_payments/:action/*', array(
        'plugin' => 'Store',
        'controller' => 'store_payments',
    ));
    
    //route to store package
    Router::connect('/stores/store_packages/:action/*', array(
        'plugin' => 'Store',
        'controller' => 'store_packages',
    ));
    
    //route to store check exist business
    Router::connect('/stores/check_exist_business_page', array(
        'plugin' => 'Store',
        'controller' => 'stores',
        'action' => 'check_exist_business_page'
    ));
    
    //route to check business page created store
    Router::connect('/stores/check_link_business_page/*', array(
        'plugin' => 'Store',
        'controller' => 'stores',
        'action' => 'check_link_business_page'
    ));
    
    //route to sellers list
    Router::connect('/stores/sellers', array(
        'plugin' => 'Store',
        'controller' => 'stores',
        'action' => 'sellers'
    ));
    
    //route to seller products
    Router::connect('/stores/seller_products/*', array(
        'plugin' => 'Store',
        'controller' => 'stores',
        'action' => 'seller_products'
    ));
    
    //route to create store 
    Router::connect('/stores/', array(
        'plugin' => 'Store',
        'controller' => 'stores',
        'action' => 'index'
    ));
    
    Router::connect('/stores/create', array(
        'plugin' => 'Store',
        'controller' => 'stores',
        'action' => 'create'
    ));
    
    Router::connect('/stores/create/*', array(
        'plugin' => 'Store',
        'controller' => 'stores',
        'action' => 'create'
    ));
    
    Router::connect('/stores/save', array(
        'plugin' => 'Store',
        'controller' => 'stores',
        'action' => 'save'
    ));
    
    Router::connect('/stores/product/*', array(
        'plugin' => 'Store',
        'controller' => 'stores',
        'action' => 'product'
    ));
    
    Router::connect('/stores/load_product_video/*', array(
        'plugin' => 'Store',
        'controller' => 'stores',
        'action' => 'load_product_video'
    ));
    
    Router::connect('/stores/product_video_detail/*', array(
        'plugin' => 'Store',
        'controller' => 'stores',
        'action' => 'product_video_detail'
    ));
    
    //route to products
    Router::connect('/stores/products', array(
        'plugin' => 'Store',
        'controller' => 'store_products',
        'action' => 'index'
    ));

    Router::connect('/stores/products/:action/*', array(
        'plugin' => 'Store',
        'controller' => 'store_products',
    ));
    
    //route to product reports
    Router::connect('/stores/product_reports', array(
        'plugin' => 'Store',
        'controller' => 'store_product_reports',
        'action' => 'index'
    ));

    Router::connect('/stores/product_reports/:action/*', array(
        'plugin' => 'Store',
        'controller' => 'store_product_reports',
    ));

    //route to wishlist
    Router::connect('/stores/wishlists', array(
        'plugin' => 'Store',
        'controller' => 'store_wishlists',
        'action' => 'index'
    ));

    Router::connect('/stores/wishlists/:action/*', array(
        'plugin' => 'Store',
        'controller' => 'store_wishlists',
    ));

    //route to cart
    Router::connect('/stores/carts', array(
        'plugin' => 'Store',
        'controller' => 'store_carts',
        'action' => 'index'
    ));

    Router::connect('/stores/carts/:action/*', array(
        'plugin' => 'Store',
        'controller' => 'store_carts',
    ));

    //route to my orders
    Router::connect('/stores/orders', array(
        'plugin' => 'Store',
        'controller' => 'store_orders',
        'action' => 'index'
    ));
    Router::connect('/stores/orders/:action/*', array(
        'plugin' => 'Store',
        'controller' => 'store_orders',
    ));
    
    //route to my shippings
    Router::connect('/stores/shippings/:action/*', array(
        'plugin' => 'Store',
        'controller' => 'store_shippings',
    ));
    Router::connect('/stores/shippings', array(
        'plugin' => 'Store',
        'controller' => 'store_shippings',
        'action' => 'index'
    ));
    
    //route to share product activity
    Router::connect('/stores/share_product_content/*', array(
        'plugin' => 'Store',
        'controller' => 'stores',
        'action' => 'share_product_content'
    ));

    //route to create store activity
    Router::connect('/stores/create_store_content/*', array(
        'plugin' => 'Store',
        'controller' => 'stores',
        'action' => 'create_store_content'
    ));
    
    //route to shortcut
    Router::connect('/stores/load_shortcut/*', array(
        'plugin' => 'Store',
        'controller' => 'stores',
        'action' => 'load_shortcut'
    ));

    //route to product upload
    Router::connect('/stores/product_upload/:action/*', array(
        'plugin' => 'Store',
        'controller' => 'store_product_upload',
    ));
    
    //route to reviews
    Router::connect('/stores/store_reviews', array(
        'plugin' => 'Store',
        'controller' => 'store_reviews',
        'action' => 'index'
    ));

    Router::connect('/stores/store_reviews/:action/*', array(
        'plugin' => 'Store',
        'controller' => 'store_reviews',
    ));
	
	//route to attribute
    Router::connect('/stores/attributes', array(
        'plugin' => 'Store',
        'controller' => 'store_attributes',
        'action' => 'index'
    ));

    Router::connect('/stores/attributes/:action/*', array(
        'plugin' => 'Store',
        'controller' => 'store_attributes',
    ));
    
    Router::connect('/stores/*', array(
        'plugin' => 'Store',
        'controller' => 'stores',
        'action' => 'index'
    ));

    //route to popup
    Router::connect('/store/popups', array(
        'plugin' => 'Store',
        'controller' => 'store_popups',
        'action' => 'index'
    ));

    Router::connect('/store/popups/:action/*', array(
        'plugin' => 'Store',
        'controller' => 'store_popups',
    ));
}