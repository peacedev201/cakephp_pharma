<?php 
App::uses('StoreAppModel', 'Store.Model');
class StoreCategory extends StoreAppModel
{
    public $validationDomain = 'store';
	public $actsAs = array(
        'Tree',
        'Translate' => array('name' => 'nameTranslation')
    );
    public $mooFields = array('href');
	public $order = 'StoreCategory.ordering asc';
	public $validate = array(           
        'name' =>   array(   
            'rule' => 'notBlank',
            'message' => 'Please provide category name'
        ),		
    );
    
    private $_default_locale = 'eng' ;
    public function setLanguage($locale) {
        $this->locale = $locale;
    }

    public function __construct($id = false, $table = null, $ds = null) 
    {
        parent::__construct($id, $table, $ds);
        $this->locale = Configure::read('Config.language');
    }
    
    public function beforeSave($options = array()) {
        parent::beforeSave($options);
        foreach($this->actsAs['Translate'] as $field => $item)
        {
            $this->data['StoreCategory']['trans_'.$field] = $this->data['StoreCategory'][$field];
            $this->data['StoreCategory'][$field] = reset($this->data['StoreCategory'][$field]);
        }
    }

    public function afterSave($created, $options = array()) {
        parent::afterSave($created, $options);
        
        //save multi language
        foreach($this->actsAs['Translate'] as $field => $item)
        {
            $data = !empty($this->data['StoreCategory']['trans_'.$field]) ? $this->data['StoreCategory']['trans_'.$field] : null;
            $this->saveMultiLanguage($data, $field, $this->data['StoreCategory']['id']);
        }
    }
    
    public function isStoreCategoryExist($id, $enable = null)
    {
        $cond = array(
            'StoreCategory.id' => $id
        );
        if(is_bool($enable))
        {
            $cond['StoreCategory.enable'] = $enable;
        }
        return $this->hasAny($cond);
    }
    
	function activeField($id, $task, $value)
    {
        $this->create();
        $this->updateAll(array(
            'StoreCategory.'.$task => $value
        ), array(
            'StoreCategory.id' => $id,
        ));
    }
    
    function activeChildField($parent_id, $task, $value)
    {
        $cats = $this->children($parent_id);
        if($cats != null)
        {
            foreach($cats as $cat)
            {
                $this->updateAll(array(
                    'StoreCategory.'.$task => $value
                ), array(
                    'StoreCategory.id' => $cat['StoreCategory']['id'],
                ));
            }
        }
    }
    
    function enableParentField($id)
    {
        $this->create();
        $category = $this->findById($id);
        if(!empty($category['StoreCategory']['parent_id']))
        {
            $this->updateAll(array(
                'StoreCategory.enable' => 1
            ), array(
                'StoreCategory.id' => $category['StoreCategory']['parent_id'],
            ));
        }
    }
    
	function saveOrdering($id, $value)
    {
        $this->create();
        $this->updateAll(array(
            'StoreCategory.ordering' => $value,
        ),array(
            'StoreCategory.id' => $id,
        ));
    }
    
    public function getHref($store_category)
    {
        if(!empty($store_category['name']))
        {
            $request = Router::getRequest();
            return $request->base.'/stores/'.seoUrl($store_category['name']).'-'.$store_category['id'];
        }
        return false;
    }
    
	public function loadManagerPaging($obj, $search = array())
    {       
        //pagination
        $this->unbindModel(array('belongsTo' => array('User')));
        $cond = array();
        if(!empty($search['keyword']))
        {
            $cond[] = "StoreCategory.name LIKE '%".$search['keyword']."%'";
        }
        if(isset($search['parent_id']) && (int)$search['parent_id'] >= 0)
        {
            $cond["StoreCategory.parent_id"] = $search['parent_id'];
        }
        $obj->Paginator->settings = array(
            'conditions' => $cond,
            'limit' => 20,
            'order' => array('StoreCategory.ordering' => 'ASC'),
        );
        try{
            $data = $obj->paginate('StoreCategory');
            if($data != null)
            {
                foreach($data as $k => $item)
                {
                    $data[$k]['StoreCategory']['child_count'] = $this->find('count', array(
                        'conditions' => array('StoreCategory.parent_id' => $item['StoreCategory']['id'])
                    ));
                }
            }
            return $data;
        } 
        catch (Exception $ex) {
            return null;
        }
    }
    
