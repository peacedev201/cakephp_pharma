<?php
class FeedbackStatus extends FeedbackAppModel 
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
        return $this->hasAny(array('FeedbackStatus.id' => $id));
    }
    
    public function updateStatusCount()
    {
        $statuses = $this->find('all');
        $mFeedback = MooCore::getInstance()->getModel('Feedback.Feedback');
        if($statuses != null)
        {
            foreach($statuses as $status)
            {
                $total = $mFeedback->find('count', array(
                    'conditions' => array(
                        'Feedback.status_id' => $status['FeedbackStatus']['id'],
                        'Feedback.approved' => 1
                    )
                ));
                $this->updateAll(array(
                    'FeedbackStatus.use_time' => $total
                ), array(
                    'FeedbackStatus.id' => $status['FeedbackStatus']['id']
                ));
            }
        }
    }

    public function getStaById($id) {
        $status = $this->findById($id);
        if (empty($status)) {
            $this->locale = $this->_default_locale;
            $status = $this->findById($id);
        }
        return $status;
    }
}