<?php
Router::connect('/sliders/:action/*', array(
    'plugin' => 'Slider',
    'controller' => 'sliders'
));

Router::connect('/sliders/*', array(
    'plugin' => 'Slider',
    'controller' => 'sliders',
    'action' => 'index'
));
