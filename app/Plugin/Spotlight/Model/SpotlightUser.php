<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
App::uses('SpotlightAppModel', 'Spotlight.Model');
class SpotlightUser extends SpotlightAppModel {
	public $mooFields = array('plugin','type','title', 'href');

	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
		),
		'Role' => array(
			'className' => 'Role',
			'foreignKey' => false,
			'conditions' => array('User.role_id = Role.id')
		),
	);

    public function getTopSpotlight(){
		$conditions = array( 'SpotlightUser.active' => 1, 'SpotlightUser.end_date >= DATE(NOW())', 'SpotlightUser.status' => 'active' );
		$order_setting =  Configure::read('Spotlight.spotlight_order');
		if($order_setting == 1) {
			$order = array('rand()');
		}
		else {
			$order = array('SpotlightUser.ordering ASC', 'SpotlightUser.created DESC');
		}
		$users = $this->find('all', array( 'conditions' => $conditions, 
										   'order' 		=> $order
								));

		$userids_online = array();
		$time = time() - intval('1200');
		$session = $this->query('SELECT DISTINCT user_id FROM ' . $this->tablePrefix . 'cake_sessions WHERE expires > ' . $time);
		foreach ($session as $session) {
			$userid = $session[$this->tablePrefix.'cake_sessions']['user_id'];
			if (is_numeric($userid)){
				$userids_online[] =  $userid ;
			}
		}
		foreach( $users as $key=>$user) {
			$users[$key]['User']['is_online'] = 0;
			if (in_array($user['User']['id'], $userids_online)) {
				$users[$key]['User']['is_online'] = 1;
			}
		}

		return $users;
    }

    public function checkCanJoinSpotlight(){
    	$period =  Configure::read('Spotlight.spotlight_period');
    	$viewerId = MooCore::getInstance()->getViewer(true);
    	//$conditions = array( 'SpotlightUser.user_id' => $viewerId, 'DATE_SUB(NOW() ,INTERVAL ? DAY) <= SpotlightUser.created' => $period, 'SpotlightUser.status' => 'active' );
		$conditions = array( 'SpotlightUser.user_id' => $viewerId, 'SpotlightUser.end_date >= DATE(NOW())', 'SpotlightUser.status' => 'active' );

        $users = $this->find('count', array( 'conditions' => $conditions ));

		return $users;
    }

	public function checkCredit($price)
	{
		if (Configure::read('Credit.credit_enabled')) {
			$uid = MooCore::getInstance()->getViewer(true);
			$mBalances = MooCore::getInstance()->getModel('Credit.CreditBalances');
			return $mBalances->pluginUseCredit($price, 'join_spotlight', 'spotlight_users', $uid, $uid);
		}
		return true;
	}

	public function getHref($row)
	{
		$request = Router::getRequest();
		return $request->base.'/';
	}

	public function getTitle(&$row)
	{
		return __d('spotlight','join spotlight');
	}


}