<?php
    require_once(APP . DS . 'Plugin' . DS . 'Sticker' . DS .'Config' . DS . 'constants.php');

    App::uses('StickerApiListener','Sticker.Lib');
    CakeEventManager::instance()->attach(new StickerApiListener());
    
    App::uses('StickerListener','Sticker.Lib');
    CakeEventManager::instance()->attach(new StickerListener());
    
    App::uses('StickerStorageListener','Sticker.Lib');
    CakeEventManager::instance()->attach(new StickerStorageListener());
?>