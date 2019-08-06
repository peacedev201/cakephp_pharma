<?php
class BusinessCategory extends BusinessAppModel 
{
    public $validationDomain = 'business';
    public $mooFields = array('title','href','plugin','type','url', 'thumb');
    
    public $order = 'BusinessCategory.ordering ASC';
    public $actsAs = array('Tree' => 'nested','Translate' => array('name' => 'nameTranslation'));

    public $validate = array(
        'name' => array(
            array(
                'rule' => 'notBlank',
                'message' => 'Name is required'
            ),
            array(   
                'rule' => array('checkDuplicateName'),
                'message' => 'This name is already exist'
            ),	
        ),
    );
    
    public $recursive = 2;
    private $_default_locale = 'eng' ;
    public function setLanguage($locale) {
        $this->locale = $locale;
    }

    public function __construct($id = false, $table = null, $ds = null) 
    {
        parent::__construct($id, $table, $ds);
        $this->locale = Configure::read('Config.language');
    }
    
    function checkDuplicateName()
    {
        $cond = array(
            'BusinessCategory.name' => $this->data['BusinessCategory']['name'],
            'BusinessCategory.parent_id' => $this->data['BusinessCategory']['parent_id']
        );
        if(isset($this->data['BusinessCategory']['id']) && $this->data['BusinessCategory']['id'] > 0)
        {
            $cond[] = 'BusinessCategory.id != '.$this->data['BusinessCategory']['id'];
        }
        if($this->hasAny($cond))
        {
            return false;
        }
        return true;
    }
    
    public function getHref($row) 
    {
        $request = Router::getRequest();
        if(isset($row['name']) && isset($row['id']))
        {
            return $request->base.'/business_search/'.seoUrl($row['name']).'/'.$row['id'];
        }
        return '';
    }
    
    public function getCategoryList($parent_id = 0) {
        $result = array();
        $rows = $this->find('all', array('conditions' => array('enable' => 1, 'parent_id' => $parent_id)));
        if(!empty($rows)){
            if($parent_id == 0){
            }else{
                $result[$parent_id] = __d('business', 'Please select sub category') ;
            }
            foreach($rows as $row) {
                foreach($row['nameTranslation'] as $t_cat) {
                    if($t_cat['locale'] == $this->locale) {
                        $result[$t_cat['foreign_key']] = $t_cat['content'] ;
                    }
                }
            }
        }
        return $result;
    }
    public function getCategorySelect($cat_id) {
        $categories = array();
        $cat_path = $this->getPath($cat_id);
        foreach ($cat_path as $cat) {
            $parent_cat_id = $cat['BusinessCategory']['parent_id'];
            $categories[] = array(  'parent_id' => $cat['BusinessCategory']['parent_id'],
                                    'selected' => $cat['BusinessCategory']['id'],
                                    'data' => $this->getCategoryList($parent_cat_id));
        }
        return $categories;
    }
    public function getBreadCrumb($cat_id) {
        return $this->getPath($cat_id);
    }

    public function getCatById($id) {
        $category = $this->findById($id);
        if (empty($category)) {
            $this->locale = $this->_default_locale;
            $category = $this->findById($id);
        }
        return $category ;
    }
    
    public function suggestCategory($keyword, $global = false)
    {
        $this->recursive = -1;
        $keyword = str_replace("'", "\\'", $keyword);
        $data = $this->find('all', array(
            'conditions' => array(
                "I18n__nameTranslation.content LIKE '%$keyword%'",
                'BusinessCategory.enable' => 1
            )
        ));
        $result = null;
        if($data != null)
        {
            foreach($data as $item)
            {
                if($global)
                {
                    $label = $item['BusinessCategory']['name'];
                }
                else
                {
                    $label = $this->getCategoryPath($item['BusinessCategory']['id']);
                }
                $result[] = array(
                    'value' => $item['BusinessCategory']['id'],
                    'label' => $label
                );
            }
        }
        return $result;
    }
    
