<?php
Router::connect('/reminders/:action/*', array(
    'plugin' => 'Reminder',
    'controller' => 'reminders'
));

Router::connect('/reminders/*', array(
    'plugin' => 'Reminder',
    'controller' => 'reminders',
    'action' => 'index'
));
