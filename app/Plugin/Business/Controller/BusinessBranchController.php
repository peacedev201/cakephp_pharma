<?php 
class BusinessBranchController extends BusinessAppController
{    
    public $components = array('Paginator');
    public function __construct($request = null, $response = null) 
    {
        parent::__construct($request, $response);
        $this->url = $this->request->base.'/businesses/';
        $this->url_dashboard = $this->request->base.'/businesses/dashboard/';
        $this->admin_url = $this->request->base.'/admin/business/business/';
        $this->set('url', $this->url);
        $this->set('url_dashboard', $this->url_dashboard);
        $this->set('admin_url', $this->admin_url);
        $this->loadModel('Business.Business');
        $this->loadModel('Business.BusinessType');
        $this->loadModel('Business.BusinessTime');
        $this->loadModel('Business.BusinessPayment');
        $this->loadModel('Business.BusinessPaymentType');
        $this->loadModel('Business.BusinessLocation');
        $this->loadModel('Business.BusinessCategory');
        $this->loadModel('Business.BusinessCategoryItem');
        $this->loadModel('Business.BusinessPhoto');
        $this->loadModel('Business.BusinessAddress');
        $this->loadModel('Business.BusinessPackage');
        $this->loadModel('Business.BusinessAdmin');
        $this->loadModel('Business.BusinessFollow');
        $this->loadModel('Business.BusinessReview');
    }
    
    public function save_branch()
    {
        $uid = MooCore::getInstance()->getViewer(true);
        $data = $this->request->data;
        $isEdit = false;
        
        //validate
        $business = $this->Business->getOnlyBusiness($data['parent_id']);
        if(!$this->Business->permission($data['parent_id'], BUSINESS_PERMISSION_MANAGE_SUBPAGE, $business['Business']['moo_permissions']))
        {
            $this->_jsonError($this->Business->permissionMessage());
        }
        elseif((int)$data['id'] > 0 && $this->Business->isBusinessExist($data['id'], null, $data['parent_id']))
        {
            $this->Business->id = $data['id'];
            $isEdit = true;
        }

        //validate category
        if(count($data['business_category']) != count(array_filter($data['business_category'])))
        {
            $this->_jsonError(__d('business', 'Please input category'));
        }
        
        if(!$isEdit)
        {
            $data['user_id'] = $business['Business']['user_id'];
            $data['creator_id'] = MooCore::getInstance()->getViewer(true);
        }
        $data['always_open'] = !empty($data['always_open']) ? $data['always_open'] : 0;
        $data['business_package_id'] = $business['Business']['business_package_id'];
        $data['verify'] = $business['Business']['verify'];
        
        //get lng lat
        $data['lat'] = $data['lng'] = 0;
        $addressDetail = array();
        if($data['address'] != null)
        {
            $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
            $addressDetail = $businessHelper->getAddressDetail(null, null, $data['address']);
            $data['lng'] = $addressDetail['lng'];
            $data['lat'] = $addressDetail['lat'];

            //get location id
            //$addressDetail = $businessHelper->getAddressDetail($data['lat'], $data['lng']);
            $data['business_location_id'] = $this->BusinessLocation->autoAddBusinessLocation($addressDetail['country'], $addressDetail['region']);
        }
        
        //validate
        $this->Business->set($data);
        $this->_validateData($this->Business);
        
        //create album if not exist
        /*if(!$isEdit)
        {
            $data['album_id'] = $this->Business->createAlbum($data['name']);
        }
        else
        {
            $branch = $this->Business->getOnlyBusiness($data['id']);
            if(empty($branch['Business']['album_id']))
            {
                $data['album_id'] = $this->Business->createAlbum($data['name']);
            }
        }*/
        
        //check auto approve
        if(!$isEdit && Configure::read('Business.business_auto_approve_sub_page'))
        {
            $data['status'] = BUSINESS_STATUS_APPROVED;
        }
        
        //save
        if($this->Business->save($data))
        {
            $branch_id = $this->Business->id;
            $branch = $this->Business->getOnlyBusiness($branch_id);
            //save time
            if(empty($data['always_open']) || $data['always_open'] != 1)
            {
                $this->save_time($data, $branch_id);
            }
            else
            {
                $this->BusinessTime->deleteByBusiness($branch_id);
            }
            
            //save category
            $this->save_category_item($data, $branch_id);
            
            //save payment type
            $this->save_payment_type($data, $branch_id);
            
            //save branch photo
            $this->save_photo_item($data, $branch_id);
            
            //update branch photo counter
            $this->BusinessPhoto->updateBusinessPhotoCounter($branch_id);
            
            //send notification to followers
            if($branch != null && $branch['Business']['status'] == BUSINESS_STATUS_APPROVED)
            {
                $this->Business->sendBusinessNotification(
                    $branch_id, 
                    'business_add_photos', 
                    MooCore::getInstance()->getViewer(true),
                    $branch['Business']['moo_url']
                );
            }
            
            //update business branch counter
            $this->Business->updateBusinessBranchCounter($data['parent_id']);
            
            //register location for search
            $this->BusinessLocation->registerLocationKeyword(
                $data['address'], 
                $data['lat'], 
                $data['lng'], 
                !empty($addressDetail['postal_code']) ? $addressDetail['postal_code'] : null, 
                !empty($addressDetail['country']) ? $addressDetail['country'] : null, 
                !empty($addressDetail['region']) ? $addressDetail['region'] : null, 
                !empty($addressDetail['city']) ? $addressDetail['city'] : null
            );
            
            //send activity and notification
            $subpage = $this->Business->getOnlyBusiness($branch_id);
            if(!$isEdit && Configure::read('Business.business_auto_approve_sub_page'))
            {
                //notification
                $this->Business->sendBusinessNotification(
                    $subpage['Business']['parent_id'], 
                    'business_create_branch', 
                    $uid,
                    $subpage['Business']['moo_url'],
                    MooCore::getInstance()->getViewer(true)
                );
                
                //activity
                $this->Business->saveBranchActivity($data['parent_id'], $branch_id);
            }
            else if(!$isEdit && !Configure::read('Business.business_auto_approve_sub_page'))
            {
                $aUsers = $this->User->find('all', array('conditions' => array('Role.is_admin' => 1)));
                foreach ($aUsers as $aUser) {
                    if($aUser['User']['id'] != $data['user_id'])
                    {
                        $this->Business->sendNotification(
                            $aUser['User']['id'], 
                            $data['user_id'], 
                            'business_sub_page_pending', 
                            '/admin/business',
                            $data['name']
                        );
                    }
                }
            }
            
            if(!Configure::read('Business.business_auto_approve_sub_page') && !$isEdit)
            {
                $this->_jsonSuccess(__d('business', 'Sub page has been successfully saved and pending for approval.'), true, array(
                    'location' => $this->url_dashboard.'branches/'.$data['parent_id']
                ));
            }

            $this->_jsonSuccess(__d('business', 'Successfully saved'), true, array(
                'location' => $this->url_dashboard.'branches/'.$data['parent_id']
            ));
        }
        $this->_jsonError(__d('business', 'Can not save branch, please try again'));
    }
    
