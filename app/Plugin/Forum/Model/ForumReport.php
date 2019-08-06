<?php
class ForumReport extends ForumAppModel{
	public $belongsTo = array(
	    'User',
        'Forum.ForumTopic',
    );
    public $validationDomain = 'forum';
    public $validate = array(
        'reason' => 	array(
            'rule' => 'notBlank',
            'message' => 'Reason is required'
        )
    );
}