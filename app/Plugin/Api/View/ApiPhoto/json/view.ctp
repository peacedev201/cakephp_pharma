<?php
$photoHelper = MooCore::getInstance()->getHelper('Photo_Photo');
$friendModel = MooCore::getInstance()->getModel('Friend');
$photoModel = MooCore::getInstance()->getModel('Photo_Photo'); 
$prePhoto = $nextPhoto = $tagArray= array();
foreach ($photo_tags as $tag):
    $tagArray[] = array (
        'id' => $tag['User']['id'],
        'name' => $tag['User']['name'],
        'url' => FULL_BASE_URL . $tag['User']['moo_href'],
        'tagger_id' => $tag['PhotoTag']['tagger_id'],
    );
endforeach;
$isViewerLiked = $isViewerDisliked = false; 
    if (!empty( $like['Like']) && $like['Like']['user_id'] == $uid   ){
        $like['Like']['thumb_up'] == 1 ? $isViewerLiked = true : $isViewerDisliked = true;
    }
$photoSizes = explode('|', Configure::read('core.photo_image_sizes'));
$imageArray = array();
    foreach ($photoSizes as $size) {
        $imageArray[$size] = $photoHelper->getImage($photo, array('prefix' => $size));
    }
if (!empty($neighbors['next']['Photo']['id'])): $nextPhoto = $neighbors['next']['Photo']['id']; endif;
if (!empty($neighbors['prev']['Photo']['id'])): $prePhoto = $neighbors['prev']['Photo']['id']; endif;

if ($is_show_full_photo) :
    $canView = true;
    $viewMess = '';
else:
    $canView = false;
    $viewMess = __("You can't view or interact with this image because of view privacy.");
endif;
$photoArray = array(
        'id' => $photo['Photo']['id'],
        'published' => h($photo['Photo']['created']),
        'publishedTranslated' => $this->Moo->getTime($photo['Photo']['created'], Configure::read('core.date_format'), $utz),
        'caption' => $photo['Photo']['caption'],
        'albumTitle' => $photo['Album']['title'],
        'thumbnail' => $imageArray,
        'privacy' => $photo['Photo']['privacy'],
        'type' => $photo['Photo']['type'],
        'albumType' => $photo['Photo']['album_type'] ? $photo['Photo']['album_type'] : $photo['Photo']['type'] ,
        'url' => FULL_BASE_URL . $photo['Photo']['moo_href'],
        'commentCount' => $photo['Photo']['comment_count'],
        'likeCount' => $photo['Photo']['like_count'],
        'dislikeCount' => $photo['Photo']['dislike_count'],
        'userId' => $photo['Photo']['user_id'],
        'userName' => $photo['User']['name'],
        'userUrl' => FULL_BASE_URL . $photo['User']['moo_href'],
        'tagged' => $tagArray ,
        'isViewerLiked' => $isViewerLiked ,
        'isViewerDisliked' => $isViewerDisliked ,
        'nextPhotoId' => $nextPhoto,
        'prePhotoId' => $prePhoto,
        'shareAction' => "photo_item_detail",
        'canView' => $canView,
        'viewMessage' => $viewMess,
);

$afterRenderApiPhoto = new CakeEvent("Api.View.ApiPhoto.afterRenderApiPhotoDetail", $this, array(
    'item_id' => $photo['Photo']['id'],
    'item_type' => $photo['Photo']["moo_type"],
    'data' => $photo,
    'data_json' => $photoArray,
));

$this->getEventManager()->dispatch($afterRenderApiPhoto);
if(!empty($afterRenderApiPhoto->result['result'])) {
    $result = $afterRenderApiPhoto->result['result'];
    $photoArray = array_merge($photoArray, $result);
}

echo json_encode($photoArray);