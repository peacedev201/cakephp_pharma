<?php
App::uses('Widget','Controller/Widgets');
class most_voted_feedbackFeedbackWidget extends Widget {
    public function beforeRender(Controller $controller) 
    {
        if(Configure::read('Feedback.feedback_enabled'))
        {
            $mFeedback = MooCore::getInstance()->getModel('Feedback.Feedback');
            $num_item_show = $this->params['num_item_show'];
            $mostVotedFeedbacks = $mFeedback->mostViewFeedback($num_item_show);
            $this->setData('mostVotedFeedbacks', $mostVotedFeedbacks);
        }
    }
}