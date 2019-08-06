<?php
$topicHelper = MooCore::getInstance()->getHelper('Topic_Topic');
$photoSizes = explode('|', Configure::read('core.photo_image_sizes'));
if (empty($topic)) {
    throw new ApiNotFoundException(__d('api', 'Topic not found'));
}
else {
    $isViewerLiked = $isViewerDisliked = false; 
    if (!empty( $like['Like']) && $like['Like']['user_id'] == $uid   ){
        $like['Like']['thumb_up'] == 1 ? $isViewerLiked = true : $isViewerDisliked = true;
    }
    $imageArray = array();
    foreach ($photoSizes as $size) {
        $imageArray[$size] = $topicHelper->getImage($topic, array('prefix' => $size));
    }
    if ($topic['Topic']['locked'] ) {
        if ((!empty($cuser) && $cuser['Role']['is_admin']) ||  $uid == $topic['Topic']['user_id'] ) {
            $canComment = true;
        }
        else {
            $canComment = false;
        }
    }
    else {
        $canComment = true;
    }
    
    $topicArray = array(
        'id' => $topic['Topic']['id'],
        'title' => h($topic['Topic']['title']),
        'body' => $this->Text->convert_clickable_links_for_hashtags( $topic['Topic']['body'] , Configure::read('Topic.topic_hashtag_enabled')),
        'thumbnail' => $imageArray,
        'categoryId' => $topic['Topic']['category_id'],
        'commentCount' => $topic['Topic']['comment_count'],
        'shareCount' => $topic['Topic']['share_count'],
        'likeCount' => $topic['Topic']['like_count'],
        'dislikeCount' => $topic['Topic']['dislike_count'],
        'userId' => $topic['Topic']['user_id'],
        'userName' => $topic['User']['name'],
        'url' => FULL_BASE_URL .  str_replace('?','',mb_convert_encoding($topic['Topic']['moo_href'], 'UTF-8', 'UTF-8')),
        'published' => h($topic['Topic']['created']),
        'publishedTranslated' => $this->Moo->getTime($topic['Topic']['created'], Configure::read('core.date_format'), $utz),
        'userUrl' => FULL_BASE_URL . $topic['User']['moo_href'],
        'isViewerLiked' => $isViewerLiked ,
        'isViewerDisliked' => $isViewerDisliked ,
        'shareAction' => "topic_item_detail",
        'type' => $topic['Topic']['moo_type'],
        'allowAction' => $canComment,
        'groupName' => (isset($group) && $group) ? $group['Group']['moo_title'] : '',
        'groupId' => (isset($group) && $group) ? $group['Group']['id'] : '',
        'groupUrl' => (isset($group) && $group) ? FULL_BASE_URL . str_replace('?','',mb_convert_encoding($group['Group']['moo_href'], 'UTF-8', 'UTF-8')) : '',
    );

    $afterRenderApiTopic = new CakeEvent("Api.View.ApiTopic.afterRenderApiTopicDetail", $this, array(
        'item_id' => $topic['Topic']['id'],
        'item_type' => $topicArray["type"],
        'data' => $topic,
        'data_json' => $topicArray,
    ));

    $this->getEventManager()->dispatch($afterRenderApiTopic);
    if(!empty($afterRenderApiTopic->result['result'])) {
        $result = $afterRenderApiTopic->result['result'];
        $topicArray = array_merge($topicArray, $result);
    }

    echo json_encode($topicArray);
}