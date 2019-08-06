<?php
if(Configure::read('Spotlight.spotlight_enabled')){
    App::uses('SpotlightListener', 'Spotlight.Lib');
    CakeEventManager::instance()->attach(new SpotlightListener());
}