    public function getCategoryPath($id, $array = false)
    {
        $this->recursive = -1;
    	$categories = $this->getPath($id);
        $data = null;
        if($categories != null)
        {
            foreach($categories as $category)
            {
                $data[$category['BusinessCategory']['id']] = $category['BusinessCategory']['name'];
            }
            if($array)
            {
                return $data;
            }
            $data = implode('->', $data);
        }
        return $data;
    }
    public function deleteBusinessCategory($id) {
        
        $mItemModel  = MooCore::getInstance()->getModel('Business.BusinessCategoryItem');
        $allChildren = $this->children($id);  
        if(!empty($allChildren)){
            foreach ($allChildren as $k => $v) {
                $childs = $mItemModel->findAllByBusinessCategoryId($v['BusinessCategory']['id']);
                if(!empty($childs)){
                    foreach ($childs as $child){
                        $mItemModel->deleteBusinessCategoryItem($child['BusinessCategoryItem']['id']);
                    }
                }
            }
        }
        $parents = $mItemModel->findAllByBusinessCategoryId($id);
        if(!empty($parents)){
            foreach ($parents as $parent){
                $mItemModel->deleteBusinessCategoryItem($parent['BusinessCategoryItem']['id']);
            }
        }
        return $this->delete($id);
    }
    
    public function getCategories($id = null, $parent_id = null, $is_highlight = null, $alphabet = null, $threaded = false)
    {
        $cond = array(
            'BusinessCategory.enable' => 1
        );
        $order = array(
            'BusinessCategory.id' => 'ASC'
        );
        if($id > 0)
        {
            $cond['BusinessCategory.id'] = $id;
        }
        if(is_numeric($parent_id))
        {
            $cond['BusinessCategory.parent_id'] = $parent_id;
        }
        if(is_bool($is_highlight))
        {
            $cond['BusinessCategory.is_highlight'] = $is_highlight;
        }
        if($alphabet != null)
        {
            $cond[] = "I18n__nameTranslation.content LIKE '$alphabet%'";
            $order = array(
                'BusinessCategory.name' => 'ASC'
            );
        }
        $find = 'all';
        if($threaded)
        {
            $find = 'threaded';
        }
        $data = $this->find($find, array(
            'conditions' => $cond,
            'order' => $order
        ));
        if($id > 0 && !empty($data[0]))
        {
            return $data[0];
        }
        return $data;
    }
    
    public function getMapCategories($parent_id = 0)
    {
        $parent = $this->find('all', array(
            'conditions' => array('BusinessCategory.parent_id' => $parent_id, 'BusinessCategory.enable' => 1),
            'order' => array(
                'BusinessCategory.ordering' => 'ASC',
                'BusinessCategory.id' => 'ASC'
            )
        ));
        if($parent != null)
        {
            foreach($parent as $k => $item)
            {
                $children = $this->find('all', array(
                    'conditions' => array('BusinessCategory.parent_id' => $item['BusinessCategory']['id'], 'BusinessCategory.enable' => 1),
                    'order' => array(
                        'BusinessCategory.ordering' => 'ASC',
                        'BusinessCategory.id' => 'ASC'
                    )
                ));
                $parent[$k]['children'] = $children;
            }
        }
        return $parent;
    }
    
    public function getDefaultCategory()
    {
        return $this->findByIsDefault(1);
    }
    
    public function getIdList($id)
    {
        $result = array($id);
        $data = $this->children($id);
        if($data != null)
        {
            foreach($data as $item)
            {
                $result[] = $item['BusinessCategory']['id'];
            }
        }
        return $result;
    }
    
    public function findCatsByParent($parent_id) {
        $result = array();
        if (!empty($parent_id)) {
            $parent = $this->findById($parent_id);
            $cats = $this->find('all', array('conditions' => array('BusinessCategory.parent_id' => $parent['BusinessCategory']['parent_id'])));
            if (!empty($cats)) {
                foreach ($cats as $cat) {
                    $result[$cat['BusinessCategory']['id']] = $cat['BusinessCategory']['name'];
                }
            }
        }
        return $result;
    }
    public function isCategoryExist($id)
    {
        $cond = array(
            'BusinessCategory.id' => $id
        );
        return $this->hasAny($cond);
    }
    public function isFeaturedReach(){
        $count = $this->find('count', array('conditions' => array('BusinessCategory.is_highlight' => 1)));
        if($count >= 15) {
            return true;
        }
        return false;
    }
    public function featureCategory($id){
        return $this->updateAll(array('BusinessCategory.is_highlight' => 1), array('BusinessCategory.id' => $id));
    }
    public function unfeatureCategory($id){
        return $this->updateAll(array('BusinessCategory.is_highlight' => 0), array('BusinessCategory.id' => $id));
    }
    
