<?php
class ForumPin extends ForumAppModel{
	public $belongsTo = array('User', 'Forum.ForumTopic');
    public $validationDomain = 'forum';
    public $mooFields = array('plugin');

    public $validate = array(
        'time' => array(
            'isNumber' =>	array(
                'rule' => 'numeric',
                'message' => 'Day only allow numbers'
            ),
            'require' => array(
                'rule' => 'notBlank',
                'message' => 'Day is required'
            )
        ),
    );
}