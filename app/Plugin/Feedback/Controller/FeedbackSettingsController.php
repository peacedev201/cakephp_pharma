<?php 
class FeedbackSettingsController extends FeedbackAppController{

    public $components = array('QuickSettings');

	public function __construct($request = null, $response = null)
	{

		parent::__construct($request, $response);
		$this->loadModel('Setting');
		$this->loadModel('SettingGroup');
        $this->loadModel('Feedback.Feedback');

		$this->url_settings = '/feedback_settings';
        $this->set('url_settings', '/feedback_settings');
	}

	public function beforeFilter()
	{
		parent::beforeFilter();
        
        $this->_checkPermission(array('super_admin' => 1));
	}

    public function admin_index( $id = null )
    {
        if(Configure::read('Feedback.feedback_enabled'))
        {
            $this->Feedback->activeMenu(1);
        }
        else 
        {
            $this->Feedback->activeMenu(0);
        }
        $this->QuickSettings->run($this, array("Feedback"), $id);
        
        $mSetting = MooCore::getInstance()->getModel('Setting');
        $feedback_button_color  = $mSetting->findByName('feedback_button_color');
        $feedback_button_color_id = !empty($feedback_button_color) ? $feedback_button_color['Setting']['id'] : '';
        
        $feedback_button_color_hover  = $mSetting->findByName('feedback_button_color_hover');
        $feedback_button_color_hover_id = !empty($feedback_button_color_hover) ? $feedback_button_color_hover['Setting']['id'] : '';
        
        $feedback_text_color  = $mSetting->findByName('feedback_text_color');
        $feedback_text_color_id = !empty($feedback_text_color) ? $feedback_text_color['Setting']['id'] : '';
        
        $feedback_text_color_hover  = $mSetting->findByName('feedback_text_hover_color ');
        $feedback_text_color_hover_id = !empty($feedback_text_color_hover) ? $feedback_text_color_hover['Setting']['id'] : '';
        
        $this->set(array(
            'title_for_layout' => __d('feedback', 'Feedback Settings'),
            'feedback_button_color_id' => $feedback_button_color_id,
            'feedback_button_color_hover_id' => $feedback_button_color_hover_id,
            'feedback_text_color_id' => $feedback_text_color_id,
            'feedback_text_color_hover_id' => $feedback_text_color_hover_id
        ));
    }
}