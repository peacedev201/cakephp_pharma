<?php
class BusinessTransaction extends BusinessAppModel 
{
    public $belongsTo = array('User',
    	'Gateway' => array(
            'className'=> 'PaymentGateway.Gateway',            
        ),
        'Business' => array(
            'className'=> 'Business.Business',            
        ),
        'BusinessPaid' => array(
                'className'=> 'Business.BusinessPaid', 		
        ),
        'BusinessPackage' => array(
            'className'=> 'Business.BusinessPackage',
        )
    	);
}
