<?php
class FeedbackBlockip extends FeedbackAppModel 
{
	public $validate = array(   
        'blockip_address' =>   array(   
            'rule'     => 'notBlank',
            'message'  => 'IP address is required'
        ), 
    );

    public function isIdExist($id)
    {
        return $this->hasAny(array('id' => $id));
    }
     
    public function beforeValidate($options = array()) {
         $this->validate['blockip_address']['message'] = __d('feedback', 'IP address is required');
     }
}