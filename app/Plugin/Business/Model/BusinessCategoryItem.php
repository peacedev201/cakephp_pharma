<?php
class BusinessCategoryItem extends BusinessAppModel 
{
    public function deleteByBusiness($business_id)
    {
        return $this->deleteAll(array(
            'BusinessCategoryItem.business_id' => $business_id 
        ));
    }
    public function deleteBusinessCategoryItem($id){
        $this->delete($id);
    }
    
    public function getListCategoryId($business_id)
    {
        return $this->find('list', array(
            'conditions' => array(
                'BusinessCategoryItem.business_id' => $business_id
            ),
            'fields' => array('BusinessCategoryItem.business_category_id')
        ));
    }
}