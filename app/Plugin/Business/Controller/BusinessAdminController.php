<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
class BusinessAdminController extends BusinessAppController {

    public function beforeFilter() 
    {
        parent::beforeFilter();
        $this->loadModel('Business.Business');
        $this->loadModel('Business.BusinessAdmin');
        $this->loadModel('Business.BusinessFollow');
    }
    
    public function load_admin($business_id)
    {
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        $business_admins = $this->BusinessAdmin->loadAdminList($business_id, $page);
        $iCountAdmins = $this->BusinessAdmin->countAdminList($business_id, $page);
        $iLoadMore = $iCountAdmins - $page * 6;
        
        $this->set(array(
            'iLoadMore' => $iLoadMore,
            'business_admins' => $business_admins,
            'more_admin_url' => '/business_admin/load_admin/'.$business_id.'/page:'.($page + 1),
        ));
        $this->render('/Elements/lists/admin_list');
    }
    
    public function suggest_admin($business_id)
    {
        $users = $this->BusinessAdmin->suggestAdmin($business_id, $this->request->data['keyword']);
        
        echo json_encode($users);
        exit;
    }
    
    public function add_admin()
    {
        $this->autoRender = false;
        $data = $this->request->data;
        $package = $this->Business->getBusinessPackage($data['business_id']);
        $business = $this->Business->getOnlyBusiness($data['business_id']);
        if(!$package['manage_admin'])
        {
            $this->_jsonError($this->Business->upgradeMessage($business));
        }
        if(!$this->Business->permission($data['business_id'], BUSINESS_PERMISSION_MANAGE_ADMIN, $business['Business']['moo_permissions']))
        {
            $this->_jsonError($this->Business->permissionMessage());
        }
        if(empty($data['user_id']))
        {
            $this->_jsonError(__d('business', 'Please select an user'));
        }
        else if($this->BusinessAdmin->isBusinessAdmin($data['business_id'], $data['user_id']))
        {
            $this->_jsonError(__d('business', 'This user has already set as admin'));
        }
        
        $this->BusinessAdmin->set($data);
        $this->_validateData($this->BusinessAdmin);
        if($this->BusinessAdmin->save())
        {
            $business_admin = $this->BusinessAdmin->findById($this->BusinessAdmin->id);
            
            //notification
            $this->Business->sendNotification(
                $business_admin['BusinessAdmin']['user_id'], 
                MooCore::getInstance()->getViewer(true), 
                'business_add_admin', 
                $business_admin['Business']['moo_url'], 
                $business_admin['Business']['name']
            );
            
            //unban
            $this->BusinessFollow->unBan($business['Business']['id'], $business_admin['BusinessAdmin']['user_id']);
                
            $this->set(array(
                'business_admin' => $business_admin
            ));
            $this->render('/Elements/misc/admin_item');
        }
        else
        {
            $this->_jsonError(__d('business', 'Can not add admin, please try again'));
        }
    }
    
    public function remove_admin()
    {
        $data = $this->request->data;
        $package = $this->Business->getBusinessPackage($data['business_id']);
        $business = $this->Business->getOnlyBusiness($data['business_id']);
        if(!$package['manage_admin'])
        {
            $this->_jsonError($this->Business->upgradeMessage($business));
        }
        if(!$this->Business->permission($data['business_id'], BUSINESS_PERMISSION_MANAGE_ADMIN, $business['Business']['moo_permissions']))
        {
            $this->_jsonError($this->Business->permissionMessage());
        }
        if($this->BusinessAdmin->deleteAdmin($data['business_id'], $data['user_id']))
        {
            $this->_jsonSuccess(__d('business', 'Admin has been removed'));
        }
        $this->_jsonError(__d('business', 'Can not remove admin, please try again'));
    }
    
    public function save_permission()
    {
        $cUser = $this->_getUser();
        $uid = MooCore::getInstance()->getViewer(true);
        $data = $this->request->data;
        $package = $this->Business->getBusinessPackage($data['business_id']);
        if(!$package['manage_admin'])
        {
            $business = $this->Business->getOnlyBusiness($data['business_id']);
            $this->_jsonError($this->Business->upgradeMessage($business));
        }
        else if((empty($cUser) || (!$cUser['Role']['is_admin'] && !$this->Business->isBusinessOwner($data['business_id']))))
        {
            $this->_jsonError(__d('business', 'You don \'t have permission to add permission'));
        }
        else if(empty($data['permissions']))
        {
            $this->_jsonError(__d('business', 'Please select at least a permission'));
        }
        else
        {
            $permissions = implode(',', $data['permissions']);
            if($this->Business->updateAll(array(
                'Business.permissions' => "'".$permissions."'"
            ), array(
                'Business.id' => $data['business_id']
            )))
            {
                $this->_jsonSuccess(__d('business', 'Successfully saved'), true);
            }
            $this->_jsonError(__d('business', 'Something went wrong, please try again'));
        }
    }
}
