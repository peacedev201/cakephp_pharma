<?php
App::uses('CakeEventListener', 'Event');

class StickerStorageListener implements CakeEventListener
{
    public function implementedEvents()
    {
        return array(
            'StorageHelper.sticker_category.getUrl.local' => 'storage_geturl_local',
            'StorageHelper.sticker_category.getUrl.amazon' => 'storage_geturl_amazon',
            'StorageAmazon.sticker_category.getFilePath' => 'storage_amazon_get_file_path',
            
            'StorageHelper.sticker_icon.getUrl.local' => 'storage_geturl_local',
            'StorageHelper.sticker_icon.getUrl.amazon' => 'storage_geturl_amazon',
            'StorageAmazon.sticker_icon.getFilePath' => 'storage_amazon_get_file_path',
            
            'StorageHelper.sticker_image.getUrl.local' => 'storage_geturl_local',
            'StorageHelper.sticker_image.getUrl.amazon' => 'storage_geturl_amazon',
            'StorageAmazon.sticker_image.getFilePath' => 'storage_amazon_get_file_path',
            
            'StorageHelper.sticker_image_app.getUrl.local' => 'storage_geturl_local',
            'StorageHelper.sticker_image_app.getUrl.amazon' => 'storage_geturl_amazon',
            'StorageAmazon.sticker_image_app.getFilePath' => 'storage_amazon_get_file_path',
            
            'StorageTaskAwsCronTransfer.execute' => 'storage_task_transfer',
        );
    }
    
    public function storage_geturl_local($e)
    {
        $v = $e->subject();
        $request = Router::getRequest();
        $oid = $e->data['oid'];
        $type = $e->data['type'];
        $thumb = $e->data['thumb'];
        $prefix = $e->data['prefix'];

        switch ($type)
        {
            case 'sticker_category':
                $url = "";
                if ($e->data['thumb'])
                {
                    $url = FULL_BASE_LOCAL_URL . $request->webroot . STICKER_UPLOAD_URL_CATEGORY .'/'. $prefix . $thumb;
                }
                break;
            case 'sticker_icon':
                $url = "";
                if ($e->data['thumb'])
                {
                    $url = FULL_BASE_LOCAL_URL . $request->webroot . STICKER_UPLOAD_URL_STICKER .'/'. $prefix . $thumb;
                }
                break;
            case 'sticker_image':
                $url = "";
                if ($e->data['thumb'])
                {
                    $url = FULL_BASE_LOCAL_URL . $request->webroot . STICKER_UPLOAD_URL_STICKER .'/'. $prefix . $thumb;
                }
            case 'sticker_image_app':
                $url = "";
                if ($e->data['thumb'])
                {
                    $url = FULL_BASE_LOCAL_URL . $request->webroot . STICKER_UPLOAD_URL_STICKER .'/'. $prefix . $thumb;
                }
                break;
        }
        $e->result['url'] = $url;
    }

    public function storage_geturl_amazon($e)
    {
        $v = $e->subject();
        $type = $e->data['type'];
        switch ($type)
        {
            case 'sticker_category':
                $e->result['url'] = $v->getAwsURL($e->data['oid'], "sticker_category", $e->data['prefix'], $e->data['thumb']);
                break;
            case 'sticker_icon':
                $e->result['url'] = $v->getAwsURL($e->data['oid'], "sticker_icon", $e->data['prefix'], $e->data['thumb']);
                break;
            case 'sticker_image':
                $e->result['url'] = $v->getAwsURL($e->data['oid'], "sticker_image", $e->data['prefix'], $e->data['thumb']);
                break;
            case 'sticker_image_app':
                $e->result['url'] = $v->getAwsURL($e->data['oid'], "sticker_image_app", $e->data['prefix'], $e->data['thumb']);
                break;
        }
    }

    public function storage_amazon_get_file_path($e)
    {
        $objectId = $e->data['oid'];
        $name = $e->data['name'];
        $thumb = $e->data['thumb'];
        $type = $e->data['type'];
        $path = false;
        switch ($type)
        {
            case 'sticker_category':
                $path = WWW_ROOT.STICKER_UPLOAD_URL_CATEGORY . '/' . $thumb;
                break;
            case 'sticker_icon':
                $path = WWW_ROOT.STICKER_UPLOAD_URL_STICKER . '/' . $thumb;
                break;
            case 'sticker_image':
                $path = WWW_ROOT.STICKER_UPLOAD_URL_STICKER . '/' . $thumb;
                break;
            case 'sticker_image_app':
                $path = WWW_ROOT.STICKER_UPLOAD_URL_STICKER . '/' . $thumb;
                break;
        }
        $e->result['path'] = $path;
    }
}