<?php

class BusinessVerify extends BusinessAppModel {

    public $actsAs = array(
        'Storage.Storage' => array(
            'type' => array('business_verifies' => 'document'),
        ),
    );
    
    public $belongsTo = array(
        'User' => array('counterCache' => true),
        'Business' => array('counterCache' => true)
    );

    public function isRequestExit($iBusinessId) {
        return $this->hasAny(array('business_id' => $iBusinessId));
    }

}
