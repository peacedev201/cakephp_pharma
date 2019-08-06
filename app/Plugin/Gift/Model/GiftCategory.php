<?php
class GiftCategory extends GiftAppModel 
{
    public $mooFields = array('href','url');
    public $actsAs = array(
        'Tree',
        'Translate' => array('name' => 'nameTranslation', 'description' => 'descriptionTranslation')
    );
	public $validate = array(   
        'name' =>   array(   
            'rule'     => 'notBlank',
            'message'  => 'Name is required'
        )  
    );
    
    private $_default_locale = 'eng' ;
    public function setLanguage($locale) {
        $this->locale = $locale;
    }
    
    public function getHref($row) 
    {
        $request = Router::getRequest();
        if (isset($row['name']) && isset($row['id']))
        {
            return $request->base.'/gifts/index/cat/' . $row['id'] . '/' . seoUrl($row['name']);
        }
        return '';
    }
    
    public function getUrl($row) 
    {
        $request = Router::getRequest();
        if (isset($row['name']) && isset($row['id']))
        {
            return '/gifts/index/cat/' . $row['id'] . '/' . seoUrl($row['name']);
        }
        return '';
    }

    public function isIdExist($id)
    {
        return $this->hasAny(array('GiftCategory.id' => $id));
    }
    
    public function isHasGifts($id)
    {
        $mGift = MooCore::getInstance()->getModel('Gift.Gift');
        return $mGift->hasAny(array(
            'gift_category_id' => $id
        ));
    }

    public function deleteCategory($id)
    {
        return $this->delete($id);
    }
    
    public function getCategories($type = 'all')
    {
        return $this->find($type, array(
            'conditions' => array(
                'GiftCategory.enable' => 1
            )
        ));
    }
    
    public function updateGiftItemCount($gift_id)
    {
        $mGift = MooCore::getInstance()->getModel('Gift.Gift');
        
        $gift = $mGift->findById($gift_id);
        if($gift != null)
        {
            $count = $mGift->find('count', array(
                'conditions' => array(
                    'Gift.is_public' => 1,
                    'Gift.enable' => 1,
                    'Gift.gift_category_id' => $gift['Gift']['gift_category_id']
                )
            ));
            
            $this->updateAll(array(
                'GiftCategory.item_count' => $count
            ), array(
                'GiftCategory.id' => $gift['Gift']['gift_category_id']
            ));
        }
    }
    
    public function decreaseCounter($id, $field = 'comment_count')
    {
        $this->query("UPDATE $this->tablePrefix$this->table SET $field=$field-1 WHERE id=" . intval($id));
    }
    
    public function getCatById($id) {
        $this->recursive = 2;
        $category = $this->findById($id);
        if (empty($category)) {
            $this->locale = $this->_default_locale;
            $category = $this->findById($id);
        }
        return $category ;
    }
}