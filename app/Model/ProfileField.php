<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */

class ProfileField extends AppModel {
	    
    public $order = 'ProfileField.weight asc';

	public $hasMany = array( 'ProfileFieldValue' => array( 
												'className' => 'ProfileFieldValue',						
												'dependent'=> true
											)
							); 
	public $actsAs = array(
			'Translate' => array('name' => 'nameTranslation')
	);

	public $validate = array(	
							'name' => 	array( 	 
								'rule' => 'notBlank',
								'message' => 'Name is required'
							),
							'type' => 	array( 	 
								'rule' => 'notBlank',
								'message' => 'Type is required'
							)
	);
	public $recursive = 1;
	private $_default_locale = 'eng' ;
	public function setLanguage($locale) {
		$this->locale = $locale;
	}
	
	function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->locale = Configure::read('Config.language');
	}
	
	// get custom fields for registration page
	public function getRegistrationFields($profile_type_id, $exclude_heading = false )
	{
		$cond = array( 'ProfileField.active' => 1, 
		               'ProfileField.registration' => 1,
					   'ProfileField.profile_type_id' => $profile_type_id
                     );
        
        if ( $exclude_heading )
            $cond['ProfileField.type <> ?'] = 'heading';
            
		$custom_fields = $this->find( 'all', array( 'conditions' => $cond ) );
									
		return 	$custom_fields;
	}
	
	public function getFields($profile_type_id,$type = null, $exclude_heading = false)
	{
		$cond = array(
			'ProfileField.active' => 1,
			'ProfileField.profile_type_id' => $profile_type_id
		);
		
		if ( $exclude_heading )
			$cond['ProfileField.type <> ?'] = 'heading';
		
		if ($type)
		{
			switch ($type)
			{
				case 'search':
					$cond['ProfileField.searchable'] = 1;
					break;
				case 'profile':
					$cond['ProfileField.profile'] = 1;
					break;
			}
		}

		$custom_fields = $this->find( 'all', array( 'conditions' => $cond ) );
			
		return 	$custom_fields;
	}

}