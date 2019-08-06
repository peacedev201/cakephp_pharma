<?php
$videoHelper = MooCore::getInstance()->getHelper('Video_Video');
$photoSizes = explode('|', Configure::read('core.photo_image_sizes'));
if (empty($video)) {
    throw new ApiNotFoundException(__d('api', 'Video not found'));
}
else {
    $imageArray = $userImageArray = array();
    $userImageArray = array(
        '50_square' => $this->Moo->getItemPhoto(array('User' => $video['User']), array('prefix' => '50_square'), array(), true),
        '100_square' => $this->Moo->getItemPhoto(array('User' => $video['User']), array('prefix' => '100_square'), array(), true),
        '200_square' => $this->Moo->getItemPhoto(array('User' => $video['User']), array('prefix' => '200_square'), array(), true),
        '600' => $this->Moo->getItemPhoto(array('User' => $video['User']), array('prefix' => '600'), array(), true),
    );
    foreach ($photoSizes as $size) {
        $imageArray[$size] = $videoHelper->getImage($video, array('prefix' => $size));
    }
    $isViewerLiked = $isViewerDisliked = false; 
    if (!empty( $like['Like']) && $like['Like']['user_id'] == $uid   ){
        $like['Like']['thumb_up'] == 1 ? $isViewerLiked = true : $isViewerDisliked = true;
    }
    $videoSource = '';
    if($video['Video']['pc_upload']) {
        $videoSource = $videoHelper->getVideo($video);
    }
    
    $videoArray = array(
        'id' => $video['Video']['id'],
        'published' => h($video['Video']['created']),
        'publishedTranslated' => $this->Moo->getTime($video['Video']['created'], Configure::read('core.date_format'), $utz),
        'title' => h($video['Video']['title']),
        'description' => $this->Moo->formatText( $video['Video']['description'], false, true, array('no_replace_ssl' => 1) ),
        'thumbnail' => $imageArray,
        'videoType' => $video['Video']['source'],
        'videoSourceId' => $video['Video']['source_id'],
        'privacy' => $video['Video']['privacy'],
        'categoryId' => $video['Video']['category_id'],
        'commentCount' => $video['Video']['comment_count'],
        'likeCount' => $video['Video']['like_count'],
        'dislikeCount' => $video['Video']['dislike_count'],
        'userId' => $video['Video']['user_id'],
        'userName' => $video['User']['name'],
        'userAvatar' => $userImageArray,
        'userUrl' => FULL_BASE_URL . $video['User']['moo_href'],
        'url' => FULL_BASE_URL .  str_replace('?','',mb_convert_encoding($video['Video']['moo_href'], 'UTF-8', 'UTF-8')),
        'isViewerLiked' => $isViewerLiked ,
        'isViewerDisliked' => $isViewerDisliked ,
        'type' => $video['Video']['moo_type'],
        'groupName' => (isset($group) && $group) ? $group['Group']['moo_title'] : '',
        'groupId' => (isset($group) && $group) ? $group['Group']['id'] : '',
        'groupUrl' => (isset($group) && $group) ? FULL_BASE_URL . str_replace('?','',mb_convert_encoding($group['Group']['moo_href'], 'UTF-8', 'UTF-8')) : '',
        'pcUpload'=>$video['Video']['pc_upload'],
        'videoSource'=>$videoSource,
        'shareAction' => "video_item_detail",
    );

    $afterRenderApiVideo = new CakeEvent("Api.View.ApiVideo.afterRenderApiVideoDetail", $this, array(
        'item_id' => $video['Video']['id'],
        'item_type' => $videoArray["type"],
        'data' => $video,
        'data_json' => $videoArray,
    ));

    $this->getEventManager()->dispatch($afterRenderApiVideo);
    if(!empty($afterRenderApiVideo->result['result'])) {
        $result = $afterRenderApiVideo->result['result'];
        $videoArray = array_merge($videoArray, $result);
    }

echo json_encode($videoArray);
}