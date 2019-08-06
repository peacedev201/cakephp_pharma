<?php
$topicHelper = MooCore::getInstance()->getHelper('Topic_Topic');
$photoSizes = explode('|', Configure::read('core.photo_image_sizes'));
if (empty($topics)) {
    throw new ApiNotFoundException(__d('api', 'Topic not found'));
}
else {
    foreach ($topics as $topic):
        $imageArray = array();
        foreach ($photoSizes as $size) {
            $imageArray[$size] = $topicHelper->getImage($topic, array('prefix' => $size));
        }
        $topicArrayTmp = array(
            'id' => $topic['Topic']['id'],
            'title' => h($topic['Topic']['title']),
            'body' => $this->Text->convert_clickable_links_for_hashtags($this->Text->truncate(strip_tags(str_replace(array('<br>','&nbsp;'), array(' ',''), $topic['Topic']['body'])), 200, array('eclipse' => '')), Configure::read('Topic.topic_hashtag_enabled')),
            'thumbnail' => $imageArray,
            'categoryId' => $topic['Topic']['category_id'],
            'commentCount' => $topic['Topic']['comment_count'],
            'shareCount' => $topic['Topic']['share_count'],
            'likeCount' => $topic['Topic']['like_count'],
            'dislikeCount' => $topic['Topic']['dislike_count'],
            'userId' => $topic['Topic']['user_id'],
            'userName' => $topic['User']['name'],
            'url' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($topic['Topic']['moo_href'], 'UTF-8', 'UTF-8')),
            'created' => $this->Moo->getTime($topic['Topic']['created'], Configure::read('core.date_format'), $utz),
        );
        $afterRenderApiTopic = new CakeEvent("Api.View.ApiTopic.afterRenderApiTopicList", $this, array(
            'item_id' => $topic['Topic']['id'],
            'item_type' => $topic['Topic']['moo_type'],
            'data' => $topic,
            'data_json' => $topicArrayTmp,
        ));

        $this->getEventManager()->dispatch($afterRenderApiTopic);
        if(!empty($afterRenderApiTopic->result['result'])) {
            $result = $afterRenderApiTopic->result['result'];
            $topicArrayTmp = array_merge($topicArrayTmp, $result);
        }
        $topicArray[] = $topicArrayTmp;
    endforeach;
    echo json_encode($topicArray);
}