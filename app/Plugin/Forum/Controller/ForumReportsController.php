<?php 
class ForumReportsController extends ForumAppController{
    public function ajax_create($forum_topic_id = null )
    {
        $forum_topic_id = intval($forum_topic_id);
        $this->_checkPermission();
        $this->set( 'forum_topic_id', $forum_topic_id );
    }

    public function ajax_save()
    {
        $this->_checkPermission();

        if ( !empty( $this->request->data ) )
        {
            $this->autoRender = false;
            $uid = $this->Auth->user('id');

            $this->request->data['user_id'] = $uid;
            $this->ForumReport->set( $this->request->data );
            $this->_validateData( $this->ForumReport );

            $count = $this->ForumReport->find( 'count', array( 'conditions' => array(
                'ForumReport.forum_topic_id' => $this->request->data['forum_topic_id'],
                'ForumReport.user_id' => $uid )
            ) 	);
            if ( $count > 0 )
            {
                $response['result'] = 0;
                $response['message'] = __d('forum','Duplicated report');
                echo json_encode($response);
                return;
            }

            if ( $this->ForumReport->save() ) // successfully saved
            {
                if ($this->isApp())
                {
                    $this->loadModel('Forum.ForumTopic');
                    $item = $this->ForumTopic->findById($this->request->data['forum_topic_id']);
                    $topic_id = $item['ForumTopic']['parent_id'] ? $item['ForumTopic']['parent_id'] : $item['ForumTopic']['id'];
                    $response['result'] = 2;
                    $response['redirect'] =  $this->request->base.'/forums/topic/view/'.$topic_id.'?app_no_tab=1';
                    $this->Session->setFlash(__d('forum','Thank you! Your report has been submitted'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
                }else{
                    $response['result'] = 1;
                    $response['message'] = __d('forum','Thank you! Your report has been submitted');
                }

                echo json_encode($response);
            }
        }
    }

    public function admin_index()
    {
        //load reports
        $cond = array();

        $this->ForumReport->order = 'ForumReport.id desc';
        $reports = $this->paginate('ForumReport', $cond);

        $this->set(array(
            'title_for_layout' => __d('forum', 'Forum Reports'),
            'reports' => $reports,
        ));
    }

    public function admin_delete()
    {
        $this->_checkPermission(array('super_admin' => 1));

        if ( !empty( $_POST['reports'] ) )
        {
            $this->ForumReport->deleteAll(array('ForumReport.id' => $_POST['reports']));

            $this->Session->setFlash( __d('forum','Reports have been deleted') , 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));
        }

        $this->redirect( array(
            'controller' => 'forum_reports',
            'action' => 'admin_index',
            'plugin' => 'forum'
        ) );
    }
}