    public function getTopCategories()
    {
        return $this->find('all', array(
            'conditions' => array(
                'BusinessCategory.enable' => 1,
                'BusinessCategory.parent_id' => 0
            ),
            'order' => array('BusinessCategory.ordering' => 'ASC', 'BusinessCategory.id' => 'ASC'),
            'limit' => Configure::read('Business.business_top_category_items')
        ));
    }
    
    public function updateBusinessCounter($business_id)
    {
        $mBusinessCategoryItem = MooCore::getInstance()->getModel('Business.BusinessCategoryItem');
        $category_ids = $mBusinessCategoryItem->find('list', array(
            'conditions' => array(
                'BusinessCategoryItem.business_id' => $business_id
            ),
            'fields' => array('BusinessCategoryItem.business_category_id')
        ));
        if($category_ids != null)
        {
            foreach($category_ids as $category_id)
            {
                $total = $mBusinessCategoryItem->find('count', array(
                    'conditions' => array(
                        'BusinessCategoryItem.business_category_id' => $category_id
                    )
                ));
                $this->updateAll(array(
                    'BusinessCategory.business_count' => $total
                ), array(
                    'BusinessCategory.id' => $category_id
                ));
                
                $paths = $this->getPath($category_id);
                foreach($paths as $path)
                {
                    if($path['BusinessCategory']['id'] != $category_id)
                    {
                        $total = $this->find('all', array(
                            'conditions' => array(
                                'BusinessCategory.parent_id' => $path['BusinessCategory']['id']
                            ),
                            'fields' => array('SUM(BusinessCategory.business_count) AS total')
                        ));
                        if(!empty($total[0][0]['total']))
                        {
                            $this->updateAll(array(
                                'BusinessCategory.business_count' => $total[0][0]['total']
                            ), array(
                                'BusinessCategory.id' => $path['BusinessCategory']['id']
                            ));
                        }
                    }
                }
            }
        }
    }
    
    public function rememberCatKeyword($location_name, $keyword)
    {
        $mBusinessCategorySearch = MooCore::getInstance()->getModel('Business.BusinessCategorySearch');
        
        //find category if exist
        $cat = $this->findByName($keyword);
        
        if($cat != null)
        {
            $cat = $cat['BusinessCategory'];
            $cat_search = $mBusinessCategorySearch->findByLocationName($location_name);
            if($cat_search != null)
            {
                $cat_search = $cat_search['BusinessCategorySearch'];
                $values = json_decode($cat_search['values'], true);
                if($values != null)
                {
                    $exist = false;
                    foreach($values as $k => $value)
                    {
                        if($value['category_id'] == $cat['id'])
                        {
                            $values[$k]['counter'] += 1;
                            $exist = true;
                            break;
                        }
                    }
                    if(!$exist)
                    {
                        $values[] = array(
                            'category_id' => $cat['id'],
                            'category_name' => $cat['name'],
                            'counter' => 0
                        );
                    }
                    $mBusinessCategorySearch->updateAll(array(
                        'values' => "'".json_encode($values)."'"
                    ), array(
                        'id' => $cat_search['id']
                    ));
                }
            }
            else
            {
                $values[] = array(
                    'category_id' => $cat['id'],
                    'category_name' => $cat['name'],
                    'counter' => 0
                );
                $mBusinessCategorySearch->save(array(
                    'location_name' => $location_name,
                    'values' => json_encode($values)
                ));
            }
        }
    }
    
    public function getHistoryCatSearch()
    {
        $mBusinessLocation = MooCore::getInstance()->getModel('Business.BusinessLocation');
        $mBusinessCategorySearch = MooCore::getInstance()->getModel('Business.BusinessCategorySearch');
        
        $location_name = $mBusinessLocation->getDefaultLocationName();
        $cat_search = $mBusinessCategorySearch->findByLocationName($location_name);
        $data = null;
        if($cat_search != null)
        {
            $values = json_decode($cat_search['BusinessCategorySearch']['values'], true);
            
            if(count($values) >= 5)
            {
                //sorting
                usort($values, function( $a, $b ){
                    return $b['counter'] - $a['counter'];
                });
                $values = array_slice($values, 0, 5, true);
                foreach($values as $item)
                {
                    $data[] = array(
                        'value' => $item['category_id'],
                        'label' => $item['category_name']
                    );
                }
            }
        }
        return $data;
    }
    
