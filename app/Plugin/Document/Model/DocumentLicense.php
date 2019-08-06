<?php

App::uses('DocumentAppModel','Document.Model');
class DocumentLicense extends DocumentAppModel {
    public $validate = array(	
		'title' => 	array( 	 
			'rule' => 'notBlank',
			'message' => 'Title is required',
		),		
	);
}
