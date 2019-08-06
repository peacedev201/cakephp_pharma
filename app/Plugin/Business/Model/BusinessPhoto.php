<?php
class BusinessPhoto extends BusinessAppModel 
{
    public $useTable = 'photos';
    public $alias = 'BusinessPhoto'; 
    public $actsAs = array(
        'MooUpload.Upload' => array(
            'thumbnail' => array(
                'path' => '{ROOT}webroot{DS}uploads{DS}photos{DS}{field}{DS}{year}{DS}{month}{DS}{day}{DS}',
            )
        )
    );
    
    public function deleteByBusiness($business_id)
    {
        return $this->deleteAll(array(
            'BusinessPhoto.target_id' => $business_id,
            'BusinessPhoto.type' => BUSINESS_MODULE_PHOTO
        ));
    }
    
    public function deletePhoto($id)
    {
        $photo = $this->findById($id);
        if($photo != null && $this->delete($id))
        {
            $original_path = WWW_ROOT.BUSINESS_FILE_PATH.'/'.$photo['BusinessPhoto']['filename'];
            $thumb_path = WWW_ROOT.BUSINESS_FILE_PATH.'/'.BUSINESS_IMAGE_THUMB_WIDTH.'_'.$photo['BusinessPhoto']['filename'];
            if(file_exists($original_path))
            {
                unlink($original_path);
            }
            if(file_exists($thumb_path))
            {
                unlink($thumb_path);
            }
        }
    }
    
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
    
    public function getPhotos($business_id, $page = 1, $limit = 0)
    {
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        
        //get album id
        $business = $mBusiness->getOnlyBusiness($business_id);
        if($business == null)
        {
            return array();
        }
        
        //get photos
        if($limit == 0)
        {
            $limit = Configure::read('Business.business_photo_per_page');
        }
        $cond = array(
            'BusinessPhoto.target_id' => $business['Business']['album_id'],
            'BusinessPhoto.type' => 'Photo_Album'
        );
        return $this->find('all', array(
            'conditions' => $cond,
            'limit' => $limit,
            'order' => array('BusinessPhoto.id' => 'DESC'),
            'page' => $page
        ));
    }
    
    public function updateBusinessPhotoCounter($business_id)
    {
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        $business = $mBusiness->getOnlyBusiness($business_id);
        
        if($business != null)
        {
            //find total photo
            $total = $this->find('count', array(
                'conditions' => array(
                    'BusinessPhoto.target_id' => $business['Business']['album_id'],
                    'BusinessPhoto.type' => 'Photo_Album'
                )
            ));

            //update business photo count
            $mBusiness->updateAll(array(
                'Business.photo_count' => $total
            ), array(
                'Business.id' => $business_id
            ));
        }
    }
    
    public function savePhotoActivity($business_id, $photo_ids)
    {
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        $photo_ids = !empty($photo_ids) ? implode(',', $photo_ids) : '';
        return $mBusiness->saveActivity($business_id, BUSINESS_ACTIVITY_PHOTO_ACTION, BUSINESS_ACTIVITY_PHOTO_ITEM, 0, $photo_ids);
    }
    
    public function getBusinessAlbumPhotos($business_id, $all = false)
    {
        $mBusiness = MooCore::getInstance()->getModel('Business.Business');
        $business = $mBusiness->getOnlyBusiness($business_id);
        if(!empty($business['Business']['album_id']))
        {
            $cond = array(
                'BusinessPhoto.target_id' => $business['Business']['album_id'],
                'BusinessPhoto.type' => 'Photo_Album',
            );
            return $this->find('all', array(
                'conditions' => $cond
            ));
        }
        return null;
    }
    
    public function updateAlbumPhotoCounter($album_id)
    {
        if($album_id > 0)
        {
            $mPhoto = MooCore::getInstance()->getModel('Photo.Photo');
            $mAlbum = MooCore::getInstance()->getModel('Photo.Album');
            
            //total photo
            $total = $mPhoto->find('count', array(
                'conditions' => array(
                    'Photo.target_id' => $album_id,
                    'Photo.type' => 'Photo_Album'
                )
            ));
            
            //get cover
            $cover = $mPhoto->find('first', array(
                'conditions' => array(
                    'Photo.target_id' => $album_id,
                    'Photo.type' => 'Photo_Album'
                ),
                'limit' => 1,
                'order' => array('Photo.id' => 'DESC')
            ));
          
            if(!empty($cover) && !empty($cover['Photo']['thumbnail'])){
                $mAlbum->updateAll(array(
                    'Album.photo_count' => $total,
                    'Album.cover' => !empty($cover) ? "'".$cover['Photo']['thumbnail']."'" : ''
                ), array(
                    'Album.id' => $album_id
                ));
            }else{
                $mAlbum->updateAll(array(
                    'Album.photo_count' => $total
                ), array(
                    'Album.id' => $album_id
                ));
            }
        }
    }
}