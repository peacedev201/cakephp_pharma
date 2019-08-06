<?php
class Business extends BusinessAppModel 
{
    public $validationDomain = 'business';
    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $mBusinessLocation = MooCore::getInstance()->getModel('Business.BusinessLocation');
        $this->location_seoname = $mBusinessLocation->getDefalutLocationSeoName();
    }
    
    public $mooFields = array(
        'title','href','plugin','type','url','seo', 'thumb', 'hrefshare',
        'hrefreview','hrefphoto', 'hrefproduct', 'hrefcontact','hrefbranch','hrefcheckin','hreffollower', 'permissions', 'urlreview');
    public $recursive = 2;
    public $validate = array(   
        'client_name' =>   array(   
            'rule' => 'notBlank',
            'message' => 'Please select business type'
        ),
        'name' =>   array(   
            'rule' => 'notBlank',
            'message' => 'Please provide name'
        ),
        'logo' =>   array(   
            'rule' => 'notBlank',
            'message' => 'Please upload logo'
        ),
        'address' =>   array(   
            'rule' => 'notBlank',
            'message' => 'Please provide address'
        ),
        'email' =>   array(   
            'rule' => array('email'),
            'message' => 'Invalid email'
        ),
    );
    
    public $actsAs = array(
        'Hashtag' => array(
            'field_created_get_hashtag' => 'description',
            'field_updated_get_hashtag' => 'description',
        ),
        'Storage.Storage' => array(
            'type' => array('businesses' => 'logo', 'business_covers' => 'cover'),
        ),
    );
    
    public $belongsTo = array( 
        'User'  => array('counterCache' => true),
    );
    
    public function getHref($row) 
    {
        $request = Router::getRequest();
        if(isset($row['name']) && isset($row['id']))
        {
            $id = isset($row['parent_id']) && $row['parent_id'] > 0 ? $row['id'].'_'.$row['parent_id'] : $row['id'];
            return $request->base.'/businesses/view/'.$id.'/'.seoUrl($row['name']);
        }
        return '';
    }
    
    public function getHrefShare($row)
    {
        if(isset($row['name']) && isset($row['id']))
        {
            return Router::url('/', true).'share/ajax_share/Business_Business/id:'.$row['id'].'/type:business_item_detail_share';
        }
        return '';
    }
    
    public function getHrefReview($row) 
    {
        $request = Router::getRequest();
        if(isset($row['name']) && isset($row['id']))
        {
            $id = isset($row['parent_id']) && $row['parent_id'] > 0 ? $row['id'].'_'.$row['parent_id'] : $row['id'];
            return $request->base.'/'.BUSINESS_DETAIL_LINK_REVIEW.'/'.seoUrl($row['name']).'-'.$id;
        }
        return '';
    }
    
    public function getUrlReview($row) 
    {
        $request = Router::getRequest();
        if(isset($row['name']) && isset($row['id']))
        {
            $id = isset($row['parent_id']) && $row['parent_id'] > 0 ? $row['id'].'_'.$row['parent_id'] : $row['id'];
            return '/'.BUSINESS_DETAIL_LINK_REVIEW.'/'.seoUrl($row['name']).'-'.$id;
        }
        return '';
    }
    
    public function getHrefPhoto($row) 
    {
        $request = Router::getRequest();
        if(isset($row['name']) && isset($row['id']))
        {
            $id = isset($row['parent_id']) && $row['parent_id'] > 0 ? $row['id'].'_'.$row['parent_id'] : $row['id'];
            return $request->base.'/'.BUSINESS_DETAIL_LINK_PHOTO.'/'.seoUrl($row['name']).'-'.$id;
        }
        return '';
    }

    public function getHrefProduct($row) 
    {
        $request = Router::getRequest();
        if(isset($row['name']) && isset($row['id']))
        {
            $id = isset($row['parent_id']) && $row['parent_id'] > 0 ? $row['id'].'_'.$row['parent_id'] : $row['id'];
            return $request->base.'/'.BUSINESS_DETAIL_LINK_PRODUCT.'/'.seoUrl($row['name']).'-'.$id;
        }
        return '';
    }
    
    public function getHrefContact($row) 
    {
        $request = Router::getRequest();
        if(isset($row['name']) && isset($row['id']))
        {
            $id = isset($row['parent_id']) && $row['parent_id'] > 0 ? $row['id'].'_'.$row['parent_id'] : $row['id'];
            return $request->base.'/'.BUSINESS_DETAIL_LINK_CONTACT.'/'.seoUrl($row['name']).'-'.$id;
        }
        return '';
    }
    
    public function getHrefBranch($row) 
    {
        $request = Router::getRequest();
        if(isset($row['name']) && isset($row['id']))
        {
            return $request->base.'/'.BUSINESS_DETAIL_LINK_BRANCH.'/'.seoUrl($row['name']).'-'.$row['id'];
        }
        return '';
    }
    
    public function getHrefCheckin($row) 
    {
        $request = Router::getRequest();
        if(isset($row['name']) && isset($row['id']))
        {
            $id = isset($row['parent_id']) && $row['parent_id'] > 0 ? $row['id'].'_'.$row['parent_id'] : $row['id'];
            return $request->base.'/'.BUSINESS_DETAIL_LINK_CHECKIN.'/'.seoUrl($row['name']).'-'.$id;
        }
        return '';
    }
    
    public function getHrefFollower($row) 
    {
        $request = Router::getRequest();
        if(isset($row['name']) && isset($row['id']))
        {
            $id = isset($row['parent_id']) && $row['parent_id'] > 0 ? $row['id'].'_'.$row['parent_id'] : $row['id'];
            return $request->base.'/'.BUSINESS_DETAIL_LINK_FOLLOWER.'/'.seoUrl($row['name']).'-'.$id;
        }
        return '';
    }
    
    public function getUrl($row) 
    {
        $request = Router::getRequest();
        if(isset($row['name']) && isset($row['id']))
        {
            $id = isset($row['parent_id']) && $row['parent_id'] > 0 ? $row['id'].'_'.$row['parent_id'] : $row['id'];
            return '/businesses/view/'.$id.'/'.seoUrl($row['name']);
        }
        return '';
    }
    
    public function getTitle(&$row) 
    {
        $request = Router::getRequest();
        if(isset($row['name']))
        {
            return $row['name'];
        }
        return '';
    }
    
    public function getSeo($row) 
    {
        if(isset($row['name']))
        {
            return seoUrl($row['name']).'-'.$row['id'];
        }
        return '';
    }
    
    public function getPermissions($row) 
    {
        return !empty($row['permissions']) ? explode(',', $row['permissions']) : array();
    }
    
    public function activeMenu($active)
    {
        $mCoreMenuItem = MooCore::getInstance()->getModel('Menu.CoreMenuItem');
        $menu = $mCoreMenuItem->findByUrl('/businesses');
        if($menu != null)
        {
            $mCoreMenuItem->id = $menu['CoreMenuItem']['id'];
            $mCoreMenuItem->save(array(
                'is_active' => $active
            ));
        }
    }
    
    public function loadManageBusinessList($obj, $keyword = '', $status = '')
    {
        $cond = array('Business.claim_id' => 0);

        if($keyword != '')
        {
            $keyword = str_replace("'", "\'", $keyword);
            $cond[] = "Business.name LIKE '%$keyword%'";
        }
        if($status != '')
        {
            $keyword = str_replace("'", "\'", $status);
            $cond[] = "Business.status LIKE '%$status%'";
        }
        $obj->Paginator->settings = array(
            'conditions' => $cond,
            'order' => array('Business.id' => 'DESC'),
            'limit' => 10,
        );
        $data = $obj->paginate('Business');
        return $this->parseParentData($data);
    }
    
    public function loadMyBusiness($user_id, $page = 1, $keyword = '', $bOwner = true)
    {
        $mBusinessAdmin = MooCore::getInstance()->getModel('Business.BusinessAdmin');

        //find all business id of current user who is set as admin
        $business_ids = $mBusinessAdmin->find('list', array(
            'conditions' => array(
                'BusinessAdmin.user_id' => $user_id
            ),
            'fields' => array('BusinessAdmin.business_id')
        ));
        
        //find my business
        $cond = array();
        
        if($business_ids != null)
        {
            $business_ids = implode(',', $business_ids);
            $cond[] = '(Business.user_id = '.$user_id.' OR Business.id IN('.$business_ids.'))';
        }
        else
        {
            $cond['Business.user_id'] = $user_id;
        }

        if($keyword != '')
        {
            $keyword = str_replace("'", "\'", $keyword);
            $cond[] = "(Business.name LIKE '%$keyword%'')";
        }
        
        if(!$bOwner){
            $cond['Business.claim_id'] = 0;
            $cond['Business.status'] = BUSINESS_STATUS_APPROVED;
        }
        
        $data = $this->find('all', array(
            'conditions' => $cond,
            'order' => array('Business.id' => 'DESC'),
            'limit' => Configure::read('Business.business_search_item_per_page'),
            'page' => $page
        ));
        return $this->parseParentData($data);
    }
    
    public function loadBusinessBranches($obj, $business_id)
    {
        $cond = array(
            'Business.parent_id' => $business_id,
        );

        /*if($keyword != '')
        {
            $keyword = str_replace("'", "\'", $keyword);
            $cond[] = "(Business.name LIKE '%$keyword%'')";
        }*/
        $obj->Paginator->settings = array(
            'conditions' => $cond,
            'order' => array('Business.id' => 'DESC'),
            'limit' => 10,
        );
        return $obj->paginate('Business');
    }
    
    public function deleteBusiness($id)
    {
        $mBusinessCategory = MooCore::getInstance()->getModel('Business.BusinessCategory');
        $mBusinessCategoryItem = MooCore::getInstance()->getModel('Business.BusinessCategoryItem');
        $mBusinessReview = MooCore::getInstance()->getModel('Business.BusinessReview');
        $mBusinessAdmin = MooCore::getInstance()->getModel('Business.BusinessAdmin');
        $mBusinessFollow = MooCore::getInstance()->getModel('Business.BusinessFollow');
        $mBusinessPaid = MooCore::getInstance()->getModel('Business.BusinessPaid');
        $mBusinessLocation = MooCore::getInstance()->getModel('Business.BusinessLocation');
        $mBusinessCheckin = MooCore::getInstance()->getModel('Business.BusinessCheckin');
		$mBusinessPaymentType = MooCore::getInstance()->getModel('Business.BusinessPaymentType');
        $mBusinessTime = MooCore::getInstance()->getModel('Business.BusinessTime');
        $mBusinessFavourite = MooCore::getInstance()->getModel('Business.BusinessFavourite');
        $mAlbum = MooCore::getInstance()->getModel('Photo.Album');
        $mActivity = MooCore::getInstance()->getModel('Activity');
        $business = $this->getOnlyBusiness($id);
        $old_cat = $mBusinessCategoryItem->getListCategoryId($id);
        if($this->delete($id))
        {
            //delete albums photos
            $album = $mAlbum->findById($business['Business']['album_id']);
            if($album != null)
            {
                $mAlbum->deleteAlbum($album);
            }
            
            //find branch album
            $branch_albums = $this->find('list', array(
                'conditions' => array(
                    'Business.parent_id' => $id
                ),
                'fields' => array('Business.album_id')
            ));
            
            //delete all branch album
            if($branch_albums != null)
            {
                foreach($branch_albums as $branch_album)
                {
                    $album = $mAlbum->findById($branch_album);
                    if($album != null)
                    {
                        $mAlbum->deleteAlbum($album);
                    }
                }
            }
            
            //delete all reviews
            $mBusinessReview->deleteReview(null, $id);
            
            //delete all admins
            $mBusinessAdmin->deleteAdmin($id);
            
            //delete all followers
            $mBusinessFollow->deleteFollower($id);
            
            // delete all paids
            $mBusinessPaid->deletePaid($id);
            //delete all checkin
            $mBusinessCheckin->deleteAll(array(
                'BusinessCheckin.business_id' => $id
            ));
            
            //delete activity
            /*$this->deleteBusinessActivity($id);
            $this->deleteVerifyActivity($id);
            $this->deleteCreateBusinessActivity($id);*/
            $mActivity->deleteAll(array(
                "(Activity.type = '".BUSINESS_ACTIVITY_TYPE."' OR Activity.plugin = 'Business')",
                'Activity.target_id' => $id,
            ));
			$create_activity = $mActivity->find('first', array(
                'conditions' => array(
                    'Activity.type' => BUSINESS_ACTIVITY_TYPE_USER,
                    'Activity.item_id' => $id,
                    'Activity.action' => BUSINESS_ACTIVITY_CREATE_BUSINESS_ACTION,
                    'Activity.plugin' => 'Business',
                )
            ));
            if($create_activity != null)
            {
                $mActivity->deleteAll(array(
                    'Activity.id' => $create_activity['Activity']['id']
                ));
                $mActivity->deleteAll(array(
                    'Activity.parent_id' => $create_activity['Activity']['id']
                ));
            }
            
            //delete claimed business
            $this->deleteAll(array(
                'Business.claim_id' => $id
            ));
			
			//delete business types
            $mBusinessPaymentType->deleteAll(array(
                'BusinessPaymentType.business_id' => $id
            ));
            
            //delete business times
            $mBusinessTime->deleteAll(array(
                'BusinessTime.business_id' => $id
            ));
            
			//delete category item
            $mBusinessCategoryItem->deleteAll(array(
                'BusinessCategoryItem.business_id' => $id
            ));
            
            //delete favourite item
            $mBusinessFavourite->deleteAll(array(
                'BusinessFavourite.business_id' => $id
            ));
            
            //delete branch
            $branches = $this->find('list', array(
                'conditions' => array('Business.parent_id' => $id),
                'fields' => array('Business.id', 'Business.id')
            ));
            if($branches != null)
            {
                foreach($branches as $branch_id)
                {
                    $this->deleteBusiness($branch_id);
                }
            }
            return true;
        }
        return false;
    }
    
    public function changeStatus($status, $id)
    {
        $this->id = $id;
        return $this->save(array(
            'status' => $status
        ));
    }
    
    public function isBusinessExist($id, $user_id = null, $parent_id = null, $block_user = true)
    {
        $cond = array(
            'Business.id' => $id
        );
        if($user_id > 0)
        {
            $cond['Business.user_id'] = $user_id;
        }
        if(is_int($parent_id))
        {
            $cond['Business.parent_id'] = $parent_id;
        }
        return $this->hasAny($cond);
    }
    
    public function isClaimBusinessExist($iClaimId, $iUserId)
    {
        $cond = array(
            'Business.claim_id' => $iClaimId
        );
        if($iUserId > 0)
        {
            $cond['Business.user_id'] = $iUserId;
        }
        return $this->hasAny($cond);
    }
    
    public function getListDayInWeek()
    {
        return array(
            'monday' => __d('business', 'Monday'),
            'tuesday' => __d('business', 'Tuesday'),
            'wednesday' => __d('business', 'Wednesday'),
            'thursday' => __d('business', 'Thursday'),
            'friday' => __d('business', 'Friday'),
            'saturday' => __d('business', 'Saturday'),
            'sunday' => __d('business', 'Sunday'),
        );
    }

    public function getListTimeOpen()
    {
        return array(
            '0 00:00' => __d('business', '12:00 am (midnight)'),
            '0 00:30' => __d('business', '12:30 am'),
            '0 01:00' => __d('business', '1:00 am'),
            '0 01:30' => __d('business', '1:30 am'),
            '0 02:00' => __d('business', '2:00 am'),
            '0 02:30' => __d('business', '2:30 am'),
            '0 03:00' => __d('business', '3:00 am'),
            '0 03:30' => __d('business', '3:30 am'),
            '0 04:00' => __d('business', '4:00 am'),
            '0 04:30' => __d('business', '4:30 am'),
            '0 05:00' => __d('business', '5:00 am'),
            '0 05:30' => __d('business', '5:30 am'),
            '0 06:00' => __d('business', '6:00 am'),
            '0 06:30' => __d('business', '6:30 am'),
            '0 07:00' => __d('business', '7:00 am'),
            '0 07:30' => __d('business', '7:30 am'),
            '0 08:00' => __d('business', '8:00 am'),
            '0 08:30' => __d('business', '8:30 am'),
            '0 09:00' => __d('business', '9:00 am'),
            '0 09:30' => __d('business', '9:30 am'),
            '0 10:00' => __d('business', '10:00 am'),
            '0 10:30' => __d('business', '10:30 am'),
            '0 11:00' => __d('business', '11:00 am'),
            '0 11:30' => __d('business', '11:30 am'),
            '0 12:00' => __d('business', '12:00 pm (noon)'),
            '0 12:30' => __d('business', '12:30 pm'),
            '0 13:00' => __d('business', '1:00 pm'),
            '0 13:30' => __d('business', '1:30 pm'),
            '0 14:00' => __d('business', '2:00 pm'),
            '0 14:30' => __d('business', '2:30 pm'),
            '0 15:00' => __d('business', '3:00 pm'),
            '0 15:30' => __d('business', '3:30 pm'),
            '0 16:00' => __d('business', '4:00 pm'),
            '0 16:30' => __d('business', '4:30 pm'),
            '0 17:00' => __d('business', '5:00 pm'),
            '0 17:30' => __d('business', '5:30 pm'),
            '0 18:00' => __d('business', '6:00 pm'),
            '0 18:30' => __d('business', '6:30 pm'),
            '0 19:00' => __d('business', '7:00 pm'),
            '0 19:30' => __d('business', '7:30 pm'),
            '0 20:00' => __d('business', '8:00 pm'),
            '0 20:30' => __d('business', '8:30 pm'),
            '0 21:00' => __d('business', '9:00 pm'),
            '0 21:30' => __d('business', '9:30 pm'),
            '0 22:00' => __d('business', '10:00 pm'),
            '0 22:30' => __d('business', '10:30 pm'),
            '0 23:00' => __d('business', '11:00 pm'),
            '0 23:30' => __d('business', '11:30 pm')
        );
    }

    public function getListTimeClose()
    {
        return array(
            '0 00:30' => __d('business', '12:30 am'),
            '0 01:00' => __d('business', '1:00 am'),
            '0 01:30' => __d('business', '1:30 am'),
            '0 02:00' => __d('business', '2:00 am'),
            '0 02:30' => __d('business', '2:30 am'),
            '0 03:00' => __d('business', '3:00 am'),
            '0 03:30' => __d('business', '3:30 am'),
            '0 04:00' => __d('business', '4:00 am'),
            '0 04:30' => __d('business', '4:30 am'),
            '0 05:00' => __d('business', '5:00 am'),
            '0 05:30' => __d('business', '5:30 am'),
            '0 06:00' => __d('business', '6:00 am'),
            '0 06:30' => __d('business', '6:30 am'),
            '0 07:00' => __d('business', '7:00 am'),
            '0 07:30' => __d('business', '7:30 am'),
            '0 08:00' => __d('business', '8:00 am'),
            '0 08:30' => __d('business', '8:30 am'),
            '0 09:00' => __d('business', '9:00 am'),
            '0 09:30' => __d('business', '9:30 am'),
            '0 10:00' => __d('business', '10:00 am'),
            '0 10:30' => __d('business', '10:30 am'),
            '0 11:00' => __d('business', '11:00 am'),
            '0 11:30' => __d('business', '11:30 am'),
            '0 12:00' => __d('business', '12:00 pm (noon)'),
            '0 12:30' => __d('business', '12:30 pm'),
            '0 13:00' => __d('business', '1:00 pm'),
            '0 13:30' => __d('business', '1:30 pm'),
            '0 14:00' => __d('business', '2:00 pm'),
            '0 14:30' => __d('business', '2:30 pm'),
            '0 15:00' => __d('business', '3:00 pm'),
            '0 15:30' => __d('business', '3:30 pm'),
            '0 16:00' => __d('business', '4:00 pm'),
            '0 16:30' => __d('business', '4:30 pm'),
            '0 17:00' => __d('business', '5:00 pm'),
            '0 17:30' => __d('business', '5:30 pm'),
            '0 18:00' => __d('business', '6:00 pm'),
            '0 18:30' => __d('business', '6:30 pm'),
            '0 19:00' => __d('business', '7:00 pm'),
            '0 19:30' => __d('business', '7:30 pm'),
            '0 20:00' => __d('business', '8:00 pm'),
            '0 20:30' => __d('business', '8:30 pm'),
            '0 21:00' => __d('business', '9:00 pm'),
            '0 21:30' => __d('business', '9:30 pm'),
            '0 22:00' => __d('business', '10:00 pm'),
            '0 22:30' => __d('business', '10:30 pm'),
            '0 23:00' => __d('business', '11:00 pm'),
            '0 23:30' => __d('business', '11:30 pm'),
            '1 00:00' => __d('business', '12:00 am (midnight next day)'),
            '1 00:30' => __d('business', '12:30 am (next day)'),
            '1 01:00' => __d('business', '1:00 am (next day)'),
            '1 01:30' => __d('business', '1:30 am (next day)'),
            '1 02:00' => __d('business', '2:00 am (next day)'),
            '1 02:30' => __d('business', '2:30 am (next day)'),
            '1 03:00' => __d('business', '3:00 am (next day)'),
            '1 03:30' => __d('business', '3:30 am (next day)'),
            '1 04:00' => __d('business', '4:00 am (next day)'),
            '1 04:30' => __d('business', '4:30 am (next day)'),
            '1 05:00' => __d('business', '5:00 am (next day)'),
            '1 05:30' => __d('business', '5:30 am (next day)'),
            '1 06:00' => __d('business', '6:00 am (next day)')
        );
    }
    
    public function getBusiness($id = null, $user_id = null, $parent_id = 0)
    {
        $this->bindModel(array(
            'belongsTo' => array(
                'BusinessLocation' => array('className' => 'Business.BusinessLocation'),
                'BusinessPackage'  => array('className' => 'Business.BusinessPackage'),
				'BusinessType'  => array('className' => 'Business.BusinessType'),
            ),
            'hasMany' => array(
                'BusinessTime' => array('className' => 'Business.BusinessTime'),
            ),
            'hasAndBelongsToMany' => array(
                'BusinessCategory' => array(
                    'className' => 'Business.BusinessCategory',
                    'counterCache' => true,
                    'joinTable' => 'business_category_items',
                    'foreignKey' => 'business_id',
                    'associationForeignKey' => 'business_category_id',
                ),
                'BusinessPayment' => array(
                    'className' => 'Business.BusinessPayment',
                    'counterCache' => true,
                    'joinTable' => 'business_payment_types',
                    'foreignKey' => 'business_id',
                    'associationForeignKey' => 'business_payment_id',
                )
            )
        ));
        
        $cond = array(
            'Business.parent_id' => $parent_id,
        );
        $find = 'all';
        if($id > 0)
        {
            $cond['Business.id'] = $id;
            $find = 'first';
        }
        if($user_id > 0)
        {
            $cond['Business.user_id'] = $user_id;
        }
        
        //normal
        $data = $this->find($find, array(
            'conditions' => $cond
        ));
        if($id > 0)
        {
            $data = $this->parseCategoryLanguage($data);
            return $this->parseBusinessData($data);
        }
        else if($data != null)
        {
            $result = null;
            foreach($data as $k => $item)
            {
                $result[] = $this->parseBusinessData($item);
            }
            return $result;
        }
        return null;
    }
    
    public function getBusinessByClaimed($iClaimId = null)
    {
        if(empty($iClaimId))
        {
            return null;
        }
        
        return $this->find('all', array('conditions' => array('Business.claim_id' => $iClaimId)));
    }
    
    public function getBusinessPaging($obj = null, $params = null, $sort_by = null, $block_user = true)
    {
        $mBusinessCategory = MooCore::getInstance()->getModel('Business.BusinessCategory');
        $mBusinessCategoryItem = MooCore::getInstance()->getModel('Business.BusinessCategoryItem');
        $mBusinessLocation = MooCore::getInstance()->getModel('Business.BusinessLocation');
        $mBusinessCategory->recursive = -1;
        $best_match_order = "";
        
        $cond = array(
            //'Business.parent_id' => 0,
            'Business.status' => BUSINESS_STATUS_APPROVED,
            'Business.claim_id' => 0
        );
        
        //search by keyword
        if(!empty($params['keyword']))
        {
            $keyword = $params['keyword'];
            if((empty($advanced_search['by_cat_name']) && empty($advanced_search['by_listing'])) || 
               (!empty($advanced_search['by_listing']) && $advanced_search['by_listing'] == 1))
            {
                $keyword = trim($keyword);
                $keyword = str_replace("'", "\\'", $keyword);
                $temp_keyword = explode(" ", $keyword);
                if($temp_keyword != null)
                {
                    $best_match_order = "(Business.name LIKE '$keyword%') + ";
                    $cond['OR'][] = "Business.name LIKE '%$keyword%'";
                    foreach($temp_keyword as $index => $kw)
                    {
                        $cond['OR'][] = "Business.name LIKE '$kw%'";
                        $best_match_order .= "(Business.name LIKE '$kw%')";
                        $best_match_order .= $index < count($temp_keyword) -1 ? " + " : "";
                    }
                }
                $cond['OR'][] = "Business.description LIKE '%".htmlentities($keyword)."%'";
                $cond['OR'][] = "Business.company_number = '$keyword'";
                $cond['OR'][] = "Business.postal_code = '$keyword'";
            }
            
            //find by category
            $cond['OR'][] = "Business.id IN(SELECT business_id FROM ".$this->tablePrefix."business_category_items WHERE business_category_id IN(SELECT id FROM ".$this->tablePrefix."business_categories WHERE name = '$keyword' AND enable = 1))";
        }
      
        //search by location
        if(!empty($params['keyword_location']) && empty($params['distance']))
        {
            $location = $mBusinessLocation->findByName($params['keyword_location']);
            if($location != null)
            {
                $list_ids = $mBusinessLocation->find('list', array(
                    'conditions' => array(
                        'BusinessLocation.parent_id' => $location['BusinessLocation']['id']
                    ),
                    'fields' => array('BusinessLocation.id')
                ));
                $list_ids[] = $location['BusinessLocation']['id'];
                $cond['Business.business_location_id'] = $list_ids;
            }
            else 
            {
                $cond['Business.business_location_id'] = 0;
            }
        }
		if(isset($params['location_id']))
        {
			$cond['Business.business_location_id'] = $params['location_id'];
		}

        //search by category
        if(!empty($params['category_id']))
        {
            $cat_ids = $mBusinessCategory->getIdList($params['category_id']);
            $cond[] = 'Business.id IN (SELECT business_id FROM '.$this->tablePrefix.'business_category_items WHERE business_category_id IN('. implode(',', $cat_ids).'))';
        }
            
        //search by distance
        if (!empty($params['distance']) && !empty($params['keyword_location'])) {
            $around_params = $this->findBusinessAround($params['distance'], null, false, $params['keyword_location']);
            $cond[] = "Business.id IN(".$around_params['query'].")";
        }

        //sort
        $show_distance = false;
        if(isset($around_params['virtual_field']))
        {
            $show_distance = true;
        }
        switch($sort_by)
        {
            case BUSINESS_SEARCH_BY_DATE:
                $order = array('Business.featured' => 'DESC', 'Business.created' => 'DESC');
                break;
            case BUSINESS_SEARCH_BY_RATING:
                $order = array('Business.featured' => 'DESC', 'Business.total_score' => 'DESC');
                break;
            default :
                if(isset($around_params['virtual_field']))
                {
                    $order = array('Business.featured' => 'DESC', 'Business.distance' => 'ASC');
                }
                else
                {
                    $order = $best_match_order != "" ? "Business.featured DESC, ($best_match_order) DESC" : array("Business.featured" => "DESC");
                }
        }

        //paging
        try{
            $obj->Paginator->settings = array(
                'conditions' => $cond,
                'order' => $order,
                'limit' => Configure::read('Business.business_search_item_per_page'),
            );
            $data = $obj->paginate('Business');
            $data = $this->parseParentData($data);
            return array($show_distance, $this->parseBusinessData($data));
        } catch (Exception $ex) {
            return array(array(), array());
        }
    }
    
    public function getAllBusinessPaging($obj, $block_user = true){
        $cond = array(
            'Business.status' => BUSINESS_STATUS_APPROVED,
            'Business.claim_id' => 0
        );
        
        //paging
        try{
            $obj->Paginator->settings = array(
                'conditions' => $cond,
                'order' => array('Business.featured' => 'DESC', 'Business.id' => 'DESC'),
                'limit' => Configure::read('Business.business_search_item_per_page'),
            );
            $data = $obj->paginate('Business');
            return $this->parseParentData($data);
        } catch (Exception $ex) {
            return array(array(), array());
        }
    }
    
    private function parseCategoryLanguage($data)
    {
        if($data != null)
        {
            $single = false;
            if(isset($data['Business']))
            {
                $temp = array($data);
                $data = $temp;
                $single = true;
            }
            foreach($data as $k => $item)
            {
                if(empty($item['BusinessCategory'][0]['nameTranslation']))
                {
                    continue;
                }
                foreach ($item['BusinessCategory'][0]['nameTranslation'] as $nameTranslation) 
                {
                    if ($nameTranslation['locale'] == Configure::read('Config.language')) 
                    {
                        $data[$k]['BusinessCategory'][0]['name'] = $nameTranslation['content'];
                        $data[$k]['BusinessCategory'][0]['moo_url'] = '/business_search/'.seoUrl($nameTranslation['content']).'/'.$item['BusinessCategory'][0]['id'];
                        $data[$k]['BusinessCategory'][0]['moo_href'] = Router::url('/').'business_search/'.seoUrl($nameTranslation['content']).'/'.$item['BusinessCategory'][0]['id'];
                        break;;
                    }
                }
            }
            if($single)
            {
                return $data[0];
            }
        }
        return $data;
    }

    public function parseParentData($data)
    {
        if($data != null)
        {
			$mUser = MooCore::getInstance()->getModel('User');
			$mBusinessPackage = MooCore::getInstance()->getModel('Business.BusinessPackage');
            $mBusinessCategory = MooCore::getInstance()->getModel('Business.BusinessCategory');
            $mBusinessCategoryItem = MooCore::getInstance()->getModel('Business.BusinessCategoryItem');
            foreach($data as $k => $item)
            {
                $parent = $item['Business']['parent_id'] > 0 ? $this->findById($item['Business']['parent_id']) : null;
                $data[$k]['Business']['moo_parent'] = !empty($parent['Business']) ? $parent['Business'] : null;
                $data[$k]['Business']['moo_parent_cat'] = !empty($parent['BusinessCategory']) ? $parent['BusinessCategory'] : null;
				$user = $mUser->findById($item['Business']['user_id']);
				$package = $mBusinessPackage->findById($item['Business']['business_package_id']);
                $business_category_ids = $mBusinessCategoryItem->find('list', array(
                    'conditions' => array(
                        'BusinessCategoryItem.business_id' => $item['Business']['id']
                    ),
                    'fields' => array('BusinessCategoryItem.id', 'BusinessCategoryItem.business_category_id')
                ));
                $business_categories = $mBusinessCategory->find('all', array(
                    'conditions' => array(
                        'BusinessCategory.id' => $business_category_ids
                    )
                ));
				$data[$k]['User'] = !empty($user['User']) ? $user['User'] : array();
				$data[$k]['BusinessPackage'] = !empty($package['BusinessPackage']) ? $package['BusinessPackage'] : array();
                $data[$k]['BusinessCategory'] = array();
                if($business_categories != null)
                {
                    foreach ($business_categories as $business_category)
                    {
                        $data[$k]['BusinessCategory'][] = $business_category['BusinessCategory'];
                    }
                }
            }
            $data = $this->parseCategoryLanguage($data);
        }
        return $data;
    }
    
    public function findBusinessAround($distance = 0, $cond = null, $query_location = false, $address = null) {
        $mBusinessLocation = MooCore::getInstance()->getModel('Business.BusinessLocation');
        $mBusinessLocationSearch = MooCore::getInstance()->getModel('Business.BusinessLocationSearch');
        $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
        $ignore_index = "claim_id, status,parent_id";

        $search = $mBusinessLocationSearch->findByAddress($address);
        if ($search == null) {
            $addressDetail = $businessHelper->getAddressDetail(null, null, $address);
            $lng = $addressDetail['lng'];
            $lat = $addressDetail['lat'];
            $mBusinessLocation->registerLocationKeyword($address, $lat, $lng, $addressDetail['postal_code'], $addressDetail['country'], $addressDetail['region'], $addressDetail['city']);

            //set region id if exist
            if($addressDetail['region'] != null)
            {
                $location = $mBusinessLocation->findByName($addressDetail['region']);
                /*if($location != null)
                {
                    $mBusinessLocation->setRegionId($location['BusinessLocation']['id']);
                }*/
            }
        } else {
            $lng = $search['BusinessLocationSearch']['lng'];
            $lat = $search['BusinessLocationSearch']['lat'];

            //set region id if exist
            $location = $mBusinessLocation->findByName($search['BusinessLocationSearch']['region']);
            /*if($location != null)
            {
                $mBusinessLocation->setRegionId($location['BusinessLocation']['id']);
            }*/
        }

        if ((int) $distance == 0) {
            $distance = Configure::read('Business.business_search_default_distance');
        }

        $query = 
            "SELECT id, 
                111.045 * DEGREES(ACOS(COS(RADIANS(latpoint)) * COS(RADIANS(lat)) * COS(RADIANS(longpoint) - RADIANS(lng)) + SIN(RADIANS(latpoint)) * SIN(RADIANS(lat)))) AS distance
            FROM ".$this->tablePrefix."businesses 
            JOIN(SELECT $lat AS latpoint, $lng AS longpoint, $distance AS r) AS p  
            WHERE lat BETWEEN latpoint - (r / 111.045) AND latpoint + (r / 111.045) AND 
                lng BETWEEN longpoint - (r / (111.045 * COS(RADIANS(latpoint)))) AND longpoint + (r / (111.045 * COS(RADIANS(latpoint)))) AND 
                claim_id = 0 AND 
                status = '".BUSINESS_STATUS_APPROVED."' $cond ";
        if($query_location)
        {
            return array(
                'query' => $query
            );
        }
        $query = "SELECT id "
            . "FROM ($query) Business "
            . "WHERE distance <= ".$distance;
        return array(
            'query' => $query
        );
    }
    
    private function parseBusinessData($data)
    {
        //get full business category path
        if(!empty($data['BusinessCategory']))
        {
            $mBusinessCategory = MooCore::getInstance()->getModel('Business.BusinessCategory');
            foreach($data['BusinessCategory'] as $k => $item)
            {
                $data['BusinessCategory'][$k]['path_name'] = $mBusinessCategory->getCategoryPath($item['id']);
            }
        }
        
        //get selected payment array list
        if(!empty($data['BusinessPayment']))
        {
            $paymentSelect = array();
            foreach($data['BusinessPayment'] as $item)
            {
                $paymentSelect[] = $item['id'];
            }
            $data['PaymentSelect'] = $paymentSelect;
        }
        
        //check working time
        if(!empty($data['BusinessTime']))
        {
            $cuser = MooCore::getInstance()->getViewer();
            $user_timezone = !empty($cuser['User']['timezone']) ? $cuser['User']['timezone'] : $data['Business']['timezone'];
            $userTimezone = new DateTimeZone($user_timezone);
            $businessTimezone = new DateTimeZone($data['Business']['timezone']);
            $dt = new DateTime(date('Y-m-d H:i:s'), $userTimezone);
            $offset = $businessTimezone->getOffset($dt);
            $myInterval = DateInterval::createFromDateString((string)$offset . 'seconds');
            $dt->add($myInterval);
            $today = $dt->format('l');
            $curTime = $dt->format('H:i');
            foreach($data['BusinessTime'] as $item)
            {
                $data['Business']['now_open'] = false;
                $data['Business']['open_today'] = null;
                if(strtolower($today) == $item['day'])
                {
                    $time_close = $item['next_day'] ? (strtotime($item['time_close']) + 24 * 60 * 60) : strtotime($item['time_close']);
                    if(strtotime($item['time_open']) <= strtotime($curTime) &&
                       $time_close >= strtotime($curTime))
                    {
                        $data['Business']['open_today'] = $item;
                        $data['Business']['now_open'] = true;
                        break;
                    }
                }
            }
        }
        return $data;
    }
    
    public function afterPaymentComplete($paid) {
        $mBusinessPackage = MooCore::getInstance()->getModel('Business.BusinessPackage');
        $package = $mBusinessPackage->findById($paid['business_package_id']);
        if($paid['pay_type'] == 'featured_package') {
            $this->updateAll(
                array('Business.featured' => 1),
                array('Business.id' => $paid['business_id'])
            );
        }
        if($paid['pay_type'] == 'business_package') {
            if($this->updateAll(
                array(
                    'Business.business_package_id' => $paid['business_package_id']
                ),
                array('Business.id' => $paid['business_id'])
            ))
            {
                $this->relatedBusinessData($paid['business_id'], $paid['business_package_id']);
            }
        }
    }
    
    public function onDowngrade($business_id){
        $mBusinessPackage = MooCore::getInstance()->getModel('Business.BusinessPackage');
        $package_default = $mBusinessPackage->getDefaultPackage() ;
        if($this->updateAll(
            array(
                'Business.business_package_id' => $package_default
            ), 
            array('Business.id' => $business_id)
        ))
        {
            $this->relatedBusinessData($business_id, $package_default);
        }
    }
    
    public function relatedBusinessData($business_id, $package_id = null, $enable = null)
    {
        //downgrade photo
        //$this->enablePackagePhoto($business_id);
    }
    
    //check allow business module - depend on package
    public function isAllowModule($business_id, $name)
    {
        $mBusinessPackage = MooCore::getInstance()->getModel('Business.BusinessPackage');
        $business = $this->findById($business_id);
        $business_package = $mBusinessPackage->findById($business['Business']['business_package_id']);
        if(!empty($business_package['BusinessPackage']['options']))
        {
            $options = json_decode($business_package['BusinessPackage']['options']);
            //group of check
            if(is_array($name))
            {
                $results = array();
                foreach($name as $k => $item)
                {
                    $results[$k] = false;
                    if($options != null && in_array($item, $options))
                    {
                        $results[$k] = true;
                    }
                }
                return $results;
            }
            else //single check
            {
                if($options != null && in_array($name, $options))
                {
                    return true;
                }
            }
        }
        return false;
    }
    
    public function applyDefaultPackage($business_id)
    {
        $mBusinessPackage = MooCore::getInstance()->getModel('Business.BusinessPackage');
        $package = $mBusinessPackage->getDefaultPackage(false);
        if($package)
        {
            return $this->updateAll(array(
                'Business.business_package_id' => $package['id']
            ), array(
                'Business.id' => $business_id
            ));
        }
        return false;
    }
    
    public function sendNotification($user_id, $sender_id, $action, $url, $param = null)
    {
        $data = array(
            'user_id' => $user_id,
            'sender_id' => $sender_id,
            'action' => $action,
            'url' => $url,
            'params' => $param,
            'plugin' => 'Business',
        );
        $mNotification = MooCore::getInstance()->getModel('Notification');
        $mNotification->create();
        $mNotification->save($data);
    }
    
    public function updateRelatedCounter($business_id) 
    {
        $mBusinessLocation = MooCore::getInstance()->getModel('Business.BusinessLocation');
        
        //update business counter for location
        $business = $this->findById($business_id);
        $mBusinessLocation->updateBusinessCounter($business['Business']['business_location_id']);
    }
    
    public function suggestBusiness($keyword, $page = 1, $block_user = true)
    {
        $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
        $mBusinessLocation = MooCore::getInstance()->getModel('Business.BusinessLocation');
        $this->recursive = -1;
        $best_match_order = "";
        $current_location_id = $mBusinessLocation->getDefaultLocationId();
        
        $cond = array(
            'Business.status' => BUSINESS_STATUS_APPROVED,
            'Business.claim_id' => 0
        );
        if($current_location_id > 0)
        {
            $cond['Business.business_location_id'] = $current_location_id;
        }
        if($keyword != null)
        {
            $keyword = trim($keyword);
            $keyword = str_replace("'", "\\'", $keyword);
            $temp_keyword = explode(" ", $keyword);
            if($temp_keyword != null)
            {
                $cond['OR'][] = "Business.name LIKE '%$keyword%'";
                $best_match_order = "(Business.name LIKE '%$keyword%') + ";
                foreach($temp_keyword as $index => $kw)
                {
                    $cond['OR'][] = "Business.name LIKE '%$kw%'";
                    $best_match_order .= "(Business.name LIKE '%$kw%')";
                    $best_match_order .= $index < count($temp_keyword) -1 ? " + " : "";
                }
            }
        }
        
        $data = $this->find('all', array(
            'conditions' => $cond,
            'order' => '('.$best_match_order.') DESC',
            'page' => $page,
            'limit' => 20
        ));

        $result = null;
        if ($data != null) {
            foreach ($data as $item) {
                $result[] = array(
                    'value' => $item['Business']['id'],
                    'label' => $item['Business']['name'],
                    'image' => $businessHelper->getPhoto($item['Business'], array('prefix' => BUSINESS_IMAGE_THUMB_WIDTH . '_')),
                    'address' => $item['Business']['address'],
                    'link' => $item['Business']['moo_href']
                );
            }
        }
        return $result;
    }
    
    public function isBusinessOwner($business_id, $user_id = null)
    {
        $cond = array(
            'Business.id' => $business_id
        );
        if($user_id > 0)
        {
            $cond['Business.user_id'] = $user_id;
        }
        else
        {
            $cond['Business.user_id'] = MooCore::getInstance()->getViewer(true);
        }
        return $this->hasAny($cond);
    }
    
    public function isBusinessApproved($business_id)
    {
        return $this->hasAny(array(
            'Business.id' => $business_id,
            'Business.status' => BUSINESS_STATUS_APPROVED
        ));
    }
    
    public function getAdminUser()
    {
        $mUser = MooCore::getInstance()->getModel('User');
        return $mUser->find('first', array(
            'conditions' => array(
                'role_id' => 1,
                'approved' => 1
            )
        ));
    }
    
    public function updateBusinessBranchCounter($business_id)
    {
        $total = $this->find('count', array(
            'conditions' => array(
                'Business.parent_id' => $business_id
            )
        ));
        $this->updateAll(array(
            'Business.branch_count' => $total
        ), array(
            'Business.id' => $business_id
        ));
        return $total;
    }
    
    public function checkHasBusiness($user_id)
    {
        return $this->hasAny(array(
            'Business.user_id' => $user_id
        ));
    }
    
    public function saveActivity($target_id, $action, $item_type, $item_id = 0, $items = null, $user_id = null, $content = null, $type = BUSINESS_ACTIVITY_TYPE)
    {
        $mActivity = MooCore::getInstance()->getModel('Activity');
        if($user_id == null)
        {
            $user_id = MooCore::getInstance()->getViewer(true);
        }
        $share = 0;
        if(in_array($action, array(
            BUSINESS_ACTIVITY_CREATE_BUSINESS_ACTION,
            BUSINESS_ACTIVITY_PHOTO_ACTION,
            BUSINESS_ACTIVITY_BRANCH_ACTION,
            BUSINESS_ACTIVITY_REVIEW_ACTION
        )))
        {
            $share = 1;
        }
        $data = array(
            'type' => $type,
            'target_id' => $target_id,
            'user_id' => $user_id,
            'action' => $action,
            //'item_type' => $item_type,
            'item_id' => $item_id,
            'plugin' => 'Business',
            'share' => $share
        );
        if($items != null)
        {
            $data['items'] = $items;
        }
        if($content != null)
        {
            $content = str_replace('<script', '', $content);
            $content = str_replace('</script>', '', $content);
            $data['content'] = $content;
        }
        return $mActivity->save($data);
    }
    
    public function deleteActivity($target_id, $action, $item_type, $item_id = 0, $items = ' ')
    {
        $mActivity = MooCore::getInstance()->getModel('Activity');
        $cond = array(
            'Activity.type' => BUSINESS_ACTIVITY_TYPE,
            'Activity.target_id' => $target_id,
            'Activity.action' => $action,
            'Activity.plugin' => 'Business',
        );
        if($item_id > 0)
        {
            $cond['Activity.item_id'] = $item_id;
        }
        return $mActivity->deleteAll($cond);
    }
    
    public function enableActivity($target_id, $action, $item_type, $enable = 1, $item_id = 0)
    {

    }
    
    public function saveCreateBusinessActivity($business_id)
    {
        $mActivity = MooCore::getInstance()->getModel('Activity');
        if($mActivity->hasAny(array(
            'Activity.type' => BUSINESS_ACTIVITY_TYPE_USER,
            'Activity.action' => BUSINESS_ACTIVITY_CREATE_BUSINESS_ACTION,
            'Activity.plugin' => 'Business',
            'Activity.item_id' => $business_id
        )))
        {
            return;
        }
        $business = $this->getOnlyBusiness($business_id);
        return $business != null ? $this->saveActivity(0, BUSINESS_ACTIVITY_CREATE_BUSINESS_ACTION, BUSINESS_ACTIVITY_BUSINESS_ITEM, $business_id, null, $business['Business']['user_id'], null, BUSINESS_ACTIVITY_TYPE_USER) : false;
    }
    
    public function deleteCreateBusinessActivity($business_id)
    {
        $this->deleteActivity($business_id, BUSINESS_ACTIVITY_CREATE_BUSINESS_ACTION, BUSINESS_ACTIVITY_BUSINESS_ITEM);
    }
    
    public function saveBranchActivity($business_id, $branch_id)
    {
        $mActivity = MooCore::getInstance()->getModel('Activity');
        if($mActivity->hasAny(array(
            'Activity.type' => BUSINESS_ACTIVITY_TYPE,
            'Activity.action' => BUSINESS_ACTIVITY_BRANCH_ACTION,
            'Activity.plugin' => 'Business',
            'Activity.target_id' => $business_id,
            'Activity.item_id' => $branch_id
        )))
        {
            return;
        }
        $branch = $this->getOnlyBusiness($branch_id);
        return $this->saveActivity($business_id, BUSINESS_ACTIVITY_BRANCH_ACTION, BUSINESS_ACTIVITY_BRANCH_ITEM, $branch_id, null, $branch['Business']['user_id']);
    }
    
    public function deleteBranchActivity($business_id, $branch_id)
    {
        $this->deleteActivity($business_id, BUSINESS_ACTIVITY_BRANCH_ACTION, BUSINESS_ACTIVITY_BRANCH_ITEM, $branch_id);
    }
    
    public function saveBusinessActivity($business_id)
    {
        return $this->saveActivity($business_id, BUSINESS_ACTIVITY_BUSINESS_ACTION, BUSINESS_ACTIVITY_BUSINESS_ITEM);
    }
    
    public function deleteBusinessActivity($business_id)
    {
        $this->deleteActivity($business_id, BUSINESS_ACTIVITY_BUSINESS_ACTION, BUSINESS_ACTIVITY_BUSINESS_ITEM);
    }
    
    public function enableBusinessActivity($business_id, $enable = 1)
    {
        return $this->enableActivity($business_id, BUSINESS_ACTIVITY_BUSINESS_ACTION, BUSINESS_ACTIVITY_BUSINESS_ITEM, $enable);
    }
    
    public function saveVerifyActivity($business_id)
    {
        $mActivity = MooCore::getInstance()->getModel('Activity');
        if($mActivity->hasAny(array(
            'Activity.type' => BUSINESS_ACTIVITY_TYPE,
            'Activity.action' => BUSINESS_ACTIVITY_VERIFY_ACTION,
            'Activity.plugin' => 'Business',
            'Activity.target_id' => $business_id
        )))
        {
            return;
        }
        return $this->saveActivity($business_id, BUSINESS_ACTIVITY_VERIFY_ACTION, BUSINESS_ACTIVITY_VERIFY_ITEM);
    }
    
    public function deleteVerifyActivity($business_id)
    {
        $this->deleteActivity($business_id, BUSINESS_ACTIVITY_VERIFY_ACTION, BUSINESS_ACTIVITY_VERIFY_ITEM);
    }
    
    public function saveCheckinActivity($business_id, $content, $item_id)
    {
        return $this->saveActivity($business_id, BUSINESS_ACTIVITY_CHECKIN_ACTION, BUSINESS_ACTIVITY_CHECKIN_ITEM, $item_id, null, null, $content);
    }
    
    public function deleteCheckinActivity($business_id)
    {
        return $this->deleteActivity($business_id, BUSINESS_ACTIVITY_CHECKIN_ACTION, BUSINESS_ACTIVITY_CHECKIN_ITEM);
    }
    
    public function searchBusiness($keyword = null, $page = 1, $limit = RESULTS_LIMIT)
    {
        $pp = Configure::read('Business.business_search_item_per_page');
        if(!empty($pp) && $limit == RESULTS_LIMIT){
            $limit = $pp;
        }
        
        //load data
        $cond = array(
            //'Business.parent_id' => 0,
            'Business.claim_id' => 0,
            'Business.status' => BUSINESS_STATUS_APPROVED
        );
        
        if($keyword != null)
        {
            $keyword = str_replace("'", "\'", $keyword);
            $cond["OR"] = array(
                "Business.name LIKE '%".$keyword."%'",
                "Business.description LIKE '%".$keyword."%'"
            );
        }
        
        $data = $this->find('all', array(
            'conditions' => $cond,
            'order' => array('Business.id' => 'DESC'),
            'limit' => $limit,
            'page' => $page
        ));
        
        //debug(end($this->getDataSource()->getLog()['log'])['query']); die;
        
        return $data;
    }
    
    public function getCategoryIcon($business_id)
    {
        $mBusinessCategory = MooCore::getInstance()->getModel('Business.BusinessCategory');
        $mBusinessCategoryItem = MooCore::getInstance()->getModel('Business.BusinessCategoryItem');
        $cat = $mBusinessCategoryItem->find('first', array(
            'conditions' => array(
                'BusinessCategoryItem.business_id' => $business_id
            )
        ));
        if($cat != null)
        {
            $map = $mBusinessCategory->getPath($cat['BusinessCategoryItem']['business_category_id']);
            if(!empty($map[0]['BusinessCategory']['icon']))
            {
                return $map[0]['BusinessCategory']['icon'];
            }
        }
        return null;
    }
    
    public function getBusinessPackage($business_id)
    {
        $this->bindModel(array(
            'belongsTo' => array(
                'BusinessPackage' => array('className' => 'Business.BusinessPackage')
            )
        ));
        $data = $this->findById($business_id);
        return $data != null ? $data['BusinessPackage'] : array();
    }
    
    public function getBusinessAlbum($business_id)
    {
        return $this->findById($business_id);
    }
    
    public function getOnlyBusiness($business_id)
    {
        $this->bindModel(array(
            'belongsTo' => array(
                'BusinessLocation' => array('className' => 'Business.BusinessLocation'),
                'BusinessPackage' => array('className' => 'Business.BusinessPackage'),
				'BusinessType' => array('className' => 'Business.BusinessType'),
            ),
            'hasMany' => array(
                'BusinessTime' => array('className' => 'Business.BusinessTime'),
            ),
            'hasAndBelongsToMany' => array(
                'BusinessCategory' => array(
                    'className' => 'Business.BusinessCategory',
                    'counterCache' => true,
                    'joinTable' => 'business_category_items',
                    'foreignKey' => 'business_id',
                    'associationForeignKey' => 'business_category_id',
                ),
            )
        ));
        return $this->findById($business_id);
    }
    
    public function createAlbum($business_name, $user_id = null)
    {
        $mAlbum = MooCore::getInstance()->getModel('Photo.Album');
        $mAlbum->save(array(
            'user_id' => $user_id != null ? $user_id : MooCore::getInstance()->getViewer(true),
            'title' => $business_name,
            'description' => $business_name,
            'type' => 'business'
        ));
        return $mAlbum->id;
    }
    
    public function loadListBranch($business_id)
    {
        return $this->find('list', array(
            'conditions' => array(
                'Business.parent_id' => $business_id
            ),
            'fields' => array('Business.name')
        ));
    }
    
    public function totalFavouriteBusiness()
    {
        $mBusinessFavourite = MooCore::getInstance()->getModel('Business.BusinessFavourite');
        return $mBusinessFavourite->find('count', array(
            'conditions' => array(
                'BusinessFavourite.user_id' => MooCore::getInstance()->getViewer(true)
            )
        ));
    }
    
    public function getRandomBusiness($except_id = null)
    {
        //get except business
        $business = $this->getOnlyBusiness($except_id);
        
        $distance = Configure::read('Business.business_search_default_distance');
        $formular = sprintf('ROUND(SQRT(
                POW(69.1 * (%s.lat - '.$business['Business']['lat'].'), 2) +
                POW(69.1 * ('.$business['Business']['lng'].' - %s.lng) * COS(%s.lat / 57.3), 2)), 2)', $this->alias, $this->alias, $this->alias);

        $this->virtualFields = array(
            'distance' => $formular
        );       
        $cond = array($formular.' <= '.$distance, 'Business.status' => BUSINESS_STATUS_APPROVED, 'Business.parent_id' => 0, 'Business.claim_id' => 0);
        if($except_id > 0)
        {
            $cond['Business.id !='] = $except_id;
        }
        $user_id = MooCore::getInstance()->getViewer(true);
        if(!empty($user_id)){
            $cond['Business.user_id !='] = $user_id;
        }
        return $this->find('all', array(
            'conditions' => $cond,
            'limit' => 2,
            'order' => 'RAND()'
        ));
    }
    
    public function loadMyBusinessList($user_id)
    {
        $mBusinessAdmin = MooCore::getInstance()->getModel('Business.BusinessAdmin');
        
        //find all business id of current user who is set as admin
        $business_ids = $mBusinessAdmin->find('list', array(
            'conditions' => array(
                'BusinessAdmin.user_id' => $user_id
            ),
            'fields' => array('BusinessAdmin.business_id')
        ));

        $cond = array(
            'OR' => array(
                'Business.user_id' => $user_id,
                'Business.id' => $business_ids
            ),
            'Business.parent_id' => 0
        );
        
        if($user_id != MooCore::getInstance()->getViewer(true)){
            $cond['Business.claim_id'] = 0;
            $cond['Business.status'] = BUSINESS_STATUS_APPROVED;
        }
        
        return $this->find('list', array(
            'conditions' => $cond,
            'fields' => array('Business.name')
        ));
    }
    
    public function hasBusinesses($user_id)
    {
        $count = count($this->loadMyBusinessList($user_id));
        if($count > 0)
        {
            return true;
        }
        return false;
    }
    
    public function hasApprovedBusinesses($user_id)
    {
        return $this->hasAny(array(
            'Business.status' => BUSINESS_STATUS_APPROVED,
            'Business.claim_id' => 0,
            'Business.parent_id' => 0,
            'Business.user_id' => $user_id
        ));
    }
    
    public function isFavourited($user_id, $business_id)
    {
        $mBusinessFavourite = MooCore::getInstance()->getModel('Business.BusinessFavourite');
        return $mBusinessFavourite->hasAny(array(
            'BusinessFavourite.user_id' => $user_id,
            'BusinessFavourite.business_id' => $business_id
        ));
    }
    
    public function addFavourite($user_id, $business_id)
    {
        $mBusinessFavourite = MooCore::getInstance()->getModel('Business.BusinessFavourite');
        return $mBusinessFavourite->save(array(
            'user_id' => $user_id,
            'business_id' => $business_id
        ));
    }
    
    public function removeFavourite($user_id, $business_id)
    {
        $mBusinessFavourite = MooCore::getInstance()->getModel('Business.BusinessFavourite');
        return $mBusinessFavourite->deleteAll(array(
            'BusinessFavourite.user_id' => $user_id,
            'BusinessFavourite.business_id' => $business_id
        ));
    }
    
    public function loadMyFavourites($user_id, $page = 1)
    {
        $cond = array(
            'Business.status' => BUSINESS_STATUS_APPROVED,
            'Business.claim_id' => 0,
            'Business.id IN(SELECT business_id FROM '.$this->tablePrefix.'business_favourites WHERE user_id = '.$user_id.')'
        );
        
        $data = $this->find('all', array(
            'conditions' => $cond,
            'limit' => Configure::read('Business.business_search_item_per_page'),
            'page' => $page,
            'order' => array('Business.id' => 'DESC')
        ));
        return $this->parseParentData($data);
    }
    
    public function loadMyFollowing($user_id, $page = 1)
    {
        $user_id = MooCore::getInstance()->getViewer(true);
        $cond = array(
            'Business.status' => BUSINESS_STATUS_APPROVED,
            'Business.claim_id' => 0,
            'Business.id IN(SELECT business_id FROM '.$this->tablePrefix.'business_follows WHERE user_id = '.$user_id.' AND is_banned = 0)'
        );
        
        $data = $this->find('all', array(
            'conditions' => $cond,
            'limit' => Configure::read('Business.business_search_item_per_page'),
            'page' => $page,
            'order' => array('Business.id' => 'DESC')
        ));
        return $this->parseParentData($data);
    }
    
    public function updateCheckinCounter($business_id)
    {
        $mBusinessCheckin = MooCore::getInstance()->getModel('Business.BusinessCheckin');
        $total = $mBusinessCheckin->find('count', array(
            'conditions' => array(
                'BusinessCheckin.business_id' => $business_id
            )
        ));
        $this->updateAll(array(
            'Business.checkin_count' => $total,
        ), array(
            'Business.id' => $business_id
        ));
    }
    
    public function updateOwnerActivities($iBusinessId, $iUserId, $iOldUserId) 
    {
        if (empty($iBusinessId) || empty($iUserId) || empty($iOldUserId)) {
            return false;
        }
        
        $aCond = array(
            'Activity.type' => 'business_business',
            'Activity.target_id' => $iBusinessId,
            'Activity.user_id' => $iOldUserId,
        );

        $mActivity = MooCore::getInstance()->getModel('Activity');
        $mActivity->updateAll(array(
            'Activity.user_id' => $iUserId
        ), $aCond);
    }
    
    public function updateOwnerAlbumPhotos($iAlbumId, $iUserId, $iOldUserId) 
    {
        if (empty($iAlbumId) || empty($iUserId)) {
            return false;
        }
        
        $mAlbum = MooCore::getInstance()->getModel('Photo.Album');
        $mAlbum->id = $iAlbumId;
        $mAlbum->save(array('user_id' => $iUserId));

        $mPhoto = MooCore::getInstance()->getModel('Photo.Photo');
        $mPhoto->updateAll(array(
            'Photo.user_id' => $iUserId
        ), array(
            'Photo.type' => 'Photo_Album',
            'Photo.target_id' => $iAlbumId,
            'Photo.user_id' => $iOldUserId,
        ));
    }
    
    public function updateOwnerBranches($iBusinessId, $iUserId, $iOldUserId, $iOldBusinessId) 
    {
        if (empty($iBusinessId) || empty($iUserId) || empty($iOldUserId) || empty($iOldBusinessId)) {
            return false;
        }
        
        $mAlbum = MooCore::getInstance()->getModel('Photo.Album');
        $subpages = $this->find('all', array(
            'conditions' => array(
                'Business.parent_id' => $iOldBusinessId
            )
        ));
        if($subpages != null)
        {
            foreach($subpages as $subpage)
            {
                $this->updateAll(array(
                    'Business.parent_id' => $iBusinessId,
                    'Business.user_id' => $iUserId
                ), array(
                    'Business.id' => $subpage['Business']['id']
                ));
                $album = $mAlbum->findById($subpage['Business']['album_id']);
                if($album != null)
                {
                    $this->updateOwnerAlbumPhotos($subpage['Business']['album_id'], $iUserId, $album['Album']['user_id']);
                }
            }
        }
    }
    
    public function getMultiLocations($businesses)
    {
        $data = array();
        if($businesses != null)
        {
            $businessHelper  = MooCore::getInstance()->getHelper('Business_Business');
            $range = range('A', 'Z');
            foreach($businesses as $k => $business)
            {
                $business = $business['Business'];
                
                $marker_icon = $this->getCategoryIcon($business['id']);
                
                /*$marker_icon = $this->request->base.'/business/images/marker_regular.png';
                //feature icon
                if($business['featured'])
                {
                    $marker_icon = $this->request->base.'/business/images/marker_featured.png';
                }*/
                $data[] = array(
                    'name' => $business['name'], 
                    'link' => $business['moo_href'], 
                    'address' => $business['address'], 
                    'image' => $businessHelper->getPhoto($business, array('prefix' => BUSINESS_IMAGE_THUMB_WIDTH.'_', 'tag' => false)),
                    'review_count' => $business['review_count'],
                    'total_score' => $business['total_score'],
                    'lat' => $business['lat'], 
                    'lng' => $business['lng'], 
                    'featured' => (int)$business['featured'],
                    'marker_label' => $range[$k],
                    'marker_icon' => $businessHelper->getPhoto($marker_icon, array('tag' => false)));
            }
        }
        $data = json_encode($data);
        return str_replace("'", '&#39', $data);
    }
    
    public function getLatestBusinesses($page = 1)
    {
        return $this->find("all", array(
            "conditions" => array(
                "Business.status" => BUSINESS_STATUS_APPROVED
            ),
            "order" => array("Business.id" => "DESC"),
            "page" => $page,
            "limit" => 10
        ));
    }
    public function getFeaturedBusinesses($limit = 12)
    {
        return $this->find("all", array(
            "conditions" => array(
                "Business.status" => BUSINESS_STATUS_APPROVED,
                "Business.featured" => 1
            ),
            "order" => array("Business.id" => "DESC"),
            "limit" => $limit,
        ));
    }
    
    public function loadBracnches($business_id, $page = 1, $limit = 0)
    {
        //get photos
        if($limit == 0)
        {
            $limit = Configure::read('Business.business_branch_per_page');
        }

        $this->bindModel(array(
            'hasAndBelongsToMany' => array(
                'BusinessCategory' => array(
                    'className' => 'Business.BusinessCategory',
                    'counterCache' => true,
                    'joinTable' => 'business_category_items',
                    'foreignKey' => 'business_id',
                    'associationForeignKey' => 'business_category_id',
                )
            )
        ));
        
        $cond = array(
            'Business.parent_id' => $business_id,
            'Business.status' => BUSINESS_STATUS_APPROVED
        );
        return $this->find('all', array(
            'conditions' => $cond,
            'limit' => $limit,
            'order' => array('Business.created' => 'DESC'),
            'page' => $page
        ));
    }
    
    public function permission($business_id, $permission, $permissions = null)
    {
        $mUser = MooCore::getInstance()->getModel('User');
        $mBusinessAdmin = MooCore::getInstance()->getModel('Business.BusinessAdmin');
        $user = $mUser->findById(MooCore::getInstance()->getViewer(true));
        $user_id = MooCore::getInstance()->getViewer(true);
        $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
        if($user == null)
        {
            return false;
        }
        if($this->isBusinessOwner($business_id) || $user['Role']['is_admin'])
        {
            return true;
        }
        
        $business = $this->getOnlyBusiness($business_id);
        if($permissions == null)
        {
            $permissions = $business['Business']['moo_permissions'];
        }
        if($business['Business']['parent_id'] > 0)
        {
            $parent_business = $this->getOnlyBusiness($business['Business']['parent_id']);
            $business_id = $parent_business['Business']['id'];
            $permissions = $parent_business['Business']['moo_permissions'];
        }
        switch($permission)
        {
            case 'can_reply_review':
            case 'can_delete_reply_review':
            case 'can_edit_reply_review':
                if($mBusinessAdmin->isBusinessAdmin($business_id))
                {
                    return true;
                }
                return false;
                break;
            case 'can_create_review':
                if($mBusinessAdmin->isBusinessAdmin($business_id))
                {
                    return false;
                }
                return true;
                break;
            case 'dashboard_can_manage_photo':
            case 'dashboard_can_manage_branches':
            case 'dashboard_can_create_branch':
                if(!$mBusinessAdmin->hasAdminPermission($business_id) && !$user['Role']['is_admin'])
                {
                    return false;
                }
                return true;
                break;
            case 'dashboard_can_manage_admin':
                if(!$this->isBusinessOwner($business_id) && !$user['Role']['is_admin'])
                {
                    return false;
                }
                return true;
                break;
            case BUSINESS_PERMISSION_MANAGE_PHOTO:
                if($permissions != null && $mBusinessAdmin->isBusinessAdmin($business_id, $user_id) && in_array(BUSINESS_PERMISSION_MANAGE_PHOTO, $permissions))
                {
                    return true;
                }
                return false;
                break;
            case BUSINESS_PERMISSION_MANAGE_PRODUCT:
                if($permissions != null && $mBusinessAdmin->isBusinessAdmin($business_id, $user_id) && in_array(BUSINESS_PERMISSION_MANAGE_PRODUCT, $permissions))
                {
                    return true;
                }
                return false;
                break;
            case BUSINESS_PERMISSION_MANAGE_SUBPAGE:
                if($permissions != null && $mBusinessAdmin->isBusinessAdmin($business_id, $user_id) && in_array(BUSINESS_PERMISSION_MANAGE_SUBPAGE, $permissions))
                {
                    return true;
                }
                return false;
                break;
            case BUSINESS_PERMISSION_UPGRADE_PAGE:
                if($permissions != null && $mBusinessAdmin->isBusinessAdmin($business_id, $user_id) && in_array(BUSINESS_PERMISSION_UPGRADE_PAGE, $permissions))
                {
                    return true;
                }
                return false;
                break;
            case BUSINESS_PERMISSION_FEATURE_PAGE:
                if($permissions != null && $mBusinessAdmin->isBusinessAdmin($business_id, $user_id) && in_array(BUSINESS_PERMISSION_FEATURE_PAGE, $permissions))
                {
                    return true;
                }
                return false;
                break;
            case BUSINESS_PERMISSION_SEND_VERIFICATION_REQUEST:
                if($permissions != null && $mBusinessAdmin->isBusinessAdmin($business_id, $user_id) && in_array(BUSINESS_PERMISSION_SEND_VERIFICATION_REQUEST, $permissions))
                {
                    return true;
                }
                return false;
                break;
            case BUSINESS_PERMISSION_MANAGE_ADMIN:
                if($permissions != null && $mBusinessAdmin->isBusinessAdmin($business_id, $user_id) && in_array(BUSINESS_PERMISSION_MANAGE_ADMIN, $permissions))
                {
                    return true;
                }
                return false;
                break;
            case BUSINESS_PERMISSION_EDIT_PAGE:
                if($permissions != null && $mBusinessAdmin->isBusinessAdmin($business_id, $user_id) && in_array(BUSINESS_PERMISSION_EDIT_PAGE, $permissions))
                {
                    return true;
                }
                return false;
                break;
            case BUSINESS_PERMISSION_BAN:
                if($permissions != null && $mBusinessAdmin->isBusinessAdmin($business_id, $user_id) && in_array(BUSINESS_PERMISSION_BAN, $permissions))
                {
                    return true;
                }
                return false;
                break;
            case BUSINESS_PERMISSION_RESPONSE_REVIEW:
                if($permissions != null && $mBusinessAdmin->isBusinessAdmin($business_id, $user_id) && in_array(BUSINESS_PERMISSION_RESPONSE_REVIEW, $permissions))
                {
                    return true;
                }
                return false;
                break;
        }
    }
    
    public function upgradeMessage($business)
    {
        return sprintf(__d('business', 'This item is currently not visible. You must %s your business page %s or get %s from site admin.'), '<a href="'.Router::url('/', true).'businesses/dashboard/upgrade/'.$business['Business']['id'].'">Upgrade</a>', '<a href="'.$business['Business']['moo_href'].'">'.$business['Business']['name'].'</a>','<a href="'.Router::url('/', true).'contact">approval</a>');
    }
    
    public function permissionMessage()
    {
        return __d('business', 'Business not found or you don\'t have permission');
    }
    
    public function getBusinessPermission($package)
    {
        $data = array(
            BUSINESS_PERMISSION_MANAGE_PHOTO => __d('business', 'Can manage photos'),
            BUSINESS_PERMISSION_MANAGE_PRODUCT => __d('business', 'Can manage products'),
            BUSINESS_PERMISSION_BAN => __d('business', 'Can ban and manage banned members'),
            BUSINESS_PERMISSION_RESPONSE_REVIEW => __d('business', 'Can Response to a review'),
            BUSINESS_PERMISSION_MANAGE_SUBPAGE => __d('business', 'Can manage and create sub-page'),
            BUSINESS_PERMISSION_UPGRADE_PAGE => __d('business', 'Can upgrade page'),
            BUSINESS_PERMISSION_FEATURE_PAGE => __d('business', 'Can featured page'),
            BUSINESS_PERMISSION_SEND_VERIFICATION_REQUEST => __d('business', 'Can send verification request'),
            BUSINESS_PERMISSION_EDIT_PAGE => __d('business', 'Can edit page details'),
            BUSINESS_PERMISSION_MANAGE_ADMIN => __d('business', 'Can manage admins'),
        );
        if(!$package['manage_admin'])
        {
            unset($data[BUSINESS_PERMISSION_MANAGE_ADMIN]);
        }
        if(!$package['response_review'])
        {
            unset($data[BUSINESS_PERMISSION_RESPONSE_REVIEW]);
        }
        if(!$package['send_verification_request'])
        {
            unset($data[BUSINESS_PERMISSION_SEND_VERIFICATION_REQUEST]);
        }
        return $data;
    }
    
    public function getBusinessSameCategories($business_id, $block_user = true)
    {
        $mBusinessCategoryItem = MooCore::getInstance()->getModel('Business.BusinessCategoryItem');

        //get business cat
        $mBusinessCategoryItem->recursive = -2;
        $cat_ids = $mBusinessCategoryItem->find('list', array(
            'conditions' => array('BusinessCategoryItem.business_id' => $business_id),
            'fields' => array('BusinessCategoryItem.id', 'BusinessCategoryItem.business_category_id')
        ));

        if($cat_ids == null)
        {
            return array();
        }
        
        //get businesses
        $cond = array(
            'Business.status' => BUSINESS_STATUS_APPROVED,
            'Business.claim_id' => 0,
            'Business.id !=' => $business_id,
            'Business.id IN(SELECT business_id FROM '.$this->tablePrefix.'business_category_items WHERE business_category_id IN('.implode(',', $cat_ids).'))'
        );
        
        $data = $this->find('all', array(
            'conditions' => $cond,
            'limit' => Configure::read('Business.business_same_categories_items'),
            'order' => array('Business.total_score' => 'DESC'),
        ));
        return $this->parseCategoryLanguage($data);
    }
    
    public function totalMyBusiness($user_id)
    {
        $mBusinessAdmin = MooCore::getInstance()->getModel('Business.BusinessAdmin');
        $business_count = $this->find('count', array(
            'conditions' => array('Business.user_id' => $user_id)
        ));
        $admin_count = $mBusinessAdmin->find('count', array(
            'conditions' => array('BusinessAdmin.user_id' => $user_id)
        ));
        return $admin_count + $business_count;
    }
    
    public function bCheckClaimBusiness($aBusiness, $iCUserId){
        
        if ($aBusiness['Business']['is_claim'] == 1 || 
                $aBusiness['Business']['claim_id'] != 0 || 
                $aBusiness['Business']['parent_id'] != 0 || 
                $aBusiness['Business']['user_id'] == $iCUserId || 
                $this->isClaimBusinessExist($aBusiness['Business']['id'], $iCUserId)) {
            return false;
        }
        
        return true;
    }
    
    public function updateBusinessAlbum($business_id, $album_id)
    {
        $this->updateAll(array(
            'Business.album_id' => $album_id
        ), array(
            'Business.id' => $business_id
        ));
    }
    
    public function checkAlbumExist($album_id)
    {
        $mAlbum = MooCore::getInstance()->getModel('Album');
        return $mAlbum->hasAny(array(
            'id' => $album_id
        ));
    }
    
    public function updateSubPageVerify($parent_id, $verify_value)
    {
        $this->updateAll(array(
            'Business.verify' => $verify_value
        ), array(
            'Business.parent_id' => $parent_id
        ));
    }
    
    public function sendBusinessNotification($business_id, $task, $sender_id = null, $link = '', $except_user_id = '')
    {
        $mBusinessAdmin = MooCore::getInstance()->getModel('Business.BusinessAdmin');
        $mBusinessFollow = MooCore::getInstance()->getModel('Business.BusinessFollow');
        
        $business = $this->getOnlyBusiness($business_id);
        if($business['Business']['parent_id'] > 0)
        {
            $parent_page = $this->getOnlyBusiness($business['Business']['parent_id']);
            $admin_ids = $mBusinessAdmin->loadAdminListId($parent_page['Business']['id']);
        }
        else 
        {
            $admin_ids = $mBusinessAdmin->loadAdminListId($business_id);
        }
        $follower_ids = $mBusinessFollow->getFollowerIds($business_id);
        $user_ids = array_merge($admin_ids, $follower_ids);
        $user_ids[] = $business['Business']['user_id'];
        $user_ids = array_unique($user_ids);

        //notification
        if($user_ids != null)
        {
            $except_user_id = $except_user_id != '' ? $except_user_id : MooCore::getInstance()->getViewer(true);
            foreach($user_ids as $user_id)
            {
                if($user_id == $except_user_id)
                {
                    continue;
                }
                $this->sendNotification(
                    $user_id, 
                    $sender_id, 
                    $task, 
                    $link, 
                    $business['Business']['name']
                );
            }
        }
    }
    
    public function getBusinessHashtags($qid, $limit = RESULTS_LIMIT,$page = 1){
        $cond = array(
            'Business.id' => $qid,
          
        );

        //get blogs of active user
        $cond['User.active'] = 1;

        $businesses = $this->find( 'all', array( 'conditions' => $cond, 'limit' => $limit, 'page' => $page ) );
        return $businesses;
    }
    
    public function copyClaimStorageImage($id = null, $filename, $aws = null)
    {
        if($aws == null)
        {
            $mStorageAwsObjectMap = MooCore::getInstance()->getModel('Storage.StorageAwsObjectMap');
            $aws = $mStorageAwsObjectMap->find('all', array(
                'conditions' => array(
                    'StorageAwsObjectMap.oid' => $id,
                    'StorageAwsObjectMap.type' => 'businesses'
                )
            ));
        }
        if($aws != null)
        {
            foreach($aws as $item)
            {
                $item = $item['StorageAwsObjectMap'];
                if($this->UR_exists($item['url']))
                {
                    $content = file_get_contents($item['url']);
                    file_put_contents('uploads'.DS.$item['key'], $content);
                }
            }
        }
    }
    
    function UR_exists($url)
    {
        $headers = get_headers($url);
        return stripos($headers[0],"200 OK")?true:false;
    }
    
    public function getCurrentStorageImage($id)
    {
        $mStorageAwsObjectMap = MooCore::getInstance()->getModel('Storage.StorageAwsObjectMap');
        return $mStorageAwsObjectMap->find('all', array(
            'conditions' => array(
                'StorageAwsObjectMap.oid' => $id,
                'StorageAwsObjectMap.type' => 'businesses'
            )
        ));
    }
    
    public function removeCoverFile( $business )
    {
        $path = WWW_ROOT . BUSINESS_COVER_FILE_PATH;
        
        if ($business['cover'] && file_exists($path . DS .$business['cover'])) 
        {
            unlink($path . DS . $business['cover']);
            unlink($path . DS . BUSINESS_COVER_IMAGE_WIDTH . '_' . $business['cover']);
        }
    }
}