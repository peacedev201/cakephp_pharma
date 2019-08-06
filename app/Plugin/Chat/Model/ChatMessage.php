<?php
App::uses('ChatAppModel', 'Chat.Model');
/**
 * OauthAccessToken Model
 *
 * @property User $User
 */
class ChatMessage extends ChatAppModel {
   public $recursive = 2;
/**
 * Primary key field
 *
 * @var string
 */
	public $primaryKey = 'id';

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'id';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'id' => array(
			'notEmpty' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		)
	);
	
	//public $actsAs = array('Containable');
    public function clearOldMessages($clear_week = 0)
    {
        if($clear_week < 1)
        {
            return;
        }
        $clear_date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').' -'.$clear_week.' weeks'));
        return $this->deleteAll(array(
            "UNIX_TIMESTAMP(ChatMessage.created) < UNIX_TIMESTAMP('".$clear_date."')"
        ));
    }
}
