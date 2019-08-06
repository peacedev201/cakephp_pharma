<?php

define('REACTION_ALL', -1);
define('REACTION_DISLIKE', 0);
define('REACTION_LIKE', 1);
define('REACTION_LOVE', 2);
define('REACTION_HAHA', 3);
define('REACTION_WOW', 4);
define('REACTION_SAD', 5);
define('REACTION_ANGRY', 6);
define('REACTION_COOL', 7);
define('REACTION_CONFUSED', 8);

define('REACTION_LABEL_DISLIKE', 'dislike');
define('REACTION_LABEL_LIKE', 'like');
define('REACTION_LABEL_LOVE', 'love');
define('REACTION_LABEL_HAHA', 'haha');
define('REACTION_LABEL_WOW', 'wow');
define('REACTION_LABEL_SAD', 'sad');
define('REACTION_LABEL_ANGRY', 'angry');
define('REACTION_LABEL_COOL', 'cool');
define('REACTION_LABEL_CONFUSED', 'confused');

App::uses('ReactionListener', 'Reaction.Lib');
MooCache::getInstance()->setCache('reaction', array('groups' => array('reaction')));
CakeEventManager::instance()->attach(new ReactionListener());