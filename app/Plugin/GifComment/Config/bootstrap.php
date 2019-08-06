<?php
App::uses('GifCommentListener', 'GifComment.Lib');
CakeEventManager::instance()->attach(new GifCommentListener()); 
?>