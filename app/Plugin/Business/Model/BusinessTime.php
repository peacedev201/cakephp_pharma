<?php
class BusinessTime extends BusinessAppModel 
{
    public function deleteByBusiness($business_id)
    {
        return $this->deleteAll(array(
            'BusinessTime.business_id' => $business_id 
        ));
    }
}