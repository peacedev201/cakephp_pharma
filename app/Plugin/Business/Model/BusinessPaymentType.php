<?php
class BusinessPaymentType extends BusinessAppModel 
{
    public function deleteByBusiness($business_id)
    {
        return $this->deleteAll(array(
            'BusinessPaymentType.business_id' => $business_id 
        ));
    }
}