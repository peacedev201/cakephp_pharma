<?php 
App::uses('StoreAppModel', 'Store.Model');
class StoreProductImage extends StoreAppModel{
    public $actsAs = array(
        'Storage.Storage' => array(
            'type' => array('products' => 'filename'),
        ),
    );
    
    function loadProductImage($product_id, $is_main = false)
    {
        $cond = array(
            'StoreProductImage.product_id' => $product_id
        );
        if($is_main)
        {
            $cond['StoreProductImage.is_main'] = 1;
        }
        $data = $this->find('all', array(
            'conditions' => $cond,
            'order' => array('StoreProductImage.ordering ASC')
        ));
        return $this->parseProductImages($data);
    }
    
    function parseProductImages($data)
    {
        if($data != null)
        {
            foreach($data as $k => $item)
            {
                $item = $item['StoreProductImage'];
                $data[$k]['StoreProductImage']['thumb'] = Router::url('/', true).PRODUCT_UPLOAD_URL.$item['path'].'/'.PRODUCT_PHOTO_THUMB_WIDTH.'_'.$item['filename'];
                $data[$k]['StoreProductImage']['large'] = Router::url('/', true).PRODUCT_UPLOAD_URL.$item['path'].'/'.PRODUCT_PHOTO_LARGE_WIDTH.'_'.$item['filename'];
            }
        }
        return $data;
    }
    
    function renameImage($filename, $date_path, $newname)
    {
        $path = 'uploads' . DS . 'products'.DS.$date_path;
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $newname = $newname.'.'.$ext;
        
        $original_photo_path = $path.DS.$filename;
        $tiny_photo_path = $path.DS . PRODUCT_PHOTO_TINY_WIDTH.'_'.$filename;
        $thumb_photo_path = $path.DS. PRODUCT_PHOTO_THUMB_WIDTH.'_'.$filename;
        $large_photo_path = $path.DS . PRODUCT_PHOTO_LARGE_WIDTH.'_'.$filename;
        
        $new_original_photo_path = $path.DS.$newname;
        $new_tiny_photo_path = $path.DS . PRODUCT_PHOTO_TINY_WIDTH.'_'.$newname;
        $new_thumb_photo_path = $path.DS. PRODUCT_PHOTO_THUMB_WIDTH.'_'.$newname;
        $new_large_photo_path = $path.DS . PRODUCT_PHOTO_LARGE_WIDTH.'_'.$newname;
        
        if(file_exists($original_photo_path))
        {
            rename($original_photo_path, $new_original_photo_path);
        }
        if(file_exists($tiny_photo_path))
        {
            rename($tiny_photo_path, $new_tiny_photo_path);
        }
        if(file_exists($thumb_photo_path))
        {
            rename($thumb_photo_path, $new_thumb_photo_path);
        }
        if(file_exists($large_photo_path))
        {
            rename($large_photo_path, $new_large_photo_path);
        }
        return $newname;
    }
    
    function loadImagePaging($obj, $params = array(), $limit = 10)
    {
        //load data
        $cond = array();
        if(!empty($params['user_id']))
        {
            $cond["Photo.user_id"] = $params['user_id'];
        }
        if(!empty($params['keyword']))
        {
            $keyword = trim($params['keyword']);
            $cond[] = "Album.title LIKE '%$keyword%'";
        }
        if(!empty($params['except_id']))
        {
            $cond[] = "Photo.id NOT IN(".$params['except_id'].")";
        }

        try
        {
            $obj->Paginator->settings=array(
                'conditions' => $cond,
                'order' => array('Photo.id' => 'DESC'),
                'limit' => $limit,
            );
            $data = $obj->paginate('Photo');
            return $data;
        } 
        catch (Exception $ex) {
            return null;
        }
    }
    
    function unlinkAllProdiuctImage($path, $filename)
    {
        $prefixes = array(
            PRODUCT_PHOTO_TINY_WIDTH, PRODUCT_PHOTO_THUMB_WIDTH, PRODUCT_PHOTO_LARGE_WIDTH
        );
        foreach($prefixes as $prefix)
        {
            $source = WWW_ROOT.DS.PRODUCT_UPLOAD_DIR.$path.DS.$prefix.'_'.$filename;
            if(file_exists($source))
            {
                unlink($source);
            }
        }
    }
    
    public function createImagePath()
    {
        $curYear = date('Y');
        $curMonthDate = date('m').'_'.date('d');
        $path = 'uploads' . DS . 'products';
        $path_year = $path.DS.$curYear;
        $path_month_date = $path.DS.$curYear.DS.$curMonthDate;
        $result = array(
            'path_date' => $curYear.'/'.$curMonthDate,
            'path' => $path_month_date,
            'url' => str_replace(DS, '/', $path_month_date)
        );
        if(!is_dir($path_month_date))
        {
            if(!is_dir($path))
            {
                $mask = umask(0);
                mkdir($path, 0777);
                umask($mask);
            }
            if(!is_dir($path_year))
            {
                $mask = umask(0);
                mkdir($path_year, 0777);
                umask($mask);
            }
            if(!is_dir($path_month_date))
            {
                $mask = umask(0);
                mkdir($path_month_date, 0777);
                umask($mask);
            }
            return $result;
        }
        else
        {
            return $result;
        }
        return array();
    }
}