    public function loadStoreCategoryList()
    {
        $cond = array(
            'StoreCategory.enable' => 1
        );

        $data= $this->find('threaded', array(
            'conditions' => $cond,
            'order' => array('StoreCategory.ordering' => 'ASC'))
        );
        return $data;
    }
    
    public function loadStoreCategories($except_id = null, $parent_id = -1, $listId = array(), $enable = null)
    {
        $cond = array();
        if((int)$except_id > 0)
        {
            $cond[] = 'StoreCategory.id != '.$except_id;
        }
        if((int)$parent_id  >= 0)
        {
            $cond['StoreCategory.parent_id'] = $parent_id;
        }
        if($listId != null)
        {
            $cond[] = 'StoreCategory.id IN('.implode(',', $listId).')';
        }
        if(is_bool($enable))
        {
            $cond['StoreCategory.enable'] = $enable;
        }
        $data= $this->find('threaded', array(
            'conditions' => $cond,
            'order' => array('StoreCategory.ordering' => 'ASC'))
        );
        return $data;
    }
    
    public function loadStoreCategoryDetail($id, $enable = null)
    {
        $cond = array(
            'StoreCategory.id' => $id
        );
        if(is_bool($enable))
        {
            $cond['StoreCategory.enable'] = $enable;
        }
        return $this->find('first', array(
            'conditions' => $cond
        ));
    }
    
    public function loadCategoryByChild($id)
    {
        $cat = $this->_loadCategoryByChild($id);
        return $cat;
    }
    
    private function _loadCategoryByChild($id)
    {
        $cat = $this->findById($id);
        if($cat != null && $cat['StoreCategory']['parent_id'] > 0)
        {
            $parent = $this->_loadCategoryByChild($cat['StoreCategory']['parent_id']);
            $parent['StoreCategory']['children'] = $cat;
        }
        else
        {
            $parent = $cat;
        }
        return $parent;
    }
    
    public function loadStoreCategoryTree()
    {
        $proCats = $this->find('threaded', array(
            'conditions' => array(
                'StoreCategory.enable' => 1
            ),
            'order' => array('StoreCategory.ordering ASC'))
        );
        return $proCats;
    }
    
    public function isContainProduct($store_category_id)
    {
        $mStoreProduct = MooCore::getInstance()->getModel("Store.StoreProduct");
        return $mStoreProduct->hasAny(array(
            "StoreProduct.store_category_id" => $store_category_id
        ));
    }
    
    public function checkHasChildren($parent_id){
    	return $this->hasAny(array(
    	    'StoreCategory.parent_id' => $parent_id
    	));
    }
    
    public function suggestCategory($keyword, $global = false)
    {
        $this->recursive = -1;
        $keyword = str_replace("'", "\\'", $keyword);
        $data = $this->find('all', array(
            'conditions' => array(
                "I18n__nameTranslation.content LIKE '%$keyword%'",
                'StoreCategory.enable' => 1
            )
        ));
        $result = null;
        if($data != null)
        {
            foreach($data as $item)
            {
                if($global)
                {
                    $label = $item['StoreCategory']['name'];
                }
                else
                {
                    $label = $this->getCategoryPath($item['StoreCategory']['id']);
                }
                $result[] = array(
                    'value' => $item['StoreCategory']['id'],
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
                $data[$category['StoreCategory']['id']] = $category['StoreCategory']['name'];
            }
            if($array)
            {
                return $data;
            }
            $data = implode('->', $data);
        }
        return $data;
    }
}