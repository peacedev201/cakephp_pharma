<?php

App::uses('FeelingListener', 'Feeling.Lib');
MooCache::getInstance()->setCache('feeling', array('groups' => array('feeling')));
CakeEventManager::instance()->attach(new FeelingListener());