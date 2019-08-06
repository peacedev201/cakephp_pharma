<?php
class FeedbackCategory extends FeedbackAppModel 
{
    public $actsAs = array('Translate' => array('name' => 'nameTranslation'));

	public $validate = array(   
        'name' =>   array(   
            'rule'     => 'notBlank',
            'message'  => 'Name is required'
        )
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
        return $this->hasAny(array('FeedbackCategory.id' => $id));
    }

    public function deleteCategory($id){

        $this->delete($id);

        App::import('Model', 'Feedback.Feedback');
        $Feedback = new Feedback();
        $aFeedbacks = $Feedback->find('all', array(
                'conditions' => array('category_id' => $id),
            ));
        
        foreach ($aFeedbacks as $aFeedback) {
            $Feedback->id = $aFeedback['Feedback']['id'];
            $Feedback->save(array('category_id' => null));
        }
    }

    public function getCatById($id) {
        $category = $this->findById($id);
        if (empty($category)) {
            $this->locale = $this->_default_locale;
            $category = $this->findById($id);
        }
        return $category;
    }
}