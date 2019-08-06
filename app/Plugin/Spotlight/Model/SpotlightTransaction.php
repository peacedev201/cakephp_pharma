<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
App::uses('SpotlightAppModel', 'Spotlight.Model');
class SpotlightTransaction extends SpotlightAppModel {

    public $belongsTo = array('User',
	    	/*'Gateway' => array(
	    		'className'=> 'PaymentGateway.Gateway',            
	        	)*/
	    	);

    public $mooFields = array('plugin','type','title', 'href');

    public function getTitle(&$row)
    {
        return __d('spotlight','join spotlight');
    }

    public function getHref($row)
    {
        $request = Router::getRequest();
        return $request->base.'/';
    }
}