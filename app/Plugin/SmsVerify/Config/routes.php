<?php
Router::connect('/sms_verifys/:action/*', array(
    'plugin' => 'SmsVerify',
    'controller' => 'sms_verifys'
));

Router::connect('/sms_verifys/*', array(
    'plugin' => 'SmsVerify',
    'controller' => 'sms_verifys',
    'action' => 'index'
));
