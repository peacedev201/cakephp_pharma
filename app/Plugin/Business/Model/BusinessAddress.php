<?php
class BusinessAddress extends BusinessAppModel 
{
    public function deleteByBusiness($business_id)
    {
        return $this->deleteAll(array(
            'BusinessAddress.business_id' => $business_id 
        ));
    }
}