<?php
class FeedbackStatisticsController extends FeedbackAppController
{
    public function __construct($request = null, $response = null)
    {
        parent::__construct($request, $response);
    }

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->_checkPermission(array('super_admin' => 1));
    }

    public function admin_index()
    {
        $this->loadModel( 'Feedback' );
        $totalFeedback = $this->Feedback->find('count');
        $totalMemberFeedback = $this->Feedback->find('count',array('conditions' => array('user_id > 0')));
        $totalPrivateFeedback = $this->Feedback->find('count',array('conditions' => array('privacy'=>2)));
        $totalAnonymous = $this->Feedback->find('count',array('conditions' => array('user_id'=>0)));

        $sum = $this->Feedback->find('all',array(
            'fields' => array(
                'SUM(comment_count) as comment, SUM(total_votes) as vote'
            )));

        $this->set('totalFeedback', $totalFeedback);
        $this->set('totalMemberFeedback', $totalMemberFeedback);
        $this->set('totalAnonymous', $totalAnonymous);
        $this->set('sumComment', $sum[0][0]['comment']);
        $this->set('sumVote', $sum[0][0]['vote']);
        $this->set('title_for_layout', __d('feedback', 'Feedback Statistics'));
    }
}