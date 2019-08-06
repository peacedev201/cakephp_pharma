<?php 
App::uses('StoreAppModel', 'Store.Model');
class StoreOrderDetail extends StoreAppModel
{
    function deleteByOrderId($order_id)
    {
        $this->deleteAll(array(
            'order_id' => $order_id
        ));
    }
    
    public function loadProductIdList($order_id)
    {
        return $this->find('list', array(
            'conditions' => array(
                'StoreOrderDetail.order_id' => $order_id
            ),
            'fields' => 'StoreOrderDetail.product_id'
        ));
    }
}