    private function save_category_item($data, $business_id)
    {
        $old_cat = $this->BusinessCategoryItem->getListCategoryId($business_id);
        $this->BusinessCategoryItem->deleteByBusiness($business_id);
        $exist_keys = array();
        $exist_category = array();
        if(!empty($data['category_id']))
        {
            $business = $this->Business->findById($business_id);
            $data['category_id'] = array_unique($data['category_id']);
            foreach($data['category_id'] as $k => $business_category_id)
            {
                if($business_category_id > 0)
                {
                    $exist_keys[] = $k;
                    $exist_category[] = $business_category_id;
                    $this->BusinessCategoryItem->create();
                    $this->BusinessCategoryItem->save(array(
                        'business_id' => $business_id,
                        'business_category_id' => $business_category_id,
                    ));
                }
            }
        }
        
        // add new category
        if(!empty($data['business_category']))
        {
            foreach($data['business_category'] as $k => $business_category)
            {
                if(in_array($k, $exist_keys))
                {
                    continue;
                }
                $category = $this->BusinessCategory->findByName($business_category);
                if($category != null && in_array($category['BusinessCategory']['id'], $exist_category))
                {
                    continue;
                }
                if($category != null)
                {
                    $business_category_id = $category['BusinessCategory']['id'];
                }
                else
                {
                    $this->BusinessCategory->create();
                    $this->BusinessCategory->save(array(
                        'name' => $business_category,
                        'user_create' => 1 
                    ));
                    $business_category_id = $this->BusinessCategory->id;
                }
                
                $this->BusinessCategoryItem->create();
                $this->BusinessCategoryItem->save(array(
                    'business_id' => $business_id,
                    'business_category_id' => $business_category_id,
                ));
            }
        }
    }
    
    private function save_time($data, $business_id)
    {
        $this->BusinessTime->deleteByBusiness($business_id);
        if(!empty($data['day']))
        {
            foreach($data['day'] as $k => $day)
            {
                $array_open = explode(' ', $data['time_open'][$k]);
                $array_close = explode(' ', $data['time_close'][$k]);
                $this->BusinessTime->create();
                $this->BusinessTime->save(array(
                    'business_id' => $business_id,
                    'day' => $day,
                    'time_open' => $array_open[1],
                    'time_close' => $array_close[1],
                    'next_day' => $array_close[0]
                ));
            }
        }
    }
    
