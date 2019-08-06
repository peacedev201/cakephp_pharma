<?php 
class SpotlightAddUsersController extends SpotlightAppController{
    public $paginate = array( 'order' => 'User.id desc', 'limit' => RESULTS_LIMIT );

    public function admin_index(){
        if ( !empty( $this->request->data['keyword'] ) )
            $this->redirect( '/admin/spotlight/spotlight_add_users/index/keyword:' . $this->request->data['keyword'] );
        $cond = array();
        $period =  Configure::read('Spotlight.spotlight_period');
        $this->loadModel('Spotlight.SpotlightUser');
        $spotlightUser = $this->SpotlightUser->find('list', array(
            'fields' => array('SpotlightUser.user_id'),
            'conditions' => array('SpotlightUser.end_date >= DATE(NOW())')
        ));

        if(!empty($spotlightUser)){
            $cond = array('NOT' => array('User.id' => $spotlightUser));
        }
        
        if ( !empty( $this->request->named['keyword'] ) ){
            $keyword = $this->request->named['keyword'];
            $cond['OR'] = array(
                'User.name LIKE ? ' => '%'.$keyword.'%',
                'User.email LIKE ? ' => '%'.$keyword.'%'
            );
        }
                  
        $users = $this->paginate( 'User', $cond );  
        $this->set('users', $users);
        $this->set('title_for_layout', __d('spotlight','Spotlight Add User'));
    }

    public function admin_add($user_id = null){
        $this->autoRender = false;
        $this->loadModel('Spotlight.SpotlightUser');
        $exitUser = $this->SpotlightUser->findByUserId($user_id);
        $period =  Configure::read('Spotlight.spotlight_period');
        if(!empty($exitUser)){
            $this->SpotlightUser->id = $exitUser['SpotlightUser']['id'];
            $data = array();
            $data['active'] = 1;
            $data['created'] = date("Y-m-d H:i:s");
            $data['end_date'] = date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s")." +".$period." days"));
            $data['status'] = 'active';
            $this->SpotlightUser->save($data);
        }else{
            $data = array();
            $data['user_id'] = $user_id;
            $data['active'] = 1;
            $data['created'] = date("Y-m-d H:i:s");
            $data['end_date'] = date('Y-m-d H:i:s', strtotime(date("Y-m-d H:i:s")." +".$period." days"));
            $data['status'] = 'active';
            $this->SpotlightUser->set($data);
            $this->SpotlightUser->save();
        }
        $this->loadModel('Notification');
        $this->Notification->clear();
        $this->Notification->save(array(
            'user_id' => $user_id,
            'sender_id' => $user_id,
            'action' => 'join_spotlight',
            'params' => json_encode(array('period' => intval($period))),
            'url' => '/home',
            'plugin' => 'Spotlight'
        ));

        //clear cache
        Cache::delete('spotlight.top_spotlight','spotlight');
      
        $this->Session->setFlash(__d('spotlight','User has been successfully added to Spotlight'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        $this->redirect( $this->referer() );
    }
}