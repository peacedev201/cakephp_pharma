<?php
if (Configure::read('Credit.credit_enabled')) {
    App::uses('CreditListener', 'Credit.Lib');
    CakeEventManager::instance()->attach(new CreditListener());
    define('CREDIT_STATUS_DELETE_AFTER_COMPLETE', '4');
    define('CREDIT_STATUS_DELETE_NOT_COMPLETE', '3');
    define('CREDIT_STATUS_DELETE', '2');
    define('CREDIT_STATUS_COMPLETED', '1');
    define('CREDIT_STATUS_PENDING', '0');
    define('ENABLE_WITHDRAW','0'); // 0 off,1 on
}
