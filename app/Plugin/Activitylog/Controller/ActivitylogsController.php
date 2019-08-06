<?php 
class ActivitylogsController extends ActivitylogAppController{
	public $components = array('Paginator');
	
	public function beforeFilter() {
        parent::beforeFilter();
    }
    
    public function index($type = '')
    {
        $this->_checkPermission();
    	$this->set('title_for_layout',  __d('activitylog','Activity log'));

        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;

        $activity_logs = $this->Activitylog->getActivityLogs($this->Auth->user('id'), $type, $page);
        $more_logs = $this->Activitylog->getActivityLogs($this->Auth->user('id'), $type, $page+1);
        $more_result = 0;
        if (!empty($more_logs))
            $more_result = 1;

        $this->set('activity_logs', $activity_logs);
        $this->set('more_result', $more_result);
        $this->set('type', $type);
        $this->set('more_url', '/activity_log/index/'.$type.'/page:' . ($page + 1));

        if ($page > 1 || $this->request->is('ajax')){
            $this->render('/Elements/lists/activities_list');
        }
    }

    public function delete($id = null)
    {
        $this->autoRender = false;
        $id = intval($id);
        $log = $this->Activitylog->findById($id);
        if (!empty($log)) {
            $this->_checkPermission(array('admins' => array($log['User']['id'])));

            $this->Activitylog->delete($id);
        }
    }
}