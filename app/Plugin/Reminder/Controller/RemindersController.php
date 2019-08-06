<?php 
class RemindersController extends ReminderAppController{
	public $components = array('Paginator');
	public function admin_index()
	{
		/*if ( !empty( $this->request->data['keyword'] ) )
			$this->redirect( '/admin/reminder/reminders/index/keyword:' . $this->request->data['keyword'] );*/
			
		$cond = array();
		if ( !empty( $this->request->query['keyword'] ) )
		{
			$cond['OR']= array(
					array('User.name LIKE '=>'%'.$this->request->query['keyword'].'%'),
					array('User.email LIKE '=>'%'.$this->request->query['keyword'].'%'),
			);
		}
		
		$this->loadModel("Reminder.ReminderUser");
		$this->ReminderUser->bindModel(
			array('belongsTo' => array('User')),false
		);
		
		$this->Paginator->settings['paramType'] = 'querystring';
		
		$users = $this->paginate( 'ReminderUser', $cond );
		
		$this->set('users', $users);
		$this->set('title_for_layout', __d('reminder','Logs'));
	}
    
}