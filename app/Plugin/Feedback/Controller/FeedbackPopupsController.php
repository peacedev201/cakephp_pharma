<?php 
class FeedbackPopupsController extends FeedbackAppController{  
    public $check_subscription = false;
	public $check_force_login = false;
    public function load_shortcut()
    {
        $is_enable = Configure::read('Feedback.feedback_button_enable');
        $useragent=$_SERVER['HTTP_USER_AGENT'];
        if($this->isApp() || $this->viewVars['isMobile'])
        {
            $is_enable = Configure::read('Feedback.feedback_button_enable_mobile');
        }
        if(!$is_enable || !$this->checkFeedbackPermission('feedback_create_feedback') || $this->checkBlockUser())
        {
            // exit;
        }
    }
}