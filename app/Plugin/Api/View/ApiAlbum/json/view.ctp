<?php
$photoSizes = explode('|', Configure::read('core.photo_image_sizes'));
$photoHelper = MooCore::getInstance()->getHelper('Photo_Photo');
$friendModel = MooCore::getInstance()->getModel('Friend');
$photoModel = MooCore::getInstance()->getModel('Photo_Photo');
    $covertPhoto = $covert = array();
        if ($album['Album']['type'] == 'newsfeed' &&  $role_id != ROLE_ADMIN && $uid != $album['Album']['user_id'] && (!$uid || $friendModel->areFriends($uid,$album['Album']['user_id'])))  
    {    		
	$photo = $photoModel->getPhotoCoverOfFeedAlbum($album['Album']['id']);
            if ($photo)
	    {
                //$covert = $photoHelper->getImage($photo, array('prefix' => '150_square'));
                foreach ($photoSizes as $size) {
                    $covert[$size] = $photoHelper->getImage($photo, array('prefix' => $size));
                }
	    }
	    else
	    {
	    	//$covert = $photoHelper->getAlbumCover('', array('prefix' => '150_square'));
                foreach ($photoSizes as $size) {
                    $covert[$size] = $photoHelper->getAlbumCover($photo, array('prefix' => $size));
                }
	    }
    }
    else
    {
    	//$covert = $photoHelper->getAlbumCover($album['Album']['cover'], array('prefix' => '150_square'));
        foreach ($photoSizes as $size) {
                    $covert[$size] = $photoHelper->getAlbumCover($album['Album']['cover'], array('prefix' => $size));
                }
    }
    $photoArray = array();
    foreach ($photos as $photoAlbum) :
        foreach ($photoSizes as $size) { 
            $covertPhoto[$size] = $photoHelper->getImage($photoAlbum, array('prefix' => $size));
        }
        $photoArray[] = array (
            'id' => $photoAlbum['Photo']['id'],
            'url' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($photoAlbum['Photo']['moo_href'], 'UTF-8', 'UTF-8')),
            'published' => h($photoAlbum['Photo']['created']),
            'publishedTranslated' => $this->Moo->getTime($photoAlbum['Photo']['created'], Configure::read('core.date_format'), $utz),
            'targetId' => $photoAlbum['Photo']['target_id'],
            'type' => $photoAlbum['Photo']['type'],
            'thumbnail' => $covertPhoto ,
            'caption' => $photoAlbum['Photo']['caption'],
            'privacy' => $photoAlbum['Photo']['privacy'],
            'commentCount' => $photoAlbum['Photo']['comment_count'],
            'likeCount' => $photoAlbum['Photo']['like_count'],
            'dislikeCount' => $photoAlbum['Photo']['dislike_count'],
        );
    endforeach;
    
    $isViewerLiked = $isViewerDisliked = false; 
    if (!empty( $like['Like']) && $like['Like']['user_id'] == $uid   ){
        $like['Like']['thumb_up'] == 1 ? $isViewerLiked = true : $isViewerDisliked = true;
    }
    
    $albumArray = array(
        'id' => $album['Album']['id'],
        'published' => h($album['Album']['created']),
        'publishedTranslated' => $this->Moo->getTime($album['Album']['created'], Configure::read('core.date_format'), $utz),
        'title' => h($album['Album']['title']),
        'description' => $album['Album']['description'],
        'thumbnail' => $covert,
        'privacy' => $album['Album']['privacy'],
        'type' => "Photo_Album",//$album['Album']['type'] ? $album['Album']['type'] : $album['Album']['moo_type'],
        'currentType' => $album['Album']['type'] ? $album['Album']['type'] : $album['Album']['moo_type'],
        'photoObject' => $photoArray ,
        'categoryId' => $album['Album']['category_id'],
        'commentCount' => $album['Album']['comment_count'],
        'likeCount' => $album['Album']['like_count'],
        'dislikeCount' => $album['Album']['dislike_count'],
        'userId' => $album['Album']['user_id'],
        'userUrl' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($album['User']['moo_href'], 'UTF-8', 'UTF-8')) ,
        'userName' => $album['User']['name'],
        'isViewerLiked' => $isViewerLiked ,
        'isViewerDisliked' => $isViewerDisliked ,
        'canUploadPhoto' => (empty($album['Album']['type']) && $uid == $album['User']['id'] ) ? true : false ,
        'photoCount' => $album['Album']['photo_count'],
        'url' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($album['Album']['moo_href'], 'UTF-8', 'UTF-8')),
        'shareAction' => "album_item_detail",
    );

    $afterRenderApiAlbum = new CakeEvent("Api.View.ApiAlbum.afterRenderApiAlbumDetail", $this, array(
        'item_id' => $album['Album']['id'],
        'item_type' => "Photo_Album",
        'data' => $album,
        'data_json' => $albumArray,
    ));

    $this->getEventManager()->dispatch($afterRenderApiAlbum);
    if(!empty($afterRenderApiAlbum->result['result'])) {
        $result = $afterRenderApiAlbum->result['result'];
        $albumArray = array_merge($albumArray, $result);
    }

echo json_encode($albumArray);