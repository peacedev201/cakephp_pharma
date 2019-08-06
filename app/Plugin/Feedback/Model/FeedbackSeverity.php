<?php
class FeedbackSeverity extends FeedbackAppModel 
{
    public $actsAs = array('Translate' => array('name' => 'nameTranslation'));

	public $validate = array(   
        'name' =>   array(   
            'rule'     => 'notBlank',
            'message'  => 'Name is required'
        ), 
    );

    public $recursive = 2;
    public $_default_locale = 'eng' ;
    public function setLanguage($locale) {
        $this->locale = $locale;
    }

    public function __construct($id = false, $table = null, $ds = null) 
    {
        parent::__construct($id, $table, $ds);
        $this->locale = Configure::read('Config.language');
    }

    public function isIdExist($id)
    {
        return $this->hasAny(array('FeedbackSeverity.id' => $id));
    }

    public function getSevById($id) {
        $severity = $this->findById($id);
        if (empty($severity)) {
            $this->locale = $this->_default_locale;
            $severity = $this->findById($id);
        }
        return $severity;
    }
}