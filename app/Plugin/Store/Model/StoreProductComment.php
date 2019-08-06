<?php 
App::uses('StoreAppModel', 'Store.Model');
class StoreProductComment extends StoreAppModel{
    public $validationDomain = 'store';
    public $recursive = 1; 
    public $belongsTo = array(
        'User'=> array(
            'className' => 'User',
            'foreignKey' => 'user_id',
            'dependent' => true
    ));
    
    public $validate = array(   
        'email' => array(
            'rule' => array('email', true),
            'message' => 'Please provide a valid email address.'
        ),
        'comment' =>   array(   
            'rule' => 'notBlank',
            'message' => 'Please provide comment'
        )
    );
    
    public function beforeSave($options = array()) 
    {
        parent::beforeSave($options);
        $this->data['StoreProductComment']['store_id'] = Configure::read('store.store_id');
    }
    
    function loadManagerPaging($obj, $keyword = '')
    {
        //load data
        $cond = array(
            'StoreProductComment.store_id' => Configure::read('store.store_id')
        );
        if($keyword != '')
        {
            $cond[] = "StoreProductComment.comment LIKE '%".$keyword."%'";
        }
        $obj->Paginator->settings=array(
            'conditions' => $cond,
            'order' => array('StoreProductComment.id' => 'DESC'),
            'limit' => 5,
        );
        $data = $obj->paginate('StoreProductComment');
        return $data;
    }
    
    function checkProductCommentExist($id, $enable = '', $store_id = null)
    {
        if($store_id == null)
        {
            $store_id = Configure::read('store.store_id');
        }
        $cond = array(
            'store_id' => $store_id,
            'id' => (int)$id 
        );
        if($enable != '')
        {
            $cond['enable'] = $enable;
        }
        return $this->hasAny($cond);
    }
    
    function checkRated($product_id)
    {
        return $this->hasAny(array(
            'StoreProductComment.store_id' => Configure::read('store.store_id'),
            'StoreProductComment.user_id' => Configure::read('store.uid'),
            'StoreProductComment.product_id' => $product_id
        ));
    }
    
    function getProductRateValue($product_id)
    {
        $data = $this->find('first', array(
            'conditions' => array(
                'StoreProductComment.user_id' => Configure::read('store.uid'),
                'StoreProductComment.product_id' => $product_id
            )
        ));
        if($data != null)
        {
            return $data['StoreProductComment']['rating'];
        }
        return 0;
    }
    
    function saveComment($data)
    {
        return $this->save($data);
    }
    
    function activeField($id, $task, $value)
    {
        $this->create();
        $this->updateAll(array(
            'StoreProductComment.'.$task => $value
        ), array(
            'StoreProductComment.store_id' => Configure::read('store.store_id'),
            'StoreProductComment.id' => $id,
        ));
    }
    
    function deleteProductComment($id)
    {
        $this->deleteAll(array(
            'StoreProductComment.store_id' => Configure::read('store.store_id'),
            'StoreProductComment.id' => $id,
        ));
    }
    
    function loadCommentList($obj, $product_id)
    {
        $cond = array(
            'StoreProductComment.enable' => 1,
            'StoreProductComment.product_id' => $product_id
        );
        $sort = array('StoreProductComment.id DESC');
        
        $obj->Paginator->settings = array(
            'conditions' => $cond,
            'order' => $sort,
            'limit' => Configure::read('store.comment_per_page'),
        );
        return $obj->paginate('StoreProductComment');
    }
    
    function loadCommentDetail($id)
    {
        return $this->findById($id);
    }
    
    function totalComment($product_id)
    {
        return $this->find('count', array(
            'conditions' => array(
                'StoreProductComment.enable' => 1,
                'StoreProductComment.product_id' => $product_id
            )
        ));
    }
    
    function totalSiteComments()
    {
        return $this->find('count', array(
            'conditions' => array(
                'store_id' => Configure::read('store.store_id')
            )
        ));
    }
}