    private function save_payment_type($data, $business_id)
    {
        $this->BusinessPaymentType->deleteByBusiness($business_id);
        if(!empty($data['business_payment_id']))
        {
            foreach($data['business_payment_id'] as $business_payment_id)
            {
                $this->BusinessPaymentType->create();
                $this->BusinessPaymentType->save(array(
                    'business_id' => $business_id,
                    'business_payment_id' => $business_payment_id,
                ));
            }
        }
    }
    
    private function save_photo_item($data, $branch_id)
    {
        $branch = $this->Business->getOnlyBusiness($branch_id);
        
        //delete
        if(!empty($data['photo_delete_id']))
        {
            $this->BusinessPhoto->deletePhotoList($data['photo_delete_id']);
        }
        
        //update
        if(!empty($data['photo_caption_exist']))
        {
            foreach($data['photo_caption_exist'] as $photo_id => $caption)
            {
                if((int)$photo_id > 0)
                {
                    $this->BusinessPhoto->updateAll(array(
                        'BusinessPhoto.caption' => "'".$caption."'",
                    ), array(
                        'BusinessPhoto.id' => $photo_id
                    ));
                }
            }
        }
        
        //new
        if(!empty($data['photo_filename']))
        {
            $photo_ids = array();
            if($branch['Business']['album_id'] == null || !$this->Business->checkAlbumExist($branch['Business']['album_id']))
            {
                $business = $this->Business->getOnlyBusiness($branch['Business']['parent_id']);
                $album_id = $this->Business->createAlbum($branch['Business']['name'], $business['Business']['user_id']);
                $this->Business->updateBusinessAlbum($branch['Business']['id'], $album_id);
                $branch['Business']['album_id'] = $album_id;
            }
            
            foreach($data['photo_filename'] as $k => $filename)
            {
                $this->BusinessPhoto->create();
                $this->BusinessPhoto->set(array(
                    'target_id' => $branch['Business']['album_id'],
                    'type' => 'Photo_Album',
                    'user_id' => $branch['Business']['user_id'],
                    'thumbnail' => $filename,
                    'caption' => $data['photo_caption'][$k],
                    'enable' => 1
                ));
                $this->BusinessPhoto->save();
                $photo_ids[] = $this->BusinessPhoto->id;
            }
            if($photo_ids != null)
            {
                $this->BusinessPhoto->savePhotoActivity($branch_id, $photo_ids);
            }
            Cache::clearGroup('photo', 'photo');
            //update photo counter
            $this->BusinessPhoto->updateBusinessPhotoCounter($branch_id);
        }
        
        //update album photo counter
        $this->BusinessPhoto->updateAlbumPhotoCounter($branch['Business']['album_id']);
    }

    public function delete_branch($business_id, $branch_id, $my = '')
    {
        $business = $this->Business->getOnlyBusiness($business_id);
        if(!$this->Business->isBusinessExist($branch_id, null, $business_id))
        {
            $this->_redirectError(__d('business', 'Sub page not found'), '/pages/error');
        }
        else if(!$this->Business->permission($business_id, BUSINESS_PERMISSION_MANAGE_SUBPAGE, $business['Business']['moo_permissions']))
        {
            $this->_redirectError($this->Business->permissionMessage(), '/pages/error');
        }
        else
        {
            $branch = $this->Business->getOnlyBusiness($branch_id);
            $redirect = '/businesses/dashboard/branches/'.$business_id;
            if($my == 'my')
            {
                $redirect = '/businesses/my/';
            }
            if($this->Business->deleteBusiness($branch_id))
            {
                //update business branch counter
                $this->Business->updateBusinessBranchCounter($branch['Business']['parent_id']);
                
                //delete activity
                $this->Business->deleteBranchActivity($branch['Business']['parent_id'], $branch_id);
            
                $this->_redirectSuccess(__d('business', 'Sub page has been deleted'), $redirect);
            }
            $this->_redirectError(__d('business', 'Can not delete branch, please try again'), $redirect);
        }
    }
    
    public function load_business_branches($business_id)
    {
        $this->autoRender = false;
        $page = (!empty($this->request->named['page'])) ? $this->request->named['page'] : 1;
        
        //branches
        $branches = $this->Business->loadBracnches($business_id, $page);
        
        $this->set(array(
            'branches' => $branches,
            'more_url' => '/business_branch/load_business_branches/'.$business_id.'/page:'.($page + 1)
        ));
        $this->render('/Elements/lists/branch_list');
    }
}