    public function getInterestedCategories()
    {
        $mBusinessLocation = MooCore::getInstance()->getModel('Business.BusinessLocation');
        $mBusinessCategoryItem = MooCore::getInstance()->getModel('Business.BusinessCategoryItem');
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        $this->recursive = -1;
        $mBusiness->recursive = -1;

        $data = $this->find('all', array(
            'conditions' => array(
                'BusinessCategory.id' => array('2955', '1428', '662', '2662')
            ),
            'order' => array("FIELD(BusinessCategory.id, '2955', '1428', '662', '2662')")
        ));
        if($data != null)
        {
            //get by location
            $around_params = $mBusiness->findBusinessAround();

            foreach($data as $k => $item)
            {
                $parent = $this->findById($item['BusinessCategory']['id']);
                $cat_ids = $this->find('list', array(
                    'conditions' => array(
                        'BusinessCategory.lft >=' => $parent['BusinessCategory']['lft'], 
                        'BusinessCategory.rght <=' => $parent['BusinessCategory']['rght']
                    ),
                    'fields' => array('BusinessCategory.id')
                ));
                $business_ids = $mBusinessCategoryItem->find('list', array(
                    'conditions' => array(
                        'BusinessCategoryItem.business_category_id' => $cat_ids,
                    ),
                    'fields' => array('BusinessCategoryItem.business_id') 
                ));
                
                $cond = array(
                    'Business.id' => $business_ids,
                );
                if(isset($around_params['conditions']))
                {
                    $cond = array_merge($cond, $around_params['conditions']);
                }
                if(isset($around_params['virtual_field']))
                {
                    $mBusiness->virtualFields = array(
                        'distance' => $around_params['virtual_field']
                    );
                }
                $data[$k]['BusinessCategory']['business_count'] = $mBusiness->find('count', array(
                    'conditions' => $cond
                ));
            }
        }
        return $data;
    }
    
    public function getLandingCategories()
    {
        return $this->find('all', array(
            'conditions' => array(
                'BusinessCategory.id' => array('1428', '662', '1466', '2400', '2975', '757')
            ),
            'order' => array("FIELD(BusinessCategory.id, '1428', '662', '1466', '2400', '2975', '757')")
        ));
    }
    
    private function updateAllBusinessCounter($select_cat_id = null, $parent_id = 0)
    {
        $this->recursive = -1;
        $cond = array('BusinessCategory.parent_id' => $parent_id);
        if($select_cat_id > 0)
        {
            $cond['BusinessCategory.id'] = $select_cat_id;
        }
        $cats = $this->find('all', array(
            'conditions' => $cond,
            'fields' => array(
                'BusinessCategory.id', 'BusinessCategory.parent_id', 'BusinessCategory.business_count'
            )
        ));
        $total = 0;
        if($cats != null)
        {
            foreach($cats as $cat)
            {
                $total_child = 0;
                if($this->hasAny(array(
                    'BusinessCategory.parent_id' => $cat['BusinessCategory']['id']
                )))
                {
                    $total_child += $cat['BusinessCategory']['business_count'];
                    $total_child += $this->updateAllBusinessCounter(null, $cat['BusinessCategory']['id']);
                    if($total_child > 0)
                    {
                        $this->updateAll(array(
                            'BusinessCategory.business_count' => $total_child
                        ), array(
                            'BusinessCategory.id' => $cat['BusinessCategory']['id']
                        ));
                    }
                }
                else
                {
                    $total_child += $cat['BusinessCategory']['business_count'];
                }
                $total += $total_child;
            }
            if($parent_id > 0)
            {
                $this->updateAll(array(
                    'BusinessCategory.business_count' => $total
                ), array(
                    'BusinessCategory.id' => $parent_id
                ));
            }
        }
        return $total;
    }
    
    public function getRoorCategoryId($cat_id)
    {
        $this->recursive = -1;
        $cat = $this->findById($cat_id);
        if($cat != null && $cat['BusinessCategory']['parent_id'] == 0)
        {
            return $cat['BusinessCategory']['id'];
        }
        else if($cat != null)
        {
            return $this->getRoorCategoryId($cat['BusinessCategory']['parent_id']);
        }
        return '';
    }
    
    public function getAllChildrenId($cat_id)
    {
        $this->recursive = -1;
        $data = $this->children($cat_id, false, array('BusinessCategory.id'));
        $result = array();
        if($data != null)
        {
            foreach($data as $item)
            {
                $result[] = $item['BusinessCategory']['id'];
            }
        }
        return $result;
    }
}