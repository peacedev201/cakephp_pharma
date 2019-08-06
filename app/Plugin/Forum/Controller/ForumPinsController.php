<?php 
class ForumPinsController extends ForumAppController{
	public function beforeFilter() {
		$this->_checkPermission(array('confirm' => true));
		parent::beforeFilter();
	}
	
	public function gateway()
	{
        $this->loadModel('PaymentGateway.Gateway');
        $gateways = $this->Gateway->find('all', array('conditions' => array('enabled' => "1")));
		$currency = Configure::read('Config.currency');
		$id = isset($this->request->data['id']) ? $this->request->data['id']: 0;
		$time = isset($this->request->data['time']) ? $this->request->data['time']: 0;
		
		$this->loadModel("Forum.ForumTopic");
		$topic = $this->ForumTopic->findById($id);
		$this->_checkExistence($topic);
		
		if (!$time)
		{
			$this->_checkExistence(null);
		}
		
		$uid = MooCore::getInstance()->getViewer(true);
		if ($uid != $topic['ForumTopic']['user_id'])
		{
			$this->_checkExistence(null);
		}
		
		$this->set('currency',$currency);
		$this->set('gateways',$gateways);
		$this->set('topic',$topic);
		$this->set('id',$id);
		$this->set('time',$time);
		$price = $time * Configure::read('Forum.forum_price_pin_per_day');
		$this->set('price',$price);

		if(!Configure::read('Forum.forum_price_pin_per_day')){
            $forumHelper = MooCore::getInstance()->getHelper('Forum_Forum');
            $data = array(
                'user_id' => MooCore::getInstance()->getViewer(true),
                'gateway_id' => 0,
                'forum_topic_id' => $id,
                'time' => $time,
                'amount' => $price,
                'currency' => $currency['Currency']['currency_code']
            );
            $this->ForumPin->clear();
            $this->ForumPin->save($data);

            $item = $this->ForumPin->read();
            $forumHelper->onSuccessful($item);

            $this->Session->setFlash( __d('forum','Topic has been pinned') , 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));
            return $this->redirect($topic['ForumTopic']['moo_url']);
        }
		
		if ($this->request->is('post') && !empty($this->request->data['gateway_id'])) {
			$this->loadModel("Forum.ForumPin");
			$gateway = $this->Gateway->findById($this->request->data['gateway_id']);
			if (!$gateway) {
				$this->Session->setFlash(__d('forum', 'Gateway invalid'), 'default', array('class' => 'Metronic-alerts alert alert-danger fade in', 'service'));
				return $this->redirect('/');
			}
			$pin_item = $this->ForumPin->find('first', array(
			    'conditions' => array('ForumPin.user_id' => MooCore::getInstance()->getViewer(true), 'ForumPin.forum_topic_id' => $id, 'ForumPin.active' => 0)
            ));
			$data = array(
				'user_id' => MooCore::getInstance()->getViewer(true),
				'gateway_id' => $this->request->data['gateway_id'],
				'forum_topic_id' => $id,
				'time' => $time,
				'amount' => $time * Configure::read('Forum.forum_price_pin_per_day'),
				'currency' => $currency['Currency']['currency_code']
			);
			$this->ForumPin->clear();
			if(!empty($pin_item)){
			    $this->ForumPin->id = $pin_item['ForumPin']['id'];
            }
			$this->ForumPin->save($data);

			$pin_id = $this->ForumPin->id;
			$plugin = $gateway['Gateway']['plugin'];
			$helperGateway = MooCore::getInstance()->getHelper($plugin . '_' . $plugin);
			return $this->redirect($helperGateway->getUrlProcess() . '/Forum_Forum_Pin/' . $pin_id);
		}
	}
	
	public function success($id = null)
	{
		$this->loadModel("Forum.ForumTopic");
		$topic = $this->ForumTopic->findById($id);
		$this->_checkExistence($topic);
		$this->set('topic',$topic);
	}
	
	public function moderator()
	{
		$id = isset($this->request->data['id']) ? $this->request->data['id']: 0;
		$time = isset($this->request->data['time']) ? $this->request->data['time']: 0;
		
		$this->loadModel("Forum.ForumTopic");
		$topic = $this->ForumTopic->findById($id);
		$this->_checkExistence($topic);
		
		if (!$time)
		{
			$this->_checkExistence(null);
		}
		$user = MooCore::getInstance()->getViewer();
		$forumHelper = MooCore::getInstance()->getHelper('Forum_Forum');
		$is_moderator = $forumHelper->checkModerator($user,array('Forum'=>$topic['Forum']));
		if (!$is_moderator)
		{
			$this->_checkExistence(null);
		}
		$this->loadModel("Forum.ForumPin");
		$data = array(
			'user_id' => MooCore::getInstance()->getViewer(true),
			'gateway_id' => 0,
			'forum_topic_id' => $id,
			'time' => $time,
			'amount' => 0,
			'currency' => ''
		);
		$this->ForumPin->clear();
		$this->ForumPin->save($data);
		$item = $this->ForumPin->read();
		$forumHelper->onSuccessful($item);
		
		$this->Session->setFlash( __d('forum','Topic has been pinned') , 'default', array('class' => 'Metronic-alerts alert alert-success fade in' ));
		$this->redirect($topic['ForumTopic']['moo_url']);
	}

	public function unpin($id = null){
        $this->loadModel("Forum.ForumTopic");
        $id = intval($id);
        $topic = $this->ForumTopic->findById($id);
        $this->_checkExistence($topic);

        $forumHelper = MooCore::getInstance()->getHelper('Forum_Forum');
        $allow_ids = array($topic['User']['id']);
        $moderators = $forumHelper->getModeratorFromForum(array('Forum' => $topic['Forum']));
        $allow_ids = array_merge($moderators, $allow_ids);

        $this->_checkPermission(array('admins' => $allow_ids));

        $forumHelper = MooCore::getInstance()->getHelper('Forum_Forum');
        $forumHelper->onExpire($topic);

        $this->Session->setFlash(__d('forum','Topic has been unpinned'), 'default', array('class' => 'Metronic-alerts alert alert-success fade in'));
        if($id){
            $this->redirect('/forums/topic/view/'.$id);
        }else{
            $this->redirect('/forums/topic');
        }
    }
}