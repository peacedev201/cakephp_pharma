<?php
Router::connect('/gif_comments/:action/*', array(
    'plugin' => 'GifComment',
    'controller' => 'gif_comments'
));

Router::connect('/gif_comments/*', array(
    'plugin' => 'GifComment',
    'controller' => 'gif_comments',
    'action' => 'index'
));
