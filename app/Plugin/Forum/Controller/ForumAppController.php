<?php 
App::uses('AppController', 'Controller');
class ForumAppController extends AppController{
    public function beforeFilter() {
        if(isset($this->params['prefix']) && $this->params['prefix'] == 'admin')
        {
            $this->_checkPermission(array('super_admin' => 1));
        }
        if (Configure::read("Forum.forum_by_pass_force_login"))
        {
        	$this->check_force_login = false;
        }
        parent::beforeFilter();
    }
    
    protected function checkAco($forum,$type)
    {
    	$viewer = MooCore::getInstance()->getViewer();
    	
    	if (!empty($viewer) && $viewer['Role']['is_admin']) {
    		return true;
    	}
    	$this->loadModel("Forum.Forum");
    	if (!is_array($forum))
    	{
    		$forum = $this->Forum->findById($forum);
    	}
    	
    	$helper = MooCore::getInstance()->getHelper("Forum_Forum");
    	
    	if ($helper->checkModerator($viewer,$forum))
    	{
    		return true;
    	}
    	
    	$this->_checkPermission(array('aco' => $type));
    }

    protected function checkPermissionForum($idForum)
    {
        $check = $this->permissionForum($idForum);
        $return_url = '?redirect_url=' . base64_encode(FULL_BASE_URL.$this->request->here);
        if(!$check)
        {
            $this->Session->setFlash(__('Access denied'), 'default', array('class' => 'error-message'));
            $this->redirect('/pages/no-permission' . $return_url);
        }
    }
    private function permissionForum($idForum = null)
    {
        $this->loadModel('Forum.Forum');
        $viewer = MooCore::getInstance()->getViewer();
        if($idForum != null)
        {
            $role = Cache::read('permissionForum');
            if(empty($role))
            {
                $this->loadModel('Forum.Forum');
                $forums = $this->Forum->find('all',array(
                    'conditions' => array(
                        'Forum.parent_id' => 0
                    ),
                ));
                foreach($forums as $forum)
                {
                    $flag = true;
                    $role[$forum['Forum']['id']] = null;
                    $subs = $this->Forum->find('all',array(
                        'conditions' => array(
                            'Forum.parent_id' => $forum['Forum']['id']
                        ),
                    ));
                    if($forum['Forum']['permission'] != null and $forum['Forum']['permission'] != '')
                    {
                        $role[$forum['Forum']['id']] = array();
                        $ex = explode(',',$forum['Forum']['permission']);
                        $ex = $this->setKeyArray($ex);
                        $role[$forum['Forum']['id']] = $ex;
                        foreach ($subs as $sub)
                        {
                            $role[$sub['Forum']['id']] = $ex;
                        }
                        $flag = false;
                    }
                    else
                        $role[$forum['Forum']['id']] = 'everyone';
                    if($flag)
                    {
                        foreach ($subs as $sub)
                        {
                            if($sub['Forum']['permission'] != null and $sub['Forum']['permission'] != '')
                            {
                                $ex = explode(',',$sub['Forum']['permission']);
                                $ex = $this->setKeyArray($ex);
                                $role[$sub['Forum']['id']] = $ex;
                            }
                            else
                                $role[$sub['Forum']['id']] = 'everyone';

                        }
                    }
                }
                Cache::write('permissionForum',$role);
            }
            if($viewer['Role']['is_admin'])
                return true;
            if(empty($role[$idForum]))
                return false;
            if($role[$idForum] == 'everyone')
                return true;
            if(isset($role[$idForum][$viewer['Role']['id']]))
                return true;
            else
                return false;
        }
        else
            return false;
    }
    private function setKeyArray($array)
    {
        $res = array();
        for($i=0; $i < count($array ); $i++){
            $res[$array [$i]] = true;
        }
        return $res;
    }
}