<?php
$prefix = 'admin';
Router::connect('/'.$prefix.'/sticker/categories', array(
    'plugin' => 'Sticker',
    'controller' => 'StickerCategories',
    'action' => 'index',
    'prefix' => $prefix, 
    $prefix => true
));

Router::connect('/'.$prefix.'/sticker/categories/:action/*', array(
    'plugin' => 'Sticker',
    'controller' => 'StickerCategories',
    'prefix' => $prefix, 
    $prefix => true
));

Router::connect('/'.$prefix.'/sticker/stickers', array(
    'plugin' => 'Sticker',
    'controller' => 'Stickers',
    'action' => 'index',
    'prefix' => $prefix, 
    $prefix => true
));

Router::connect('/'.$prefix.'/sticker/stickers/:action/*', array(
    'plugin' => 'Sticker',
    'controller' => 'Stickers',
    'prefix' => $prefix, 
    $prefix => true
));

Router::connect('/'.$prefix.'/sticker/settings', array(
    'plugin' => 'Sticker',
    'controller' => 'StickerSettings',
    'action' => 'index',
    'prefix' => $prefix, 
    $prefix => true
));

Router::connect('/'.$prefix.'/sticker/settings/:action/*', array(
    'plugin' => 'Sticker',
    'controller' => 'StickerSettings',
    'prefix' => $prefix, 
    $prefix => true
));

Router::connect('/'.$prefix.'/sticker', array(
    'plugin' => 'Sticker',
    'controller' => 'Sticker',
    'action' => 'index',
    'prefix' => $prefix, 
    $prefix => true
));

Router::connect('/'.$prefix.'/sticker/:action/*', array(
    'plugin' => 'Sticker',
    'controller' => 'Sticker',
    'prefix' => $prefix, 
    $prefix => true
));

if(Configure::read('Sticker.sticker_enabled'))
{
    Router::connect('/sticker/:action/*', array(
        'plugin' => 'Sticker',
        'controller' => 'Stickers'
    ));

    Router::connect('/sticker/*', array(
        'plugin' => 'Sticker',
        'controller' => 'Stickers',
        'action' => 'index'
    ));
    
    /////////////////////api/////////////////////
    Router::connect('/api/stickers/browse', array(
        'plugin' => 'Sticker',
        'controller' => 'ApiStickers',
        '[method]' => 'GET',
        'action' => 'browse',
        'ext' => 'json',
    ));
    
    Router::connect('/api/stickers/images/:id', array(
        'plugin' => 'Sticker',
        'controller' => 'ApiStickers',
        '[method]' => 'GET',
        'action' => 'images',
        'ext' => 'json',
    ), array(
        'id' => '[0-9]+'
    ));
    
    Router::connect('/api/stickers/search', array(
        'plugin' => 'Sticker',
        'controller' => 'ApiStickers',
        '[method]' => 'POST',
        'action' => 'apisearch',
        'ext' => 'json',
    ));
    
    Router::connect('/api/stickers/recent', array(
        'plugin' => 'Sticker',
        'controller' => 'ApiStickers',
        '[method]' => 'GET',
        'action' => 'recent',
        'ext' => 'json',
    ));
    /////////////////////end api/////////////////////
}