<?php
class StoreReviewPhoto extends StoreAppModel 
{
    public $useTable = 'photos';
    public $alias = 'StoreReviewPhoto'; 
    public $actsAs = array(
        'MooUpload.Upload' => array(
            'thumbnail' => array(
                'path' => '{ROOT}webroot{DS}uploads{DS}photos{DS}{field}{DS}{year}{DS}{month}{DS}{day}{DS}',
            )
        )
    );
    
    public function deletePhotoList($list)
    {
        if(!is_array($list))
        {
            $list = explode(',', $list);
        }
        if($list != null)
        {
            foreach($list as $item)
            {
                $this->delete($item);
            }
        }
    }
}