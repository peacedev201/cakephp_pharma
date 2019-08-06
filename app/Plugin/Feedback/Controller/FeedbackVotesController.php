<?php
/**
* 
*/
class FeedbackVotesController extends FeedbackAppController
{

    public function ajax_add( $iFeedback_id){

        $this->autoRender = false;
        $iUser_id = MooCore::getInstance()->getViewer(true); 
        if($iUser_id == null)
        {
            $this->_jsonError(__d('feedback', 'Please login to continue'));
            exit;
        }
        $this->loadModel('Feedback.Feedback');
        $aFeedback = $this->Feedback->findById($iFeedback_id);
        $this->Feedback->id = $aFeedback['Feedback']['id'];
        $aVote =  $this->FeedbackVote->find('all', array(
                'conditions' => array(
                        'feedback_id' => $iFeedback_id,
                        'user_id' => $iUser_id),
            ));
        $aVote = array_shift($aVote);
            
        $re = array('total_votes' => $aFeedback['Feedback']['total_votes'], 'feedback' => 'feedback_'.$iFeedback_id, 'action' => '');

        if($aVote){           
            $this->Feedback->save(array('total_votes' => $aFeedback['Feedback']['total_votes'] - 1));

            $re['total_votes']--;
            $re['action'] = __d('feedback', 'Vote');

            $this->FeedbackVote->delete($aVote['FeedbackVote']['id']);            
        }else{
            $this->Feedback->save(array('total_votes' => $aFeedback['Feedback']['total_votes'] + 1));

            $re['total_votes']++;
            $re['action'] = __d('feedback', 'Unvote');

            $this->FeedbackVote->create();
            $this->FeedbackVote->save(array(
                    'feedback_id' => $iFeedback_id,
                    'user_id'     => $iUser_id,  
                ));
        }
        if($re['total_votes'] < 2)
        {
            $re['text'] = __d('feedback', 'Vote');
        }
        else
        {
            $re['text'] = __d('feedback', 'Votes');
        }

        echo json_encode($re);
    }
}