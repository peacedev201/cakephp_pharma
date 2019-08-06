<?php
class BusinessCheckin extends BusinessAppModel 
{
    public function getPeopleCheckin($business_id, $page = 1, $limit = 0)
    {
        $mUserTagging = MooCore::getInstance()->getModel("UserTagging");
        if($limit == 0)
        {
            $limit = Configure::read('Business.business_people_checkin_items');
        }
        $this->bindModel(array(
            'belongsTo' => array(
                'User' => array(
                    'className' => 'User',
                    'foreignKey' => 'user_id',
                    'dependent' => true,
                ),
                'Business' => array(
                    'className' => 'Business.Business',
                    'foreignKey' => 'business_id',
                    'dependent' => true
                )
            )
        ));
        
        $data = $this->find('all', array(
            'conditions' => array(
                'BusinessCheckin.business_id' => $business_id
            ),
            'page' => $page,
            'limit' => $limit,
            'order' => array('BusinessCheckin.id' => 'DESC')
        ));
        if($data)
        {
            foreach($data as $k => $v)
            {
                $tagging = $mUserTagging->findByItemIdAndItemTable($v['BusinessCheckin']['id'], 'business_checkin');
                $data[$k]['UserTagging'] = $tagging != null ? $tagging['UserTagging'] : array();
            }
        }
        return $data;
    }
}