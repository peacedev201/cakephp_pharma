<?php 
App::uses('StoreAppModel', 'Store.Model');
class StoreProductVideo extends StoreAppModel{
    public $validationDomain = 'store';
    public $actsAs = array(
        'Storage.Storage' => array(
            'type' => array('products' => 'filename'),
        ),
    );
    
    function loadProductVideo($product_id, $params = array())
    {
        $cond = array(
            'StoreProductVideo.product_id' => $product_id
        );
        if(isset($params['enable']))
        {
            $cond['StoreProductVideo.enable'] = $params['enable'];
        }
        $input_data = array(
            'conditions' => $cond,
            'order' => array('StoreProductVideo.ordering ASC')
        );
        if(isset($params['limit']))
        {
            $input_data['limit'] = $params['limit'];
        }
        if(isset($params['page']))
        {
            $input_data['page'] = $params['page'];
        }
        $data = $this->find('all', $input_data);
        return $this->parseProductVideos($data);
    }
    
    function loadVideoDetail($id = null, $store_product_video_id = null)
    {
        if($id > 0)
        {
            $videoHelper = MooCore::getInstance()->getHelper('Video_Video');
            $mVideo = MooCore::getInstance()->getModel('Video_Video');
            $video = $mVideo->findById($id);
            if(!empty($video))
            {
                $video['Video']['image_url'] = $videoHelper->getImage($video, array('prefix' => '250'));
            }
            return $video;
        }
        else
        {
            $this->bindModel(array(
                'belongsTo' => array('Video' => array(
                    'className' => 'Video',
                    'foreignKey' => 'video_id',
                    'dependent' => true
                ))
            ));
            return $this->findById($store_product_video_id);
        }
    }
    
    function parseProductVideos($data)
    {
        if($data != null)
        {
            $mVideo = MooCore::getInstance()->getModel('Video.Video');
			$mVideo->unbindModel(array(
				'belongsTo' => array('User', 'Category', 'Group')
			));
            $videoHelper = MooCore::getInstance()->getHelper('Video_Video');
            foreach($data as $k => $item)
            {
                $item = $item['StoreProductVideo'];
                $video = $mVideo->findById($item['video_id']);
                if($video == null)
                {
                    continue;
                }
                $data[$k]['StoreProductVideo']['image_url'] = $videoHelper->getImage($video, array('prefix' => '250'));
                $data[$k]['StoreProductVideo']['title'] = $video['Video']['title'];
                $data[$k]['StoreProductVideo']['thumb'] = $video['Video']['thumb'];
				$data[$k]['ProductVideo'] = $video;
            }
        }
        return $data;
    }
    
    function loadVideoPaging($obj, $params = array(), $limit = 10)
    {
        //load data
        $cond = array();
        if(!empty($params['user_id']))
        {
            $cond["Video.user_id"] = $params['user_id'];
        }
        if(!empty($params['keyword']))
        {
            $keyword = trim($params['keyword']);
            $cond[] = "Video.title LIKE '%$keyword%'";
        }
        if(!empty($params['except_id']))
        {
            $cond[] = "Video.id NOT IN(".$params['except_id'].")";
        }

        try
        {
            $obj->Paginator->settings=array(
                'conditions' => $cond,
                'order' => array('Video.id' => 'DESC'),
                'limit' => $limit,
            );
            $data = $obj->paginate('Video');
            return $data;
        } 
        catch (Exception $ex) {
            return null;
        }
    }
    
    function deleteAllByVideoId($id)
    {
        $this->deleteAll(array(
            'StoreProductVideo.video_id' => $id
        ));
    }
}